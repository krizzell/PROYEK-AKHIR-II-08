package handlers

import (
	"net/http"
	"strconv"
	"strings"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/services"

	"github.com/gin-gonic/gin"
)

func writeHandlerError(c *gin.Context, statusCode int, message string, err error) {
	response := models.ApiResponse{
		Success: false,
		Message: message,
	}
	if err != nil {
		response.Errors = gin.H{"detail": err.Error()}
	}
	c.JSON(statusCode, response)
}

func isNotFoundError(err error) bool {
	if err == nil {
		return false
	}
	message := strings.ToLower(err.Error())
	return strings.Contains(message, "tidak ditemukan") || strings.Contains(message, "not found")
}

// ==============================
// SISWA HANDLERS
// ==============================

// GetAllSiswa handler untuk GET /api/admin/siswa
func GetAllSiswa(c *gin.Context) {
	siswa, err := services.GetAllSiswa(config.DB)
	if err != nil {
		writeHandlerError(c, http.StatusInternalServerError, "Gagal mengambil data siswa", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data siswa berhasil diambil",
		Data:    siswa,
	})
}

// GetSiswaByID handler untuk GET /api/admin/siswa/:id
func GetSiswaByID(c *gin.Context) {
	nomorIndukSiswa := c.Param("id")
	if nomorIndukSiswa == "" {
		writeHandlerError(c, http.StatusBadRequest, "Nomor induk siswa tidak valid", nil)
		return
	}

	siswa, err := services.GetSiswaDetail(config.DB, nomorIndukSiswa)
	if err != nil {
		statusCode := http.StatusInternalServerError
		message := "Gagal mengambil data siswa"
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
			message = "Siswa tidak ditemukan"
		}
		writeHandlerError(c, statusCode, message, err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data siswa berhasil diambil",
		Data:    siswa,
	})
}

// GetSiswaByKelas handler untuk GET /api/admin/kelas/:id/siswa
func GetSiswaByKelas(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeHandlerError(c, http.StatusBadRequest, "ID kelas tidak valid", err)
		return
	}

	siswa, err := services.GetSiswaByKelas(config.DB, id)
	if err != nil {
		writeHandlerError(c, http.StatusInternalServerError, "Gagal mengambil data siswa per kelas", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data siswa berhasil diambil",
		Data:    siswa,
	})
}

// CreateSiswa handler untuk POST /api/admin/siswa
func CreateSiswa(c *gin.Context) {
	var req models.CreateSiswaRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		writeHandlerError(c, http.StatusBadRequest, "Data siswa tidak valid", err)
		return
	}

	id, err := services.CreateNewSiswa(config.DB, &req)
	if err != nil {
		statusCode := http.StatusBadRequest
		message := "Gagal membuat siswa"
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
			message = "Data referensi tidak ditemukan"
		}
		writeHandlerError(c, statusCode, message, err)
		return
	}

	c.JSON(http.StatusCreated, models.ApiResponse{
		Success: true,
		Message: "Siswa berhasil dibuat",
		Data: map[string]interface{}{
			"nomor_induk_siswa": id,
		},
	})
}

// DeleteSiswa handler untuk DELETE /api/admin/siswa/:id
func DeleteSiswa(c *gin.Context) {
	nomorIndukSiswa := c.Param("id")
	if nomorIndukSiswa == "" {
		writeHandlerError(c, http.StatusBadRequest, "Nomor induk siswa tidak valid", nil)
		return
	}

	err := services.RemoveSiswa(config.DB, nomorIndukSiswa)
	if err != nil {
		statusCode := http.StatusInternalServerError
		message := "Gagal menghapus siswa"
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
			message = "Siswa tidak ditemukan"
		}
		writeHandlerError(c, statusCode, message, err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Siswa berhasil dihapus",
	})
}

// ==============================
// TAGIHAN HANDLERS
// ==============================

// GetAllTagihan handler untuk GET /api/admin/tagihan
func GetAllTagihan(c *gin.Context) {
	tagihan, err := services.GetAllTagihan(config.DB)
	if err != nil {
		writeHandlerError(c, http.StatusInternalServerError, "Gagal mengambil data tagihan", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data tagihan berhasil diambil",
		Data:    tagihan,
	})
}

// GetTagihanByID handler untuk GET /api/admin/tagihan/:id
func GetTagihanByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeHandlerError(c, http.StatusBadRequest, "ID tagihan tidak valid", err)
		return
	}

	tagihan, err := services.GetTagihanDetail(config.DB, id)
	if err != nil {
		statusCode := http.StatusInternalServerError
		message := "Gagal mengambil data tagihan"
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
			message = "Tagihan tidak ditemukan"
		}
		writeHandlerError(c, statusCode, message, err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data tagihan berhasil diambil",
		Data:    tagihan,
	})
}

// GetTagihanBySiswa handler untuk GET /api/admin/siswa/:id/tagihan
func GetTagihanBySiswa(c *gin.Context) {
	nomorIndukSiswa := c.Param("id")
	if nomorIndukSiswa == "" {
		writeHandlerError(c, http.StatusBadRequest, "Nomor induk siswa tidak valid", nil)
		return
	}

	tagihan, err := services.GetTagihanBySiswa(config.DB, nomorIndukSiswa)
	if err != nil {
		writeHandlerError(c, http.StatusInternalServerError, "Gagal mengambil data tagihan siswa", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data tagihan berhasil diambil",
		Data:    tagihan,
	})
}

// CreateTagihan handler untuk POST /api/admin/tagihan
func CreateTagihan(c *gin.Context) {
	var req models.CreateTagihanRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		writeHandlerError(c, http.StatusBadRequest, "Data tagihan tidak valid", err)
		return
	}

	id, err := services.CreateNewTagihan(config.DB, &req)
	if err != nil {
		statusCode := http.StatusBadRequest
		message := "Gagal membuat tagihan"
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
			message = "Siswa tidak ditemukan"
		}
		writeHandlerError(c, statusCode, message, err)
		return
	}

	c.JSON(http.StatusCreated, models.ApiResponse{
		Success: true,
		Message: "Tagihan berhasil dibuat",
		Data: map[string]interface{}{
			"id_tagihan": id,
		},
	})
}

// DeleteTagihan handler untuk DELETE /api/admin/tagihan/:id
func DeleteTagihan(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeHandlerError(c, http.StatusBadRequest, "ID tagihan tidak valid", err)
		return
	}

	err = services.RemoveTagihan(config.DB, id)
	if err != nil {
		statusCode := http.StatusInternalServerError
		message := "Gagal menghapus tagihan"
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
			message = "Tagihan tidak ditemukan"
		}
		writeHandlerError(c, statusCode, message, err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Tagihan berhasil dihapus",
	})
}

// ==============================
// PEMBAYARAN HANDLERS
// ==============================

// GetAllPembayaran handler untuk GET /api/admin/pembayaran
func GetAllPembayaran(c *gin.Context) {
	pembayaran, err := services.GetAllPembayaran(config.DB)
	if err != nil {
		writeHandlerError(c, http.StatusInternalServerError, "Gagal mengambil data pembayaran", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data pembayaran berhasil diambil",
		Data:    pembayaran,
	})
}

// GetPembayaranByID handler untuk GET /api/admin/pembayaran/:id
func GetPembayaranByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeHandlerError(c, http.StatusBadRequest, "ID pembayaran tidak valid", err)
		return
	}

	pembayaran, err := services.GetPembayaranDetail(config.DB, id)
	if err != nil {
		statusCode := http.StatusInternalServerError
		message := "Gagal mengambil data pembayaran"
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
			message = "Pembayaran tidak ditemukan"
		}
		writeHandlerError(c, statusCode, message, err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data pembayaran berhasil diambil",
		Data:    pembayaran,
	})
}

// GetPembayaranByTagihan handler untuk GET /api/admin/tagihan/:id/pembayaran
func GetPembayaranByTagihan(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeHandlerError(c, http.StatusBadRequest, "ID tagihan tidak valid", err)
		return
	}

	pembayaran, err := services.GetPembayaranByTagihan(config.DB, id)
	if err != nil {
		writeHandlerError(c, http.StatusInternalServerError, "Gagal mengambil pembayaran berdasarkan tagihan", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data pembayaran berhasil diambil",
		Data:    pembayaran,
	})
}

// UpdatePembayaranStatus handler untuk PUT /api/admin/pembayaran/:id/status
func UpdatePembayaranStatus(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeHandlerError(c, http.StatusBadRequest, "ID pembayaran tidak valid", err)
		return
	}

	var req models.UpdatePembayaranRequest
	req.IDPembayaran = id

	if err := c.ShouldBindJSON(&req); err != nil {
		writeHandlerError(c, http.StatusBadRequest, "Data pembayaran tidak valid", err)
		return
	}

	err = services.UpdatePembayaranStatusService(config.DB, &req)
	if err != nil {
		statusCode := http.StatusBadRequest
		message := "Gagal mengupdate status pembayaran"
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
			message = "Pembayaran tidak ditemukan"
		}
		writeHandlerError(c, statusCode, message, err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Status pembayaran berhasil diupdate",
	})
}
