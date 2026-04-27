package handlers

import (
	"net/http"
	"strconv"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"

	"github.com/gin-gonic/gin"
)

// GetPengumumanHandler mengambil semua pengumuman
func GetPengumumanHandler(c *gin.Context) {
	pengumuman, err := repository.GetAllPengumuman(config.DB)
	if err != nil {
		c.JSON(http.StatusInternalServerError, models.ApiResponse{
			Success: false,
			Message: "Gagal mengambil data pengumuman",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Data pengumuman berhasil diambil",
		Data:    pengumuman,
	})
}

// GetPengumumanByIDHandler mengambil pengumuman berdasarkan ID
func GetPengumumanByIDHandler(c *gin.Context) {
	idStr := c.Param("id")

	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		c.JSON(http.StatusBadRequest, models.ApiResponse{
			Success: false,
			Message: "ID pengumuman tidak valid",
			Errors:  gin.H{"id": err.Error()},
		})
		return
	}

	pengumuman, err := repository.GetPengumumanByID(config.DB, id)
	if err != nil {
		c.JSON(http.StatusNotFound, models.ApiResponse{
			Success: false,
			Message: "Pengumuman tidak ditemukan",
			Errors:  gin.H{"detail": err.Error()},
		})
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Detail pengumuman berhasil diambil",
		Data:    pengumuman,
	})
}
