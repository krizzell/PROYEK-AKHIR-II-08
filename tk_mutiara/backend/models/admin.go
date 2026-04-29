package models

import (
	"encoding/json"
	"fmt"
	"strings"
	"time"
)

// StringOrNumber menerima input JSON string atau number, lalu menyimpannya sebagai string.
type StringOrNumber string

func (v *StringOrNumber) UnmarshalJSON(data []byte) error {
	raw := strings.TrimSpace(string(data))
	if raw == "" || raw == "null" {
		return fmt.Errorf("nilai tidak boleh kosong")
	}

	if strings.HasPrefix(raw, "\"") {
		var s string
		if err := json.Unmarshal(data, &s); err != nil {
			return err
		}
		s = strings.TrimSpace(s)
		if s == "" {
			return fmt.Errorf("nilai tidak boleh kosong")
		}
		*v = StringOrNumber(s)
		return nil
	}

	var n json.Number
	dec := json.NewDecoder(strings.NewReader(raw))
	dec.UseNumber()
	if err := dec.Decode(&n); err != nil {
		return fmt.Errorf("nilai harus string atau number")
	}

	*v = StringOrNumber(n.String())
	return nil
}

func (v StringOrNumber) String() string {
	return string(v)
}

// ==============================
// ADMIN MODELS
// ==============================

// Admin model untuk admin user
type Admin struct {
	IDAdmin   int       `json:"id_admin" db:"id_admin"`
	IDGuru    int       `json:"id_guru" db:"id_guru"`
	Username  string    `json:"username" db:"username"`
	Password  string    `json:"-" db:"password"` // jangan expose password
	Email     string    `json:"email" db:"email"`
	Role      string    `json:"role" db:"role"` // admin, superadmin
	IsActive  bool      `json:"is_active" db:"is_active"`
	CreatedAt time.Time `json:"created_at" db:"created_at"`
	UpdatedAt time.Time `json:"updated_at" db:"updated_at"`
}

// ==============================
// DASHBOARD METRICS
// ==============================

// DashboardMetrics model untuk dashboard overview
type DashboardMetrics struct {
	TotalSiswa          int64   `json:"total_siswa"`
	TotalGuru           int64   `json:"total_guru"`
	TotalKelas          int64   `json:"total_kelas"`
	TotalTagihan        int64   `json:"total_tagihan"`
	TagihanTerbayar     int64   `json:"tagihan_terbayar"`
	TagihanBelumBayar   int64   `json:"tagihan_belum_bayar"`
	TotalPemasukanBulan float64 `json:"total_pemasukan_bulan"`
	TotalHutangBulan    float64 `json:"total_hutang_bulan"`
}

// DashboardStatistic model untuk statistik detail
type DashboardStatistic struct {
	Date      string  `json:"date"`
	TotalData int64   `json:"total_data"`
	Revenue   float64 `json:"revenue"`
	Status    string  `json:"status"`
}

// ==============================
// ADMIN MODELS - DETAILED VIEWS
// ==============================

// GurDetail model untuk guru detail di dashboard
type GuruDetail struct {
	IDGuru     int    `json:"id_guru" db:"id_guru"`
	NamaGuru   string `json:"nama_guru" db:"nama_guru"`
	NoHP       string `json:"no_hp" db:"no_hp"`
	Email      string `json:"email" db:"email"`
	TotalKelas int    `json:"total_kelas"`
	TotalSiswa int    `json:"total_siswa"`
	CreatedAt  string `json:"created_at" db:"created_at"`
}

// SiswaDetail model untuk siswa detail di dashboard
type SiswaDetail struct {
	NomorIndukSiswa  string  `json:"nomor_induk_siswa" db:"nomor_induk_siswa"`
	NamaAnak         string  `json:"nama_anak" db:"nama_anak"`
	NamaOrgTua       string  `json:"nama_orgtua" db:"nama_orgtua"`
	Kelas            string  `json:"kelas" db:"kelas"`
	JenisKelamin     string  `json:"jenis_kelamin" db:"jenis_kelamin"`
	TglLahir         string  `json:"tgl_lahir" db:"tgl_lahir"`
	Alamat           string  `json:"alamat" db:"alamat"`
	TotalTagihan     float64 `json:"total_tagihan"`
	TotalBayar       float64 `json:"total_bayar"`
	SisaTagihan      float64 `json:"sisa_tagihan"`
	StatusPembayaran string  `json:"status_pembayaran"`
	CreatedAt        string  `json:"created_at" db:"created_at"`
}

// KelasDetail model untuk kelas detail
type KelasDetail struct {
	IDKelas    int    `json:"id_kelas" db:"id_kelas"`
	NamaKelas  string `json:"nama_kelas" db:"nama_kelas"`
	NamaGuru   string `json:"nama_guru" db:"nama_guru"`
	TotalSiswa int    `json:"total_siswa"`
	CreatedAt  string `json:"created_at" db:"created_at"`
}

// ==============================
// PAYMENT DASHBOARD
// ==============================

// TagihanDetail model untuk tagihan detail dashboard
type TagihanDetail struct {
	IDTagihan       int     `json:"id_tagihan" db:"id_tagihan"`
	NomorIndukSiswa string  `json:"nomor_induk_siswa" db:"nomor_induk_siswa"`
	NamaAnak        string  `json:"nama_anak" db:"nama_anak"`
	JumlahTagihan   float64 `json:"jumlah_tagihan" db:"jumlah_tagihan"`
	Periode         string  `json:"periode" db:"periode"`
	Status          string  `json:"status" db:"status"`
	TotalBayar      float64 `json:"total_bayar"`
	SisaBayar       float64 `json:"sisa_bayar"`
	CreatedAt       string  `json:"created_at" db:"created_at"`
}

// PembayaranDetail model untuk pembayaran detail
type PembayaranDetail struct {
	IDPembayaran  int     `json:"id_pembayaran" db:"id_pembayaran"`
	IDTagihan     int     `json:"id_tagihan" db:"id_tagihan"`
	NamaAnak      string  `json:"nama_anak" db:"nama_anak"`
	JumlahBayar   float64 `json:"jumlah_bayar" db:"jumlah_bayar"`
	TglPembayaran string  `json:"tgl_pembayaran" db:"tgl_pembayaran"`
	StatusBayar   string  `json:"status_bayar" db:"status_bayar"`
	CreatedAt     string  `json:"created_at" db:"created_at"`
}

// ==============================
// REQUEST/RESPONSE MODELS
// ==============================

// CreateGruRequest request untuk create guru
type CreateGuruRequest struct {
	NamaGuru string `json:"nama_guru" binding:"required"`
	NoHP     string `json:"no_hp" binding:"required"`
	Email    string `json:"email" binding:"required,email"`
}

// UpdateGuruRequest request untuk update guru
type UpdateGuruRequest struct {
	NamaGuru string `json:"nama_guru"`
	NoHP     string `json:"no_hp"`
	Email    string `json:"email"`
}

// CreateKelasRequest request untuk create kelas
type CreateKelasRequest struct {
	IDGuru    int    `json:"id_guru" binding:"required"`
	NamaKelas string `json:"nama_kelas" binding:"required"`
}

// CreateSiswaRequest request untuk create siswa
type CreateSiswaRequest struct {
	IDKelas      int    `json:"id_kelas" binding:"required"`
	NamaAnak     string `json:"nama_anak" binding:"required"`
	NamaOrgTua   string `json:"nama_orgtua" binding:"required"`
	TglLahir     string `json:"tgl_lahir" binding:"required"`
	JenisKelamin string `json:"jenis_kelamin" binding:"required"`
	Alamat       string `json:"alamat" binding:"required"`
}

// CreateTagihanRequest request untuk create tagihan
type CreateTagihanRequest struct {
	NomorIndukSiswa StringOrNumber `json:"nomor_induk_siswa" binding:"required"`
	JumlahTagihan   float64        `json:"jumlah_tagihan" binding:"required"`
	Periode         string         `json:"periode" binding:"required"`
}

// UpdatePembayaranRequest request untuk update status pembayaran
type UpdatePembayaranRequest struct {
	IDPembayaran int    `json:"id_pembayaran" binding:"required"`
	StatusBayar  string `json:"status_bayar" binding:"required"`
}

// CreateMidtransTransactionRequest request untuk membuat transaksi Midtrans
type CreateMidtransTransactionRequest struct {
	IDTagihan int `json:"id_tagihan" binding:"required"`
}

// CreateMidtransTransactionResponse response untuk transaksi Midtrans
type CreateMidtransTransactionResponse struct {
	IDTagihan     int     `json:"id_tagihan"`
	IDPembayaran  int64   `json:"id_pembayaran"`
	OrderID       string  `json:"order_id"`
	SnapToken     string  `json:"snap_token"`
	RedirectURL   string  `json:"redirect_url"`
	Amount        float64 `json:"amount"`
	StatusTagihan string  `json:"status_tagihan"`
	StatusBayar   string  `json:"status_bayar"`
	ClientKey     string  `json:"client_key"`
	ExpiredAt     string  `json:"expired_at,omitempty"`
}

// PaymentStatusResponse response polling status pembayaran
type PaymentStatusResponse struct {
	IDTagihan     int     `json:"id_tagihan"`
	IDPembayaran  int64   `json:"id_pembayaran,omitempty"`
	OrderID       string  `json:"order_id,omitempty"`
	StatusTagihan string  `json:"status_tagihan"`
	StatusBayar   string  `json:"status_bayar,omitempty"`
	JumlahTagihan float64 `json:"jumlah_tagihan,omitempty"`
	JumlahBayar   float64 `json:"jumlah_bayar,omitempty"`
}

// MidtransNotification payload notifikasi Midtrans
type MidtransNotification struct {
	TransactionTime   string `json:"transaction_time"`
	TransactionStatus string `json:"transaction_status"`
	TransactionID     string `json:"transaction_id"`
	StatusMessage     string `json:"status_message"`
	StatusCode        string `json:"status_code"`
	SignatureKey      string `json:"signature_key"`
	PaymentType       string `json:"payment_type"`
	OrderID           string `json:"order_id"`
	MerchantID        string `json:"merchant_id"`
	GrossAmount       string `json:"gross_amount"`
	FraudStatus       string `json:"fraud_status"`
	Currency          string `json:"currency"`
}

// TagihanForPayment data tagihan untuk proses pembayaran
type TagihanForPayment struct {
	IDTagihan       int     `json:"id_tagihan"`
	NomorIndukSiswa string  `json:"nomor_induk_siswa"`
	NamaSiswa       string  `json:"nama_siswa"`
	NamaOrangtua    string  `json:"nama_orgtua"`
	Periode         string  `json:"periode"`
	JumlahTagihan   float64 `json:"jumlah_tagihan"`
	StatusTagihan   string  `json:"status_tagihan"`
}
