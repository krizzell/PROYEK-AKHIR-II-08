package handlers

import (
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/services"

	"github.com/gin-gonic/gin"
)

func getNomorIndukSiswa(c *gin.Context) (string, error) {
	value, exists := c.Get("nomor_induk_siswa")
	if !exists {
		return "", fmt.Errorf("nomor induk siswa tidak tersedia di token")
	}

	nomor := strings.TrimSpace(fmt.Sprintf("%v", value))
	if nomor == "" || nomor == "<nil>" {
		return "", fmt.Errorf("nomor induk siswa tidak valid")
	}
	return nomor, nil
}

// GetMyTagihanHandler mengambil daftar tagihan milik parent login.
func GetMyTagihanHandler(c *gin.Context) {
	nomorIndukSiswa, err := getNomorIndukSiswa(c)
	if err != nil {
		writeHandlerError(c, http.StatusUnauthorized, "Akses pembayaran membutuhkan akun orangtua", err)
		return
	}

	tagihan, err := services.GetTagihanBySiswa(config.DB, nomorIndukSiswa)
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

// CreateMidtransTransactionHandler membuat transaksi Midtrans untuk parent login.
func CreateMidtransTransactionHandler(c *gin.Context) {
	nomorIndukSiswa, err := getNomorIndukSiswa(c)
	if err != nil {
		writeHandlerError(c, http.StatusUnauthorized, "Akses pembayaran membutuhkan akun orangtua", err)
		return
	}

	var req models.CreateMidtransTransactionRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		writeHandlerError(c, http.StatusBadRequest, "Data request tidak valid", err)
		return
	}

	result, err := services.CreateMidtransTransaction(config.DB, nomorIndukSiswa, req.IDTagihan)
	if err != nil {
		statusCode := http.StatusBadRequest
		if strings.Contains(strings.ToLower(err.Error()), "midtrans") {
			statusCode = http.StatusBadGateway
		}
		writeHandlerError(c, statusCode, "Gagal membuat transaksi pembayaran", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Transaksi pembayaran berhasil dibuat",
		Data:    result,
	})
}

// GetPaymentStatusHandler endpoint polling status pembayaran parent login.
func GetPaymentStatusHandler(c *gin.Context) {
	nomorIndukSiswa, err := getNomorIndukSiswa(c)
	if err != nil {
		writeHandlerError(c, http.StatusUnauthorized, "Akses pembayaran membutuhkan akun orangtua", err)
		return
	}

	idTagihan, err := strconv.Atoi(c.Param("idTagihan"))
	if err != nil {
		writeHandlerError(c, http.StatusBadRequest, "ID tagihan tidak valid", err)
		return
	}

	result, err := services.GetPaymentStatusByTagihan(config.DB, nomorIndukSiswa, idTagihan)
	if err != nil {
		statusCode := http.StatusBadRequest
		if isNotFoundError(err) {
			statusCode = http.StatusNotFound
		}
		writeHandlerError(c, statusCode, "Gagal mengambil status pembayaran", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Status pembayaran berhasil diambil",
		Data:    result,
	})
}

// MidtransWebhookHandler menerima callback webhook Midtrans.
func MidtransWebhookHandler(c *gin.Context) {
	var payload models.MidtransNotification
	if err := c.ShouldBindJSON(&payload); err != nil {
		writeHandlerError(c, http.StatusBadRequest, "Payload webhook Midtrans tidak valid", err)
		return
	}

	if err := services.HandleMidtransWebhook(config.DB, &payload); err != nil {
		statusCode := http.StatusBadRequest
		if strings.Contains(strings.ToLower(err.Error()), "tidak ditemukan") {
			statusCode = http.StatusNotFound
		}
		writeHandlerError(c, statusCode, "Gagal memproses webhook Midtrans", err)
		return
	}

	c.JSON(http.StatusOK, models.ApiResponse{
		Success: true,
		Message: "Webhook Midtrans berhasil diproses",
	})
}
