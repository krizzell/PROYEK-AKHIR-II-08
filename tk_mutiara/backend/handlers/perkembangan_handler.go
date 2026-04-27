package handlers

import (
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"

	"github.com/gin-gonic/gin"
)

// GetPerkembanganHandler - Get perkembangan data dengan kategori details
func GetPerkembanganHandler(c *gin.Context) {
	// Get siswa nomor induk dari JWT token
	nomorIndukSiswa, exists := c.Get("nomor_induk_siswa")
	if !exists {
		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Message: "Unauthorized - nomor_induk_siswa not found in token",
		})
		return
	}

	nomorIndukStr := fmt.Sprintf("%v", nomorIndukSiswa)

	// Query perkembangan dengan SEMUA field dari database
	query := `
		SELECT 
			p.id_perkembangan,
			p.id_guru,
			p.nomor_induk_siswa,
			s.nama_siswa,
			COALESCE(g.nama_guru, '') as nama_guru,
			COALESCE(k.nama_kelas, '') as kelas,
			COALESCE(p.bulan, 0) as bulan,
			COALESCE(p.tahun, 0) as tahun,
			p.kategori,
			p.deskripsi,
			COALESCE(p.template_deskripsi, '') as template_deskripsi,
			COALESCE(p.status_utama, 'BSH') as status_utama,
			DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i:%s') as created_at,
			DATE_FORMAT(p.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
		FROM perkembangan p
		INNER JOIN siswa s ON p.nomor_induk_siswa = s.nomor_induk_siswa
		LEFT JOIN guru g ON p.id_guru = g.id_guru
		LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
		WHERE p.nomor_induk_siswa = ?
		ORDER BY p.id_perkembangan DESC
	`

	rows, err := config.DB.Query(query, nomorIndukStr)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal mengambil data perkembangan",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}
	defer rows.Close()

	var perkembanganList []models.Perkembangan

	for rows.Next() {
		var p models.Perkembangan

		err := rows.Scan(
			&p.IDPerkembangan, &p.IDGuru, &p.NomorIndukSiswa, &p.NamaAnak, &p.NamaGuru, &p.Kelas,
			&p.Bulan, &p.Tahun, &p.Kategori, &p.Deskripsi, &p.TemplateDeskripsi, &p.StatusUtama, &p.CreatedAt, &p.UpdatedAt,
		)
		if err != nil {
			continue
		}

		// Query kategori details untuk perkembangan ini
		kategoriQuery := `
			SELECT 
				id_perkembangan_kategori,
				id_perkembangan,
				nama_kategori,
				nilai,
				status_utama,
				deskripsi,
				DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at,
				DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
			FROM perkembangan_kategori
			WHERE id_perkembangan = ?
			ORDER BY FIELD(nama_kategori, 'Akademik', 'Sosial', 'Emosional')
		`

		kategoriRows, err := config.DB.Query(kategoriQuery, p.IDPerkembangan)
		if err != nil {
			continue
		}

		var kategoriDetails []models.PerkembanganKategori
		for kategoriRows.Next() {
			var pk models.PerkembanganKategori
			if err := kategoriRows.Scan(&pk.IDCategori, &pk.IDPerkembangan, &pk.NamaKategori, &pk.Nilai, &pk.StatusUtama, &pk.Deskripsi, &pk.CreatedAt, &pk.UpdatedAt); err != nil {
				continue
			}
			kategoriDetails = append(kategoriDetails, pk)
		}
		kategoriRows.Close()

		p.KategoriDetails = kategoriDetails

		perkembanganList = append(perkembanganList, p)
	}

	if err := rows.Err(); err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Terjadi kesalahan saat membaca data perkembangan",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data perkembangan berhasil diambil",
		Data:    perkembanganList,
	})
}

// GetPerkembanganByIDHandler - Get detail perkembangan by ID
func GetPerkembanganByIDHandler(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Message: "ID tidak valid",
			Errors:  gin.H{"id": err.Error()},
		})
		return
	}

	query := `
		SELECT 
			p.id_perkembangan,
			p.id_guru,
			p.nomor_induk_siswa,
			s.nama_siswa,
			COALESCE(g.nama_guru, '') as nama_guru,
			COALESCE(k.nama_kelas, '') as kelas,
			COALESCE(p.bulan, 0) as bulan,
			COALESCE(p.tahun, 0) as tahun,
			p.kategori,
			p.deskripsi,		COALESCE(p.template_deskripsi, '') as template_deskripsi,			COALESCE(p.status_utama, 'BSH') as status_utama,
			DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i:%s') as created_at,
			DATE_FORMAT(p.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
		FROM perkembangan p
		INNER JOIN siswa s ON p.nomor_induk_siswa = s.nomor_induk_siswa
		LEFT JOIN guru g ON p.id_guru = g.id_guru
		LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
		WHERE p.id_perkembangan = ?
	`

	var p models.Perkembangan
	err = config.DB.QueryRow(query, id).Scan(
		&p.IDPerkembangan, &p.IDGuru, &p.NomorIndukSiswa, &p.NamaAnak, &p.NamaGuru, &p.Kelas,
		&p.Bulan, &p.Tahun, &p.Kategori, &p.Deskripsi, &p.TemplateDeskripsi, &p.StatusUtama, &p.CreatedAt, &p.UpdatedAt,
	)
	if err != nil {
		statusCode := http.StatusInternalServerError
		message := "Gagal mengambil perkembangan"
		if strings.Contains(strings.ToLower(err.Error()), "no rows") {
			statusCode = http.StatusNotFound
			message = "Perkembangan tidak ditemukan"
		}
		c.JSON(statusCode, models.ApiResponse{
			Success: false,
			Message: message,
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	// Query kategori details
	kategoriQuery := `
		SELECT 
			id_perkembangan_kategori,
			id_perkembangan,
			nama_kategori,
			nilai,
			status_utama,
			deskripsi,
			DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at,
			DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
		FROM perkembangan_kategori
		WHERE id_perkembangan = ?
		ORDER BY FIELD(nama_kategori, 'Akademik', 'Sosial', 'Emosional')
	`

	kategoriRows, err := config.DB.Query(kategoriQuery, p.IDPerkembangan)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Error fetching kategori",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}
	defer kategoriRows.Close()

	var kategoriDetails []models.PerkembanganKategori
	for kategoriRows.Next() {
		var pk models.PerkembanganKategori
		if err := kategoriRows.Scan(&pk.IDCategori, &pk.IDPerkembangan, &pk.NamaKategori, &pk.Nilai, &pk.StatusUtama, &pk.Deskripsi, &pk.CreatedAt, &pk.UpdatedAt); err != nil {
			continue
		}
		kategoriDetails = append(kategoriDetails, pk)
	}

	p.KategoriDetails = kategoriDetails

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Detail perkembangan berhasil diambil",
		Data:    p,
	})
}
