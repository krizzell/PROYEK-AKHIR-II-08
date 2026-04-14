package config

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/go-sql-driver/mysql"
)

var DB *sql.DB

// InitDB menginisialisasi koneksi database
func InitDB(config *Config) error {
	var err error
	DB, err = sql.Open("mysql", config.GetDSN())
	if err != nil {
		return fmt.Errorf("gagal membuka database: %w", err)
	}

	// Test koneksi
	err = DB.Ping()
	if err != nil {
		return fmt.Errorf("gagal terhubung ke database: %w", err)
	}

	// Set connection pool
	DB.SetMaxOpenConns(25)
	DB.SetMaxIdleConns(5)

	log.Println("✓ Database berhasil terkoneksi")
	return nil
}

// CloseDB menutup koneksi database
func CloseDB() {
	if DB != nil {
		DB.Close()
	}
}
