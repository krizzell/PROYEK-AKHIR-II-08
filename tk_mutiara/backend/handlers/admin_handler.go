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

func writeAdminError(c *gin.Context, statusCode int, message string, err error) {
	response := models.ApiResponse{Success: false, Message: message}
	if err != nil {
		response.Errors = gin.H{"detail": err.Error()}
	}
	c.JSON(statusCode, response)
}

// ==============================
// DASHBOARD HANDLERS
// ==============================

// GetDashboardMetrics handler untuk GET /api/admin/dashboard/metrics
func GetDashboardMetrics(c *gin.Context) {
	metrics, err := services.GetDashboardOverview(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal mengambil dashboard metrics",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Dashboard metrics berhasil diambil",
		Data:    metrics,
	})
}

// GetDashboardStatistics handler untuk GET /api/admin/dashboard/statistics
func GetDashboardStatistics(c *gin.Context) {
	limitStr := c.DefaultQuery("limit", "30")
	limit, err := strconv.Atoi(limitStr)
	if err != nil {
		limit = 30
	}

	statistics, err := services.GetDashboardStatistics(config.DB, limit)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal mengambil dashboard statistics",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Dashboard statistics berhasil diambil",
		Data:    statistics,
	})
}

// ==============================
// GURU HANDLERS
// ==============================

// GetAllGuru handler untuk GET /api/admin/guru
func GetAllGuru(c *gin.Context) {
	gurus, err := services.GetAllGurus(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal mengambil data guru",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data guru berhasil diambil",
		Data:    gurus,
	})
}

// GetGuruByID handler untuk GET /api/admin/guru/:id
func GetGuruByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeAdminError(c, http.StatusBadRequest, "ID guru tidak valid", err)
		return
	}

	guru, err := services.GetGuruDetail(config.DB, id)
	if err != nil {
		statusCode := http.StatusInternalServerError
		if strings.Contains(strings.ToLower(err.Error()), "tidak ditemukan") {
			statusCode = http.StatusNotFound
		}
		c.JSON(statusCode, models.ApiResponse{
			Success: false,
			Message: "Guru tidak ditemukan",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data guru berhasil diambil",
		Data:    guru,
	})
}

// CreateGuru handler untuk POST /api/admin/guru
func CreateGuru(c *gin.Context) {
	var req models.CreateGuruRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		writeAdminError(c, http.StatusBadRequest, "Data guru tidak valid", err)
		return
	}

	id, err := services.CreateNewGuru(config.DB, &req)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal membuat guru",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusCreated, models.ApiResponse{
		Success: true,
		Message: "Guru berhasil dibuat",
		Data: map[string]interface{}{
			"id_guru": id,
		},
	})
}

// UpdateGuru handler untuk PUT /api/admin/guru/:id
func UpdateGuru(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeAdminError(c, http.StatusBadRequest, "ID guru tidak valid", err)
		return
	}

	var req models.UpdateGuruRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Message: "Data guru tidak valid",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	err = services.UpdateGuruData(config.DB, id, &req)
	if err != nil {
		statusCode := http.StatusInternalServerError
		if strings.Contains(strings.ToLower(err.Error()), "tidak ditemukan") {
			statusCode = http.StatusNotFound
		}
		c.JSON(statusCode, models.ApiResponse{
			Success: false,
			Message: "Gagal mengupdate guru",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Guru berhasil diupdate",
	})
}

// DeleteGuru handler untuk DELETE /api/admin/guru/:id
func DeleteGuru(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeAdminError(c, http.StatusBadRequest, "ID guru tidak valid", err)
		return
	}

	err = services.RemoveGuru(config.DB, id)
	if err != nil {
		statusCode := http.StatusInternalServerError
		if strings.Contains(strings.ToLower(err.Error()), "tidak ditemukan") {
			statusCode = http.StatusNotFound
		}
		c.JSON(statusCode, models.ApiResponse{
			Success: false,
			Message: "Gagal menghapus guru",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Guru berhasil dihapus",
	})
}

// ==============================
// KELAS HANDLERS
// ==============================

// GetAllKelas handler untuk GET /api/admin/kelas
func GetAllKelas(c *gin.Context) {
	kelas, err := services.GetAllKelas(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal mengambil data kelas",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data kelas berhasil diambil",
		Data:    kelas,
	})
}

// GetKelasByID handler untuk GET /api/admin/kelas/:id
func GetKelasByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeAdminError(c, http.StatusBadRequest, "ID kelas tidak valid", err)
		return
	}

	kelas, err := services.GetKelasDetail(config.DB, id)
	if err != nil {
		statusCode := http.StatusInternalServerError
		if strings.Contains(strings.ToLower(err.Error()), "tidak ditemukan") {
			statusCode = http.StatusNotFound
		}
		c.JSON(statusCode, models.ApiResponse{
			Success: false,
			Message: "Kelas tidak ditemukan",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data kelas berhasil diambil",
		Data:    kelas,
	})
}

// CreateKelas handler untuk POST /api/admin/kelas
func CreateKelas(c *gin.Context) {
	var req models.CreateKelasRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		writeAdminError(c, http.StatusBadRequest, "Data kelas tidak valid", err)
		return
	}

	id, err := services.CreateNewKelas(config.DB, &req)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal membuat kelas",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusCreated, models.ApiResponse{
		Success: true,
		Message: "Kelas berhasil dibuat",
		Data: map[string]interface{}{
			"id_kelas": id,
		},
	})
}

// DeleteKelas handler untuk DELETE /api/admin/kelas/:id
func DeleteKelas(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.Atoi(idStr)
	if err != nil {
		writeAdminError(c, http.StatusBadRequest, "ID kelas tidak valid", err)
		return
	}

	err = services.RemoveKelas(config.DB, id)
	if err != nil {
		statusCode := http.StatusInternalServerError
		if strings.Contains(strings.ToLower(err.Error()), "tidak ditemukan") {
			statusCode = http.StatusNotFound
		}
		c.JSON(statusCode, models.ApiResponse{
			Success: false,
			Message: "Gagal menghapus kelas",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Kelas berhasil dihapus",
	})
}
