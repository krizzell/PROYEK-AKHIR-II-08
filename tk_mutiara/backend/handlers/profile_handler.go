package handlers

import (
	"database/sql"
	"fmt"
	"net/http"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"

	"github.com/gin-gonic/gin"
	"golang.org/x/crypto/bcrypt"
)

// GetProfileHandler handles GET /api/profile
func GetProfileHandler(c *gin.Context) {
	// Get user info dari JWT token (set by middleware)
	userID, exists := c.Get("user_id")
	if !exists {
		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Error:   "User tidak ditemukan",
		})
		return
	}

	userIDInt := userID.(int)
	role, _ := c.Get("role")
	userRole := role.(string)

	// Query user data dari akun table
	var username, akunRole string
	var nomorIndukSiswa sql.NullString
	var idGuru sql.NullInt64

	query := `
		SELECT a.username, a.role, a.nomor_induk_siswa, a.id_guru
		FROM akun a
		WHERE a.id_akun = ?
	`

	err := config.DB.QueryRow(query, userIDInt).Scan(&username, &akunRole, &nomorIndukSiswa, &idGuru)
	if err != nil {
		if err == sql.ErrNoRows {
			c.JSON(http.StatusNotFound, models.ApiResponse{
				Success: false,
				Error:   "User tidak ditemukan",
			})
			return
		}
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   "Terjadi kesalahan server",
		})
		return
	}

	// Get detailed info based on role
	var profileData gin.H

	if userRole == "orangtua" {
		// Get siswa data
		var namaSiswa, namaOrtu, tglLahir, jenisKelamin, alamat string
		var idKelas int
		var namaKelas string

		querySiswa := `
			SELECT s.nama_siswa, s.nama_orgtua, s.tgl_lahir, s.jenis_kelamin, s.alamat, s.id_kelas, k.nama_kelas
			FROM siswa s
			JOIN kelas k ON s.id_kelas = k.id_kelas
			WHERE s.nomor_induk_siswa = ?
		`

		err := config.DB.QueryRow(querySiswa, nomorIndukSiswa.String).Scan(
			&namaSiswa, &namaOrtu, &tglLahir, &jenisKelamin, &alamat, &idKelas, &namaKelas,
		)

		if err != nil && err != sql.ErrNoRows {
			fmt.Println("Error fetching siswa data:", err)
		}

		profileData = gin.H{
			"id":                userIDInt,
			"username":          username,
			"role":              akunRole,
			"nomor_induk_siswa": nomorIndukSiswa.String,
			"nama_siswa":        namaSiswa,
			"nama_ortu":         namaOrtu,
			"tgl_lahir":         tglLahir,
			"jenis_kelamin":     jenisKelamin,
			"alamat":            alamat,
			"id_kelas":          idKelas,
			"nama_kelas":        namaKelas,
		}
	} else if userRole == "guru" {
		// Get guru data
		var namaGuru, noHp, email string

		queryGuru := `
			SELECT nama_guru, no_hp, email
			FROM guru
			WHERE id_guru = ?
		`

		err := config.DB.QueryRow(queryGuru, idGuru.Int64).Scan(&namaGuru, &noHp, &email)
		if err != nil && err != sql.ErrNoRows {
			fmt.Println("Error fetching guru data:", err)
		}

		profileData = gin.H{
			"id":        userIDInt,
			"username":  username,
			"role":      akunRole,
			"id_guru":   idGuru.Int64,
			"nama_guru": namaGuru,
			"no_hp":     noHp,
			"email":     email,
		}
	}

	c.JSON(http.StatusOK, gin.H{
		"success": true,
		"data":    profileData,
	})
}

// UpdatePasswordHandler handles PUT /api/profile/password
func UpdatePasswordHandler(c *gin.Context) {
	userID, exists := c.Get("user_id")
	if !exists {
		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Error:   "User tidak ditemukan",
		})
		return
	}

	var req struct {
		OldPassword string `json:"old_password" binding:"required"`
		NewPassword string `json:"new_password" binding:"required"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "Old password dan new password harus diisi",
		})
		return
	}

	if len(req.NewPassword) < 6 {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Error:   "Password baru minimal 6 karakter",
		})
		return
	}

	userIDInt := userID.(int)

	// Get current password hash
	var currentPasswordHash string
	query := `SELECT password FROM akun WHERE id_akun = ?`
	err := config.DB.QueryRow(query, userIDInt).Scan(&currentPasswordHash)
	if err != nil {
		if err == sql.ErrNoRows {
			c.JSON(http.StatusNotFound, models.ApiResponse{
				Success: false,
				Error:   "User tidak ditemukan",
			})
			return
		}
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   "Terjadi kesalahan server",
		})
		return
	}

	// Verify old password
	err = bcrypt.CompareHashAndPassword([]byte(currentPasswordHash), []byte(req.OldPassword))
	if err != nil {
		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Error:   "Password lama tidak sesuai",
		})
		return
	}

	// Hash new password
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(req.NewPassword), bcrypt.DefaultCost)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   "Gagal mengenkripsi password",
		})
		return
	}

	// Update password
	updateQuery := `UPDATE akun SET password = ? WHERE id_akun = ?`
	_, err = config.DB.Exec(updateQuery, string(hashedPassword), userIDInt)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Error:   "Gagal mengupdate password",
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Password berhasil diubah",
	})
}

// UpdateProfileHandler handles PUT /api/profile
func UpdateProfileHandler(c *gin.Context) {
	userID, exists := c.Get("user_id")
	if !exists {
		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Error:   "User tidak ditemukan",
		})
		return
	}

	role, _ := c.Get("role")
	userRole := role.(string)
	userIDInt := userID.(int)

	var err error

	if userRole == "orangtua" {
		var req struct {
			NamaOrtu string `json:"nama_ortu"`
			NoHP     string `json:"no_hp"`
			Alamat   string `json:"alamat"`
		}

		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, models.ApiResponse{
				Success: false,
				Error:   "Data tidak valid",
			})
			return
		}

		// Get nomor_induk_siswa from akun table
		var nomorIndukSiswa string
		query := `SELECT nomor_induk_siswa FROM akun WHERE id_akun = ?`
		err = config.DB.QueryRow(query, userIDInt).Scan(&nomorIndukSiswa)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal mengambil data siswa",
			})
			return
		}

		// Update siswa table
		updateQuery := `UPDATE siswa SET nama_orgtua = ?, alamat = ? WHERE nomor_induk_siswa = ?`
		_, err = config.DB.Exec(updateQuery, req.NamaOrtu, req.Alamat, nomorIndukSiswa)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal mengupdate profil",
			})
			return
		}

	} else if userRole == "guru" {
		var req struct {
			NamaGuru string `json:"nama_guru"`
			NoHP     string `json:"no_hp"`
			Email    string `json:"email"`
		}

		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, models.ApiResponse{
				Success: false,
				Error:   "Data tidak valid",
			})
			return
		}

		// Get id_guru from akun table
		var idGuru int64
		query := `SELECT id_guru FROM akun WHERE id_akun = ?`
		err = config.DB.QueryRow(query, userIDInt).Scan(&idGuru)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal mengambil data guru",
			})
			return
		}

		// Update guru table
		updateQuery := `UPDATE guru SET nama_guru = ?, no_hp = ?, email = ? WHERE id_guru = ?`
		_, err = config.DB.Exec(updateQuery, req.NamaGuru, req.NoHP, req.Email, idGuru)
		if err != nil {
			c.JSON(http.StatusInternalServerError, models.ApiResponse{
				Success: false,
				Error:   "Gagal mengupdate profil",
			})
			return
		}
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Profil berhasil diubah",
	})
}
