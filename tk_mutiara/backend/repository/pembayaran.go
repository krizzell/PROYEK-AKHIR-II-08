package repository

import (
	"database/sql"
	"errors"
	"fmt"
	"strconv"
	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
)

// PembayaranRepository interface untuk pembayaran operations
type PembayaranRepository interface {
	GetAll() ([]models.Pembayaran, error)
	GetByID(id string) (*models.Pembayaran, error)
	GetByStatus(status string) ([]models.Pembayaran, error)
	Create(p *models.Pembayaran) error
	Update(p *models.Pembayaran) error
	Delete(id string) error
}

// pembayaranRepo implementasi repository
type pembayaranRepo struct {
	db *sql.DB
}

// NewPembayaranRepository membuat instance baru
func NewPembayaranRepository() PembayaranRepository {
	return &pembayaranRepo{db: config.DB}
}

// GetAll mengambil semua pembayaran
func (r *pembayaranRepo) GetAll() ([]models.Pembayaran, error) {
	query := `SELECT id, bulan, tahun, nominal, status, tanggal_bayar, metode_pembayaran, kode_transaksi 
	          FROM pembayaran ORDER BY tahun DESC, bulan DESC`

	rows, err := r.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var pembayaran []models.Pembayaran
	for rows.Next() {
		var p models.Pembayaran
		if err := rows.Scan(&p.ID, &p.Bulan, &p.Tahun, &p.Nominal, &p.Status, &p.TanggalBayar, &p.MetodePembayaran, &p.KodeTransaksi); err != nil {
			return nil, err
		}
		pembayaran = append(pembayaran, p)
	}

	return pembayaran, nil
}

// GetByID mengambil pembayaran berdasarkan ID
func (r *pembayaranRepo) GetByID(id string) (*models.Pembayaran, error) {
	query := `SELECT id, bulan, tahun, nominal, status, tanggal_bayar, metode_pembayaran, kode_transaksi 
	          FROM pembayaran WHERE id = ?`

	var p models.Pembayaran
	err := r.db.QueryRow(query, id).Scan(&p.ID, &p.Bulan, &p.Tahun, &p.Nominal, &p.Status, &p.TanggalBayar, &p.MetodePembayaran, &p.KodeTransaksi)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, errors.New("pembayaran tidak ditemukan")
		}
		return nil, err
	}

	return &p, nil
}

// GetByStatus mengambil pembayaran berdasarkan status
func (r *pembayaranRepo) GetByStatus(status string) ([]models.Pembayaran, error) {
	query := `SELECT id, bulan, tahun, nominal, status, tanggal_bayar, metode_pembayaran, kode_transaksi 
	          FROM pembayaran WHERE status = ? ORDER BY tahun DESC, bulan DESC`

	rows, err := r.db.Query(query, status)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var pembayaran []models.Pembayaran
	for rows.Next() {
		var p models.Pembayaran
		if err := rows.Scan(&p.ID, &p.Bulan, &p.Tahun, &p.Nominal, &p.Status, &p.TanggalBayar, &p.MetodePembayaran, &p.KodeTransaksi); err != nil {
			return nil, err
		}
		pembayaran = append(pembayaran, p)
	}

	return pembayaran, nil
}

// Create membuat pembayaran baru
func (r *pembayaranRepo) Create(p *models.Pembayaran) error {
	query := `INSERT INTO pembayaran (bulan, tahun, nominal, status, tanggal_bayar, metode_pembayaran, kode_transaksi) 
	          VALUES (?, ?, ?, ?, ?, ?, ?)`
	result, err := r.db.Exec(query, p.Bulan, p.Tahun, p.Nominal, p.Status, p.TanggalBayar, p.MetodePembayaran, p.KodeTransaksi)
	if err != nil {
		return err
	}

	insertID, err := result.LastInsertId()
	if err != nil {
		return fmt.Errorf("gagal mengambil id pembayaran: %w", err)
	}
	p.ID = strconv.FormatInt(insertID, 10)
	return nil
}

// Update mengupdate pembayaran
func (r *pembayaranRepo) Update(p *models.Pembayaran) error {
	query := `UPDATE pembayaran SET bulan = ?, tahun = ?, nominal = ?, status = ?, tanggal_bayar = ?, 
	          metode_pembayaran = ?, kode_transaksi = ? WHERE id = ?`
	_, err := r.db.Exec(query, p.Bulan, p.Tahun, p.Nominal, p.Status, p.TanggalBayar, p.MetodePembayaran, p.KodeTransaksi, p.ID)
	return err
}

// Delete menghapus pembayaran
func (r *pembayaranRepo) Delete(id string) error {
	query := `DELETE FROM pembayaran WHERE id = ?`
	_, err := r.db.Exec(query, id)
	return err
}
