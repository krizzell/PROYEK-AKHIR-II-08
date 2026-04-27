package routes

import (
	"tk_mutiara_backend/handlers"
	"tk_mutiara_backend/middleware"

	"github.com/gin-gonic/gin"
)

// SetupAdminRoutes mengatur routes untuk Dashboard Admin API
func SetupAdminRoutes(r *gin.Engine) {
	// Group routes dengan prefix /api/admin
	admin := r.Group("/api/admin")
	admin.Use(middleware.AuthMiddleware())
	admin.Use(middleware.AdminRoleMiddleware())
	{
		// ==============================
		// DASHBOARD ROUTES
		// ==============================
		dashboard := admin.Group("/dashboard")
		{
			dashboard.GET("/metrics", handlers.GetDashboardMetrics)
			dashboard.GET("/statistics", handlers.GetDashboardStatistics)
		}

		// ==============================
		// GURU ROUTES
		// ==============================
		guru := admin.Group("/guru")
		{
			guru.GET("", handlers.GetAllGuru)        // GET /api/admin/guru
			guru.POST("", handlers.CreateGuru)       // POST /api/admin/guru
			guru.GET("/:id", handlers.GetGuruByID)   // GET /api/admin/guru/:id
			guru.PUT("/:id", handlers.UpdateGuru)    // PUT /api/admin/guru/:id
			guru.DELETE("/:id", handlers.DeleteGuru) // DELETE /api/admin/guru/:id
		}

		// ==============================
		// KELAS ROUTES
		// ==============================
		kelas := admin.Group("/kelas")
		{
			kelas.GET("", handlers.GetAllKelas)               // GET /api/admin/kelas
			kelas.POST("", handlers.CreateKelas)              // POST /api/admin/kelas
			kelas.GET("/:id", handlers.GetKelasByID)          // GET /api/admin/kelas/:id
			kelas.DELETE("/:id", handlers.DeleteKelas)        // DELETE /api/admin/kelas/:id
			kelas.GET("/:id/siswa", handlers.GetSiswaByKelas) // GET /api/admin/kelas/:id/siswa
		}

		// ==============================
		// SISWA ROUTES
		// ==============================
		siswa := admin.Group("/siswa")
		{
			siswa.GET("", handlers.GetAllSiswa)                   // GET /api/admin/siswa
			siswa.POST("", handlers.CreateSiswa)                  // POST /api/admin/siswa
			siswa.GET("/:id", handlers.GetSiswaByID)              // GET /api/admin/siswa/:id
			siswa.DELETE("/:id", handlers.DeleteSiswa)            // DELETE /api/admin/siswa/:id
			siswa.GET("/:id/tagihan", handlers.GetTagihanBySiswa) // GET /api/admin/siswa/:id/tagihan
		}

		// ==============================
		// TAGIHAN ROUTES
		// ==============================
		tagihan := admin.Group("/tagihan")
		{
			tagihan.GET("", handlers.GetAllTagihan)                         // GET /api/admin/tagihan
			tagihan.POST("", handlers.CreateTagihan)                        // POST /api/admin/tagihan
			tagihan.GET("/:id", handlers.GetTagihanByID)                    // GET /api/admin/tagihan/:id
			tagihan.DELETE("/:id", handlers.DeleteTagihan)                  // DELETE /api/admin/tagihan/:id
			tagihan.GET("/:id/pembayaran", handlers.GetPembayaranByTagihan) // GET /api/admin/tagihan/:id/pembayaran
		}

		// ==============================
		// PEMBAYARAN ROUTES
		// ==============================
		pembayaran := admin.Group("/pembayaran")
		{
			pembayaran.GET("", handlers.GetAllPembayaran)                  // GET /api/admin/pembayaran
			pembayaran.GET("/:id", handlers.GetPembayaranByID)             // GET /api/admin/pembayaran/:id
			pembayaran.PUT("/:id/status", handlers.UpdatePembayaranStatus) // PUT /api/admin/pembayaran/:id/status
		}
	}
}
