package main

import (
	"log"
	"net/http"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/handlers"
	"tk_mutiara_backend/middleware"
	"tk_mutiara_backend/migrations"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/routes"

	"github.com/gin-gonic/gin"
)

func main() {
	// Load configuration
	if err := config.LoadConfig(); err != nil {
		log.Fatalf("Gagal load config: %v", err)
	}

	// Initialize database
	if err := config.InitDB(config.AppConfig); err != nil {
		log.Fatalf("Gagal init database: %v", err)
	}
	defer config.CloseDB()

	// Initialize schema
	if err := migrations.InitializeSchema(config.DB); err != nil {
		log.Fatalf("Gagal init schema: %v", err)
	}

	// Initialize Gin
	gin.SetMode(config.AppConfig.Environment)
	r := gin.Default()

	// Middleware
	r.Use(middleware.CORSMiddleware())

	// ==============================
	// PUBLIC ROUTES
	// ==============================
	r.GET("/", func(c *gin.Context) {
		c.JSON(http.StatusOK, models.ApiResponse{
			Success: true,
			Message: "Backend TK Mutiara jalan 🚀",
		})
	})

	// Login route - NEW HANDLER
	r.POST("/login", handlers.LoginHandler)

	// ==============================
	// PROTECTED ROUTES
	// ==============================
	protected := r.Group("/api")
	protected.Use(middleware.AuthMiddleware())
	{
		// Pengumuman routes
		protected.GET("/pengumuman", handlers.GetPengumumanHandler)
		protected.GET("/pengumuman/:id", handlers.GetPengumumanByIDHandler)

		// Perkembangan routes
		protected.GET("/perkembangan", handlers.GetPerkembanganHandler)
		protected.GET("/perkembangan/:id", handlers.GetPerkembanganByIDHandler)

		// Profile routes
		protected.GET("/profile", handlers.GetProfileHandler)
		protected.PUT("/profile", handlers.UpdateProfileHandler)
		protected.PUT("/profile/password", handlers.UpdatePasswordHandler)
	}

	// Admin routes (already implemented in handlers/services/repository)
	routes.SetupAdminRoutes(r)

	// Start server
	port := ":" + config.AppConfig.ServerPort
	log.Printf("Server berjalan di http://localhost%s\n", port)
	if err := r.Run(port); err != nil {
		log.Fatalf("Gagal menjalankan server: %v", err)
	}
}
