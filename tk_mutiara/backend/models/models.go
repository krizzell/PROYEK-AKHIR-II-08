package models

// LoginRequest request untuk login
type LoginRequest struct {
	Email    string `json:"email" binding:"required,email"`
	Password string `json:"password" binding:"required,min=6"`
}

// UserLogin model untuk user login response
type UserLogin struct {
	UserID          int    `json:"user_id"`
	Username        string `json:"username"`
	Role            string `json:"role"`
	NomorIndukSiswa string `json:"nomor_induk_siswa"`
	IDGuru          int64  `json:"id_guru"`
	NamaSiswa       string `json:"nama_siswa"`
	NamaOrtu        string `json:"nama_ortu"`
	Kelas           string `json:"kelas"`
	NamaGuru        string `json:"nama_guru"`
}

// Pengumuman model untuk announcements (sesuai Laravel)
type Pengumuman struct {
	IDPengumuman int64  `json:"id_pengumuman" db:"id_pengumuman"`
	IDGuru       int64  `json:"id_guru" db:"id_guru"`
	NamaGuru     string `json:"nama_guru" db:"nama_guru"`
	Judul        string `json:"judul" db:"judul"`
	Media        string `json:"media" db:"media"`
	WaktuUnggah  string `json:"waktu_unggah" db:"waktu_unggah"`
	Deskripsi    string `json:"deskripsi" db:"deskripsi"`
	CreatedAt    string `json:"created_at" db:"created_at"`
	UpdatedAt    string `json:"updated_at" db:"updated_at"`
}

// Perkembangan model untuk development tracking
type PerkembanganKategori struct {
	IDCategori     int    `json:"id_perkembangan_kategori" db:"id_perkembangan_kategori"`
	IDPerkembangan int    `json:"id_perkembangan" db:"id_perkembangan"`
	NamaKategori   string `json:"nama_kategori" db:"nama_kategori"`
	Nilai          int    `json:"nilai" db:"nilai"`
	StatusUtama    string `json:"status_utama" db:"status_utama"`
	Deskripsi      string `json:"deskripsi" db:"deskripsi"`
	CreatedAt      string `json:"created_at" db:"created_at"`
	UpdatedAt      string `json:"updated_at" db:"updated_at"`
}

type Perkembangan struct {
	IDPerkembangan    int                    `json:"id_perkembangan" db:"id_perkembangan"`
	IDGuru            int                    `json:"id_guru" db:"id_guru"`
	NomorIndukSiswa   string                 `json:"nomor_induk_siswa" db:"nomor_induk_siswa"`
	NamaAnak          string                 `json:"nama_siswa" db:"nama_siswa"`
	NamaGuru          string                 `json:"nama_guru" db:"nama_guru"`
	Kelas             string                 `json:"kelas" db:"kelas"`
	Bulan             int                    `json:"bulan" db:"bulan"`
	Tahun             int                    `json:"tahun" db:"tahun"`
	Kategori          string                 `json:"kategori" db:"kategori"`
	Deskripsi         string                 `json:"deskripsi" db:"deskripsi"`
	TemplateDeskripsi string                 `json:"template_deskripsi" db:"template_deskripsi"`
	StatusUtama       string                 `json:"status_utama" db:"status_utama"`
	CreatedAt         string                 `json:"created_at" db:"created_at"`
	UpdatedAt         string                 `json:"updated_at" db:"updated_at"`
	KategoriDetails   []PerkembanganKategori `json:"kategori_details" db:"-"`
}

// Pembayaran model untuk payment tracking
type Pembayaran struct {
	ID               string `json:"id" db:"id"`
	Bulan            string `json:"bulan" db:"bulan"`
	Tahun            string `json:"tahun" db:"tahun"`
	Nominal          int    `json:"nominal" db:"nominal"`
	Status           string `json:"status" db:"status"` // "lunas" atau "belum"
	TanggalBayar     string `json:"tanggal_bayar" db:"tanggal_bayar"`
	MetodePembayaran string `json:"metode_pembayaran" db:"metode_pembayaran"`
	KodeTransaksi    string `json:"kode_transaksi" db:"kode_transaksi"`
}

// BayarRequest request untuk pembayaran
type BayarRequest struct {
	ID     string `json:"id" binding:"required"`
	Metode string `json:"metode" binding:"required"`
}

// ApiResponse generic response wrapper
type ApiResponse struct {
	Success bool        `json:"success"`
	Message string      `json:"message,omitempty"`
	Data    interface{} `json:"data,omitempty"`
	Errors  interface{} `json:"errors,omitempty"`
}
