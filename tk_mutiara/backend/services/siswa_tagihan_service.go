package services

import (
	"database/sql"
	"fmt"
	"strconv"
	"strings"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
)

// ==============================
// SISWA SERVICE
// ==============================

// GetAllSiswa mengambil semua siswa
func GetAllSiswa(db *sql.DB) ([]models.SiswaDetail, error) {
	siswa, err := repository.GetAllSiswa(db)
	if err != nil {
		return nil, err
	}

	return siswa, nil
}

// GetSiswaDetail mengambil detail siswa berdasarkan ID
func GetSiswaDetail(db *sql.DB, nomorIndukSiswa string) (*models.SiswaDetail, error) {
	if nomorIndukSiswa == "" {
		return nil, fmt.Errorf("nomor induk siswa tidak boleh kosong")
	}

	siswa, err := repository.GetSiswaByID(db, nomorIndukSiswa)
	if err != nil {
		return nil, err
	}

	return siswa, nil
}

// GetSiswaByKelas mengambil siswa berdasarkan kelas
func GetSiswaByKelas(db *sql.DB, idKelas int) ([]models.SiswaDetail, error) {
	if idKelas <= 0 {
		return nil, fmt.Errorf("kelas ID tidak valid")
	}

	siswa, err := repository.GetSiswaByKelas(db, idKelas)
	if err != nil {
		return nil, err
	}

	return siswa, nil
}

// CreateNewSiswa membuat siswa baru
func CreateNewSiswa(db *sql.DB, siswaReq *models.CreateSiswaRequest) (int64, error) {
	// Validasi
	if siswaReq.IDKelas <= 0 {
		return 0, fmt.Errorf("kelas ID tidak valid")
	}
	if siswaReq.NamaAnak == "" {
		return 0, fmt.Errorf("nama anak tidak boleh kosong")
	}
	if siswaReq.NamaOrgTua == "" {
		return 0, fmt.Errorf("nama orang tua tidak boleh kosong")
	}
	if siswaReq.TglLahir == "" {
		return 0, fmt.Errorf("tanggal lahir tidak boleh kosong")
	}
	if siswaReq.JenisKelamin == "" {
		return 0, fmt.Errorf("jenis kelamin tidak boleh kosong")
	}
	if siswaReq.Alamat == "" {
		return 0, fmt.Errorf("alamat tidak boleh kosong")
	}

	// Cek kelas exist
	_, err := repository.GetKelasByID(db, siswaReq.IDKelas)
	if err != nil {
		return 0, fmt.Errorf("kelas tidak ditemukan")
	}

	// Create siswa
	id, err := repository.CreateSiswa(db, siswaReq)
	if err != nil {
		return 0, err
	}

	return id, nil
}

// RemoveSiswa menghapus siswa
func RemoveSiswa(db *sql.DB, nomorIndukSiswa string) error {
	if nomorIndukSiswa == "" {
		return fmt.Errorf("nomor induk siswa tidak boleh kosong")
	}

	// Cek siswa exist
	_, err := repository.GetSiswaByID(db, nomorIndukSiswa)
	if err != nil {
		return fmt.Errorf("siswa tidak ditemukan")
	}

	// Delete
	err = repository.DeleteSiswa(db, nomorIndukSiswa)
	if err != nil {
		return err
	}

	return nil
}

// ==============================
// TAGIHAN SERVICE
// ==============================

// GetAllTagihan mengambil semua tagihan
func GetAllTagihan(db *sql.DB) ([]models.TagihanDetail, error) {
	tagihan, err := repository.GetAllTagihan(db)
	if err != nil {
		return nil, err
	}

	return tagihan, nil
}

// GetTagihanDetail mengambil detail tagihan
func GetTagihanDetail(db *sql.DB, idTagihan int) (*models.TagihanDetail, error) {
	if idTagihan <= 0 {
		return nil, fmt.Errorf("tagihan ID tidak valid")
	}

	tagihan, err := repository.GetTagihanByID(db, idTagihan)
	if err != nil {
		return nil, err
	}

	return tagihan, nil
}

// GetTagihanBySiswa mengambil tagihan berdasarkan siswa
func GetTagihanBySiswa(db *sql.DB, nomorIndukSiswa string) ([]models.TagihanDetail, error) {
	if nomorIndukSiswa == "" {
		return nil, fmt.Errorf("nomor induk siswa tidak boleh kosong")
	}

	tagihan, err := repository.GetTagihanBySiswa(db, nomorIndukSiswa)
	if err != nil {
		return nil, err
	}

	return tagihan, nil
}

// CreateNewTagihan membuat tagihan baru
func CreateNewTagihan(db *sql.DB, tagihanReq *models.CreateTagihanRequest) (int64, error) {
	// Validasi
	nomorIndukInput := strings.TrimSpace(tagihanReq.NomorIndukSiswa.String())
	if nomorIndukInput == "" {
		return 0, fmt.Errorf("nomor induk siswa tidak valid")
	}
	if tagihanReq.JumlahTagihan <= 0 {
		return 0, fmt.Errorf("jumlah tagihan harus lebih dari 0")
	}
	if tagihanReq.Periode == "" {
		return 0, fmt.Errorf("periode tidak boleh kosong")
	}

	// NIS di DB berupa string; seed menggunakan format zero-padded (contoh: 000007).
	// Coba lookup raw terlebih dulu, lalu fallback ke format 6 digit bila input numerik.
	nomorIndukForInsert := nomorIndukInput
	if _, err := repository.GetSiswaByID(db, nomorIndukInput); err != nil {
		nomorIndukPadded := nomorIndukInput
		if n, convErr := strconv.Atoi(nomorIndukInput); convErr == nil {
			nomorIndukPadded = fmt.Sprintf("%06d", n)
		}

		if nomorIndukPadded == nomorIndukInput {
			return 0, fmt.Errorf("siswa tidak ditemukan")
		}

		if _, errPadded := repository.GetSiswaByID(db, nomorIndukPadded); errPadded != nil {
			return 0, fmt.Errorf("siswa tidak ditemukan")
		}
		nomorIndukForInsert = nomorIndukPadded
	}

	// Create tagihan
	id, err := repository.CreateTagihan(db, nomorIndukForInsert, tagihanReq.JumlahTagihan, tagihanReq.Periode)
	if err != nil {
		return 0, err
	}

	return id, nil
}

// RemoveTagihan menghapus tagihan
func RemoveTagihan(db *sql.DB, idTagihan int) error {
	if idTagihan <= 0 {
		return fmt.Errorf("tagihan ID tidak valid")
	}

	// Cek tagihan exist
	_, err := repository.GetTagihanByID(db, idTagihan)
	if err != nil {
		return fmt.Errorf("tagihan tidak ditemukan")
	}

	// Delete
	err = repository.DeleteTagihan(db, idTagihan)
	if err != nil {
		return err
	}

	return nil
}

// ==============================
// PEMBAYARAN SERVICE
// ==============================

// GetAllPembayaran mengambil semua pembayaran
func GetAllPembayaran(db *sql.DB) ([]models.PembayaranDetail, error) {
	pembayaran, err := repository.GetAllPembayaran(db)
	if err != nil {
		return nil, err
	}

	return pembayaran, nil
}

// GetPembayaranDetail mengambil detail pembayaran
func GetPembayaranDetail(db *sql.DB, idPembayaran int) (*models.PembayaranDetail, error) {
	if idPembayaran <= 0 {
		return nil, fmt.Errorf("pembayaran ID tidak valid")
	}

	pembayaran, err := repository.GetPembayaranByID(db, idPembayaran)
	if err != nil {
		return nil, err
	}

	return pembayaran, nil
}

// GetPembayaranByTagihan mengambil pembayaran berdasarkan tagihan
func GetPembayaranByTagihan(db *sql.DB, idTagihan int) ([]models.PembayaranDetail, error) {
	if idTagihan <= 0 {
		return nil, fmt.Errorf("tagihan ID tidak valid")
	}

	pembayaran, err := repository.GetPembayaranByTagihan(db, idTagihan)
	if err != nil {
		return nil, err
	}

	return pembayaran, nil
}

// UpdatePembayaranStatusService update status pembayaran dengan validasi
func UpdatePembayaranStatusService(db *sql.DB, updateReq *models.UpdatePembayaranRequest) error {
	// Validasi
	if updateReq.IDPembayaran <= 0 {
		return fmt.Errorf("pembayaran ID tidak valid")
	}
	if updateReq.StatusBayar == "" {
		return fmt.Errorf("status bayar tidak boleh kosong")
	}

	// Validasi status
	validStatuses := map[string]bool{
		"menunggu": true,
		"diterima": true,
	}
	if !validStatuses[updateReq.StatusBayar] {
		return fmt.Errorf("status bayar tidak valid. gunakan: menunggu atau diterima")
	}

	// Cek pembayaran exist
	_, err := repository.GetPembayaranByID(db, updateReq.IDPembayaran)
	if err != nil {
		return fmt.Errorf("pembayaran tidak ditemukan")
	}

	// Update status
	err = repository.UpdatePembayaranStatus(db, updateReq.IDPembayaran, updateReq.StatusBayar)
	if err != nil {
		return err
	}

	return nil
}
