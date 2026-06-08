package handlers

import (
	"database/sql"
	"net/http"
	"time"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"

	"github.com/gin-gonic/gin"
	"github.com/golang-jwt/jwt/v5"
	"golang.org/x/crypto/bcrypt"
)

func writeAuthError(c *gin.Context, statusCode int, message string, err error) {
	response := models.ApiResponse{Success: false, Message: message}
	if err != nil {
		response.Errors = gin.H{"detail": err.Error()}
	}
	c.JSON(statusCode, response)
}

// LoginHandler handles user login
func LoginHandler(c *gin.Context) {
	var req struct {
		Email    string `json:"email" binding:"required"`
		Password string `json:"password" binding:"required"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Message: "Username dan password harus diisi",
			Errors:  gin.H{"username": "required", "password": "required"},
		})
		return
	}

	// Query database untuk cek user
	var userID int
	var userName string
	var userRole string
	var password string
	var nomorIndukSiswa sql.NullString
	var idGuru sql.NullInt64

	query := `
		SELECT a.id_akun, a.username, a.role, a.password, a.nomor_induk_siswa, a.id_guru
		FROM akun a
		WHERE a.username = ?
	`

	err := config.DB.QueryRow(query, req.Email).Scan(&userID, &userName, &userRole, &password, &nomorIndukSiswa, &idGuru)
	if err != nil {
		if err == sql.ErrNoRows {
			writeAuthError(c, http.StatusUnauthorized, "Username atau password salah", nil)
			return
		}
		writeAuthError(c, http.StatusInternalServerError, "Terjadi kesalahan server", err)
		return
	}

	// Verify password menggunakan bcrypt
	err = bcrypt.CompareHashAndPassword([]byte(password), []byte(req.Password))
	if err != nil {
		writeAuthError(c, http.StatusUnauthorized, "Username atau password salah", nil)
		return
	}

	// Generate JWT token
	token := jwt.NewWithClaims(jwt.SigningMethodHS256, jwt.MapClaims{
		"user_id":           userID,
		"username":          userName,
		"role":              userRole,
		"nomor_induk_siswa": nomorIndukSiswa.String,
		"id_guru":           idGuru.Int64,
		"exp":               time.Now().Add(time.Hour * 24).Unix(),
	})

	tokenString, err := token.SignedString([]byte(config.AppConfig.JWTSecret))
	if err != nil {
		writeAuthError(c, http.StatusInternalServerError, "Gagal membuat token", err)
		return
	}

	// Get user details
	var userData models.UserLogin
	if userRole == "orangtua" {
		// Get siswa data
		queryChild := `
			SELECT s.nomor_induk_siswa, s.nama_siswa, s.nama_orgtua, k.nama_kelas, g.nama_guru
			FROM siswa s
			JOIN kelas k ON s.id_kelas = k.id_kelas
			LEFT JOIN guru g ON k.id_guru = g.id_guru
			WHERE s.nomor_induk_siswa = ?
		`
		var namaGuru sql.NullString
		err := config.DB.QueryRow(queryChild, nomorIndukSiswa.String).Scan(
			&userData.NomorIndukSiswa, &userData.NamaSiswa, &userData.NamaOrtu, &userData.Kelas, &namaGuru,
		)
		if namaGuru.Valid {
			userData.NamaGuru = namaGuru.String
		}
		if err != nil {
			// Optional profile enrichment failure should not block login.
		}

	} else if userRole == "guru" {
		// Get guru data
		queryGuru := `
			SELECT id_guru, nama_guru, email
			FROM guru
			WHERE id_guru = ?
		`
		var email string
		err := config.DB.QueryRow(queryGuru, idGuru.Int64).Scan(
			&userData.IDGuru, &userData.NamaGuru, &email,
		)
		if err != nil {
			// Optional profile enrichment failure should not block login.
		}
	}

	userPayload := gin.H{
		"id":                userID,
		"username":          userName,
		"role":              userRole,
		"nomor_induk_siswa": nomorIndukSiswa.String,
		"nama_siswa":        userData.NamaSiswa,
		"nama_ortu":         userData.NamaOrtu,
		"kelas":             userData.Kelas,
		"id_guru":           idGuru.Int64,
		"nama_guru":         userData.NamaGuru,
	}

	c.JSON(http.StatusOK, gin.H{
		"success": true,
		"message": "Login berhasil",
		"data": gin.H{
			"token": tokenString,
			"user":  userPayload,
		},
		// Backward compatibility for existing Flutter parser.
		"token": tokenString,
		"user":  userPayload,
	})
}

// ChangePasswordHandler handles password updates
func ChangePasswordHandler(c *gin.Context) {
	var req struct {
		Username                string `json:"username" binding:"required"`
		OldPassword             string `json:"old_password" binding:"required"`
		NewPassword             string `json:"new_password" binding:"required"`
		NewPasswordConfirmation string `json:"new_password_confirmation" binding:"required"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Message: "Semua data harus diisi dengan benar",
		})
		return
	}

	if req.NewPassword != req.NewPasswordConfirmation {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Message: "Konfirmasi password tidak cocok",
		})
		return
	}

	if len(req.NewPassword) < 8 {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Message: "Password baru minimal 8 karakter",
		})
		return
	}

	// Cek user dan password lama
	var passwordHash string
	var userID int
	query := "SELECT id_akun, password FROM akun WHERE username = ?"
	err := config.DB.QueryRow(query, req.Username).Scan(&userID, &passwordHash)
	if err != nil {
		if err == sql.ErrNoRows {
			c.JSON(http.StatusUnauthorized, models.ApiResponse{
				Success: false,
				Message: "Username atau password lama salah",
			})
			return
		}
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Terjadi kesalahan server",
		})
		return
	}

	// Verify old password
	err = bcrypt.CompareHashAndPassword([]byte(passwordHash), []byte(req.OldPassword))
	if err != nil {
		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Message: "Username atau password lama salah",
		})
		return
	}

	// Hash new password
	newHash, err := bcrypt.GenerateFromPassword([]byte(req.NewPassword), bcrypt.DefaultCost)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal memproses password baru",
		})
		return
	}

	// Update password
	updateQuery := "UPDATE akun SET password = ? WHERE id_akun = ?"
	_, err = config.DB.Exec(updateQuery, string(newHash), userID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal memperbarui password di database",
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Password berhasil diubah",
	})
}
