package handlers

import (
	"database/sql"
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"

	"github.com/gin-gonic/gin"
	"golang.org/x/crypto/bcrypt"
)

func toInt(value interface{}) (int, error) {
	switch v := value.(type) {
	case int:
		return v, nil
	case int32:
		return int(v), nil
	case int64:
		return int(v), nil
	case float64:
		return int(v), nil
	case string:
		i, err := strconv.Atoi(strings.TrimSpace(v))
		if err != nil {
			return 0, err
		}
		return i, nil
	default:
		return 0, fmt.Errorf("unsupported numeric type")
	}
}

func toString(value interface{}) string {
	if value == nil {
		return ""
	}
	return strings.TrimSpace(fmt.Sprintf("%v", value))
}

func writeProfileError(c *gin.Context, statusCode int, message string, err error) {
	response := models.ApiResponse{Success: false, Message: message}
	if err != nil {
		response.Errors = gin.H{"detail": err.Error()}
	}
	c.JSON(statusCode, response)
}

// GetProfileHandler handles GET /api/profile
func GetProfileHandler(c *gin.Context) {
	// Get user info dari JWT token (set by middleware)
	userID, exists := c.Get("user_id")
	if !exists {
		writeProfileError(c, http.StatusUnauthorized, "User tidak ditemukan", nil)
		return
	}

	userIDInt, err := toInt(userID)
	if err != nil || userIDInt <= 0 {
		writeProfileError(c, http.StatusUnauthorized, "User tidak valid", nil)
		return
	}

	role, _ := c.Get("role")
	userRole := toString(role)

	// Query user data dari akun table
	var username, akunRole string
	var nomorIndukSiswa sql.NullString
	var idGuru sql.NullInt64

	query := `
		SELECT a.username, a.role, a.nomor_induk_siswa, a.id_guru
		FROM akun a
		WHERE a.id_akun = ?
	`

	err = config.DB.QueryRow(query, userIDInt).Scan(&username, &akunRole, &nomorIndukSiswa, &idGuru)
	if err != nil {
		if err == sql.ErrNoRows {
			writeProfileError(c, http.StatusNotFound, "User tidak ditemukan", nil)
			return
		}
		writeProfileError(c, http.StatusInternalServerError, "Terjadi kesalahan server", err)
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
			writeProfileError(c, http.StatusInternalServerError, "Gagal mengambil data siswa", err)
			return
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
			writeProfileError(c, http.StatusInternalServerError, "Gagal mengambil data guru", err)
			return
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

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Profil berhasil diambil",
		Data:    profileData,
	})
}

// UpdatePasswordHandler handles PUT /api/profile/password
func UpdatePasswordHandler(c *gin.Context) {
	userID, exists := c.Get("user_id")
	if !exists {
		writeProfileError(c, http.StatusUnauthorized, "User tidak ditemukan", nil)
		return
	}

	var req struct {
		OldPassword string `json:"old_password" binding:"required"`
		NewPassword string `json:"new_password" binding:"required"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		writeProfileError(c, http.StatusBadRequest, "Old password dan new password harus diisi", err)
		return
	}

	if len(req.NewPassword) < 6 {
		writeProfileError(c, http.StatusBadRequest, "Password baru minimal 6 karakter", nil)
		return
	}

	userIDInt, err := toInt(userID)
	if err != nil || userIDInt <= 0 {
		writeProfileError(c, http.StatusUnauthorized, "User tidak valid", nil)
		return
	}

	// Get current password hash
	var currentPasswordHash string
	query := `SELECT password FROM akun WHERE id_akun = ?`
	err = config.DB.QueryRow(query, userIDInt).Scan(&currentPasswordHash)
	if err != nil {
		if err == sql.ErrNoRows {
			writeProfileError(c, http.StatusNotFound, "User tidak ditemukan", nil)
			return
		}
		writeProfileError(c, http.StatusInternalServerError, "Terjadi kesalahan server", err)
		return
	}

	// Verify old password
	err = bcrypt.CompareHashAndPassword([]byte(currentPasswordHash), []byte(req.OldPassword))
	if err != nil {
		writeProfileError(c, http.StatusUnauthorized, "Password lama tidak sesuai", nil)
		return
	}

	// Hash new password
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(req.NewPassword), bcrypt.DefaultCost)
	if err != nil {
		writeProfileError(c, http.StatusInternalServerError, "Gagal mengenkripsi password", err)
		return
	}

	// Update password
	updateQuery := `UPDATE akun SET password = ? WHERE id_akun = ?`
	_, err = config.DB.Exec(updateQuery, string(hashedPassword), userIDInt)
	if err != nil {
		writeProfileError(c, http.StatusInternalServerError, "Gagal mengupdate password", err)
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
		writeProfileError(c, http.StatusUnauthorized, "User tidak ditemukan", nil)
		return
	}

	role, _ := c.Get("role")
	userRole := toString(role)
	userIDInt, err := toInt(userID)
	if err != nil || userIDInt <= 0 {
		writeProfileError(c, http.StatusUnauthorized, "User tidak valid", nil)
		return
	}

	if userRole == "orangtua" {
		var req struct {
			NamaOrtu string `json:"nama_ortu"`
			NoHP     string `json:"no_hp"`
			Alamat   string `json:"alamat"`
		}

		if err := c.ShouldBindJSON(&req); err != nil {
			writeProfileError(c, http.StatusBadRequest, "Data tidak valid", err)
			return
		}

		// Get nomor_induk_siswa from akun table
		var nomorIndukSiswa string
		query := `SELECT nomor_induk_siswa FROM akun WHERE id_akun = ?`
		err = config.DB.QueryRow(query, userIDInt).Scan(&nomorIndukSiswa)
		if err != nil {
			writeProfileError(c, http.StatusInternalServerError, "Gagal mengambil data siswa", err)
			return
		}

		// Update siswa table
		updateQuery := `UPDATE siswa SET nama_orgtua = ?, alamat = ? WHERE nomor_induk_siswa = ?`
		_, err = config.DB.Exec(updateQuery, req.NamaOrtu, req.Alamat, nomorIndukSiswa)
		if err != nil {
			writeProfileError(c, http.StatusInternalServerError, "Gagal mengupdate profil", err)
			return
		}

	} else if userRole == "guru" {
		var req struct {
			NamaGuru string `json:"nama_guru"`
			NoHP     string `json:"no_hp"`
			Email    string `json:"email"`
		}

		if err := c.ShouldBindJSON(&req); err != nil {
			writeProfileError(c, http.StatusBadRequest, "Data tidak valid", err)
			return
		}

		// Get id_guru from akun table
		var idGuru int64
		query := `SELECT id_guru FROM akun WHERE id_akun = ?`
		err = config.DB.QueryRow(query, userIDInt).Scan(&idGuru)
		if err != nil {
			writeProfileError(c, http.StatusInternalServerError, "Gagal mengambil data guru", err)
			return
		}

		// Update guru table
		updateQuery := `UPDATE guru SET nama_guru = ?, no_hp = ?, email = ? WHERE id_guru = ?`
		_, err = config.DB.Exec(updateQuery, req.NamaGuru, req.NoHP, req.Email, idGuru)
		if err != nil {
			writeProfileError(c, http.StatusInternalServerError, "Gagal mengupdate profil", err)
			return
		}
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Profil berhasil diubah",
	})
}
