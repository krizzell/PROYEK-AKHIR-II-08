package config

import (
	"fmt"
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

type Config struct {
	// Database
	DBHost     string
	DBPort     string
	DBUser     string
	DBPassword string
	DBName     string
	DBSSLMode  string

	// Server
	ServerPort string

	// JWT
	JWTSecret     string
	JWTExpiration int
	Environment   string

	// Midtrans
	MidtransServerKey   string
	MidtransClientKey   string
	MidtransEnvironment string

	// FCM
	FCMServiceAccountPath string
}

var AppConfig *Config

func LoadConfig() error {
	_ = godotenv.Load()

	jwtExp := os.Getenv("JWT_EXPIRATION")
	if jwtExp == "" {
		jwtExp = "24"
	}
	exp, _ := strconv.Atoi(jwtExp)

	AppConfig = &Config{
		DBHost:     getEnv("DB_HOST", "localhost"),
		DBPort:     getEnv("DB_PORT", "3307"),
		DBUser:     getEnv("DB_USER", "root"),
		DBPassword: getEnv("DB_PASSWORD", ""),
		DBName:     getEnv("DB_NAME", "dashboard_pa2"),
		DBSSLMode:  getEnv("DB_SSL_MODE", "false"),
		ServerPort: getEnv("SERVER_PORT", "8081"),

		JWTSecret:     getEnv("JWT_SECRET", "tk_mutiara_secret_key"),
		JWTExpiration: exp,
		Environment:   getEnv("ENVIRONMENT", "development"),

		MidtransServerKey:   getEnv("MIDTRANS_SERVER_KEY", ""),
		MidtransClientKey:   getEnv("MIDTRANS_CLIENT_KEY", ""),
		MidtransEnvironment: getEnv("MIDTRANS_ENVIRONMENT", "sandbox"),

		FCMServiceAccountPath: getEnv("FIREBASE_SERVICE_ACCOUNT_PATH", "firebase-service-account.json"),
	}

	return nil
}

func (c *Config) GetDSN() string {
	return fmt.Sprintf(
		"%s:%s@tcp(%s:%s)/%s?parseTime=true",
		c.DBUser, c.DBPassword, c.DBHost, c.DBPort, c.DBName,
	)
}

func getEnv(key, defaultValue string) string {
	if value, exists := os.LookupEnv(key); exists {
		return value
	}
	return defaultValue
}