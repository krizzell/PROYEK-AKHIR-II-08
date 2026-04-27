package middleware

import (
	"fmt"
	"net/http"
	"strings"
	"time"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"

	"github.com/gin-gonic/gin"
	"github.com/golang-jwt/jwt/v5"
)

// AuthMiddleware middleware untuk verifikasi JWT
func AuthMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		tokenString := c.GetHeader("Authorization")
		if tokenString == "" {
			c.JSON(http.StatusUnauthorized, models.ApiResponse{
				Success: false,
				Message: "Token tidak ditemukan",
				Errors:  gin.H{"detail": "Token tidak ditemukan"},
			})
			c.Abort()
			return
		}

		// Hapus prefix "Bearer "
		tokenString = strings.TrimPrefix(tokenString, "Bearer ")

		token, err := jwt.Parse(tokenString, func(token *jwt.Token) (interface{}, error) {
			return []byte(config.AppConfig.JWTSecret), nil
		})

		if err != nil || !token.Valid {
			c.JSON(http.StatusUnauthorized, models.ApiResponse{
				Success: false,
				Message: "Token tidak valid",
				Errors:  gin.H{"detail": "Token tidak valid"},
			})
			c.Abort()
			return
		}

		if claims, ok := token.Claims.(jwt.MapClaims); ok {
			c.Set("user_id", claims["user_id"])
			c.Set("username", claims["username"])
			c.Set("role", claims["role"])
			c.Set("nomor_induk_siswa", claims["nomor_induk_siswa"])
			c.Set("id_guru", claims["id_guru"])
			c.Next()
			return
		}

		c.JSON(http.StatusUnauthorized, models.ApiResponse{
			Success: false,
			Message: "Token claims tidak valid",
		})
		c.Abort()
		return
	}
}

// AdminRoleMiddleware memastikan endpoint admin hanya bisa diakses role backend-admin.
func AdminRoleMiddleware() gin.HandlerFunc {
	allowedRoles := map[string]bool{
		"admin":      true,
		"superadmin": true,
		"guru":       true,
	}

	return func(c *gin.Context) {
		roleRaw, exists := c.Get("role")
		if !exists {
			c.JSON(http.StatusForbidden, models.ApiResponse{
				Success: false,
				Message: "Akses ditolak",
			})
			c.Abort()
			return
		}

		role := fmt.Sprintf("%v", roleRaw)
		if !allowedRoles[strings.ToLower(strings.TrimSpace(role))] {
			c.JSON(http.StatusForbidden, models.ApiResponse{
				Success: false,
				Message: "Role tidak memiliki akses admin",
			})
			c.Abort()
			return
		}

		c.Next()
	}
}

// GenerateToken membuat JWT token baru
func GenerateToken(email, role string) (string, error) {
	claims := jwt.MapClaims{
		"email": email,
		"role":  role,
		"exp":   time.Now().Add(time.Duration(config.AppConfig.JWTExpiration) * time.Hour).Unix(),
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	return token.SignedString([]byte(config.AppConfig.JWTSecret))
}

// CORSMiddleware middleware untuk CORS
func CORSMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		c.Header("Access-Control-Allow-Origin", "*")
		c.Header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
		c.Header("Access-Control-Allow-Headers", "Content-Type, Authorization")
		if c.Request.Method == "OPTIONS" {
			c.AbortWithStatus(204)
			return
		}
		c.Next()
	}
}
