package repository

import (
	"database/sql"
	"fmt"
	"time"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/utils"
)

// ==============================
// TAGIHAN REPOSITORY
// ==============================

// GetAllTagihan mengambil semua tagihan
func GetAllTagihan(db *sql.DB) ([]models.TagihanDetail, error) {
	rows, err := db.Query(`
		SELECT 
			t.id_tagihan,
			t.nomor_induk_siswa,
			s.nama_siswa AS nama_anak,
			t.jumlah_tagihan,
			t.periode,
			t.status,
			COALESCE(t.payment_status, t.status, 'belum_bayar') as payment_status,
			COALESCE(DATE_FORMAT(t.payment_date, '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(MAX(CASE WHEN p.status_bayar = 'diterima' THEN p.paid_at ELSE NULL END), '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(MAX(CASE WHEN p.status_bayar = 'diterima' THEN p.tgl_pembayaran ELSE NULL END), '%Y-%m-%d %H:%i:%s'), '') as payment_date,
			COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0) as total_bayar,
			(t.jumlah_tagihan - COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0)) as sisa_bayar,
			t.created_at,
			t.updated_at
		FROM tagihan t
		LEFT JOIN siswa s ON t.nomor_induk_siswa = s.nomor_induk_siswa
		LEFT JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
		GROUP BY t.id_tagihan
		ORDER BY t.created_at DESC
	`)
	if err != nil {
		return nil, fmt.Errorf("error query tagihan: %v", err)
	}
	defer rows.Close()

	var tagihan []models.TagihanDetail
	for rows.Next() {
		var t models.TagihanDetail
		err := rows.Scan(
			&t.IDTagihan,
			&t.NomorIndukSiswa,
			&t.NamaAnak,
			&t.JumlahTagihan,
			&t.Periode,
			&t.Status,
			&t.PaymentStatus,
			&t.PaymentDate,
			&t.TotalBayar,
			&t.SisaBayar,
			&t.CreatedAt,
			&t.UpdatedAt,
		)
		if err != nil {
			return nil, fmt.Errorf("error scan tagihan: %v", err)
		}
		applyLateFee(&t)
		tagihan = append(tagihan, t)
	}

	return tagihan, nil
}

// GetTagihanByID mengambil tagihan berdasarkan ID
func GetTagihanByID(db *sql.DB, idTagihan int) (*models.TagihanDetail, error) {
	var t models.TagihanDetail
	err := db.QueryRow(`
		SELECT 
			t.id_tagihan,
			t.nomor_induk_siswa,
			s.nama_siswa AS nama_anak,
			t.jumlah_tagihan,
			t.periode,
			t.status,
			COALESCE(t.payment_status, t.status, 'belum_bayar') as payment_status,
			COALESCE(DATE_FORMAT(t.payment_date, '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(MAX(CASE WHEN p.status_bayar = 'diterima' THEN p.paid_at ELSE NULL END), '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(MAX(CASE WHEN p.status_bayar = 'diterima' THEN p.tgl_pembayaran ELSE NULL END), '%Y-%m-%d %H:%i:%s'), '') as payment_date,
			COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0) as total_bayar,
			(t.jumlah_tagihan - COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0)) as sisa_bayar,
			t.created_at,
			t.updated_at
		FROM tagihan t
		LEFT JOIN siswa s ON t.nomor_induk_siswa = s.nomor_induk_siswa
		LEFT JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
		WHERE t.id_tagihan = ?
		GROUP BY t.id_tagihan
	`, idTagihan).Scan(
		&t.IDTagihan,
		&t.NomorIndukSiswa,
		&t.NamaAnak,
		&t.JumlahTagihan,
		&t.Periode,
		&t.Status,
		&t.PaymentStatus,
		&t.PaymentDate,
		&t.TotalBayar,
		&t.SisaBayar,
		&t.CreatedAt,
		&t.UpdatedAt,
	)
	if err != nil {
		return nil, fmt.Errorf("error query tagihan: %v", err)
	}
	applyLateFee(&t)

	return &t, nil
}

// GetTagihanBySiswa mengambil tagihan berdasarkan siswa
func GetTagihanBySiswa(db *sql.DB, nomorIndukSiswa string) ([]models.TagihanDetail, error) {
	rows, err := db.Query(`
		SELECT 
			t.id_tagihan,
			t.nomor_induk_siswa,
			s.nama_siswa AS nama_anak,
			t.jumlah_tagihan,
			t.periode,
			t.status,
			COALESCE(t.payment_status, t.status, 'belum_bayar') as payment_status,
			COALESCE(DATE_FORMAT(t.payment_date, '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(MAX(CASE WHEN p.status_bayar = 'diterima' THEN p.paid_at ELSE NULL END), '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(MAX(CASE WHEN p.status_bayar = 'diterima' THEN p.tgl_pembayaran ELSE NULL END), '%Y-%m-%d %H:%i:%s'), '') as payment_date,
			COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0) as total_bayar,
			(t.jumlah_tagihan - COALESCE(SUM(CASE WHEN p.status_bayar = 'diterima' THEN p.jumlah_bayar ELSE 0 END), 0)) as sisa_bayar,
			t.created_at,
			t.updated_at
		FROM tagihan t
		LEFT JOIN siswa s ON t.nomor_induk_siswa = s.nomor_induk_siswa
		LEFT JOIN pembayaran p ON t.id_tagihan = p.id_tagihan
		WHERE t.nomor_induk_siswa = ?
		GROUP BY t.id_tagihan
		ORDER BY t.created_at DESC
	`, nomorIndukSiswa)
	if err != nil {
		return nil, fmt.Errorf("error query tagihan: %v", err)
	}
	defer rows.Close()

	var tagihan []models.TagihanDetail
	for rows.Next() {
		var t models.TagihanDetail
		err := rows.Scan(
			&t.IDTagihan,
			&t.NomorIndukSiswa,
			&t.NamaAnak,
			&t.JumlahTagihan,
			&t.Periode,
			&t.Status,
			&t.PaymentStatus,
			&t.PaymentDate,
			&t.TotalBayar,
			&t.SisaBayar,
			&t.CreatedAt,
			&t.UpdatedAt,
		)
		if err != nil {
			return nil, fmt.Errorf("error scan tagihan: %v", err)
		}
		applyLateFee(&t)
		tagihan = append(tagihan, t)
	}

	return tagihan, nil
}

// CreateTagihan membuat tagihan baru
func CreateTagihan(db *sql.DB, nomorIndukSiswa string, jumlahTagihan float64, periode string) (int64, error) {
	result, err := db.Exec(`
		INSERT INTO tagihan (nomor_induk_siswa, jumlah_tagihan, periode, status, created_at, updated_at) 
		VALUES (?, ?, ?, 'belum_bayar', NOW(), NOW())
	`,
		nomorIndukSiswa,
		jumlahTagihan,
		periode,
	)
	if err != nil {
		return 0, fmt.Errorf("error create tagihan: %v", err)
	}

	return result.LastInsertId()
}

// DeleteTagihan delete tagihan
func DeleteTagihan(db *sql.DB, idTagihan int) error {
	_, err := db.Exec("DELETE FROM tagihan WHERE id_tagihan = ?", idTagihan)
	if err != nil {
		return fmt.Errorf("error delete tagihan: %v", err)
	}

	return nil
}

func applyLateFee(t *models.TagihanDetail) {
	paidLateFee := t.TotalBayar - t.JumlahTagihan
	if paidLateFee < 0 {
		paidLateFee = 0
	}

	t.DendaKeterlambatan = paidLateFee
	if t.DendaKeterlambatan == 0 {
		t.DendaKeterlambatan = utils.LateFeeForPeriod(t.Periode, t.Status, time.Now())
	}
	t.TotalPembayaran = t.JumlahTagihan + t.DendaKeterlambatan
	if t.SisaBayar > 0 {
		t.SisaBayar += t.DendaKeterlambatan
	}
}

// ==============================
// PEMBAYARAN REPOSITORY
// ==============================

// GetAllPembayaran mengambil semua pembayaran
func GetAllPembayaran(db *sql.DB) ([]models.PembayaranDetail, error) {
	rows, err := db.Query(`
		SELECT 
			p.id_pembayaran,
			p.id_tagihan,
			s.nama_siswa AS nama_anak,
			p.jumlah_bayar,
			p.tgl_pembayaran,
			p.status_bayar,
			p.created_at
		FROM pembayaran p
		LEFT JOIN tagihan t ON p.id_tagihan = t.id_tagihan
		LEFT JOIN siswa s ON t.nomor_induk_siswa = s.nomor_induk_siswa
		ORDER BY p.created_at DESC
	`)
	if err != nil {
		return nil, fmt.Errorf("error query pembayaran: %v", err)
	}
	defer rows.Close()

	var pembayaran []models.PembayaranDetail
	for rows.Next() {
		var p models.PembayaranDetail
		err := rows.Scan(
			&p.IDPembayaran,
			&p.IDTagihan,
			&p.NamaAnak,
			&p.JumlahBayar,
			&p.TglPembayaran,
			&p.StatusBayar,
			&p.CreatedAt,
		)
		if err != nil {
			return nil, fmt.Errorf("error scan pembayaran: %v", err)
		}
		pembayaran = append(pembayaran, p)
	}

	return pembayaran, nil
}

// GetPembayaranByID mengambil pembayaran berdasarkan ID
func GetPembayaranByID(db *sql.DB, idPembayaran int) (*models.PembayaranDetail, error) {
	var p models.PembayaranDetail
	err := db.QueryRow(`
		SELECT 
			p.id_pembayaran,
			p.id_tagihan,
			s.nama_siswa AS nama_anak,
			p.jumlah_bayar,
			p.tgl_pembayaran,
			p.status_bayar,
			p.created_at
		FROM pembayaran p
		LEFT JOIN tagihan t ON p.id_tagihan = t.id_tagihan
		LEFT JOIN siswa s ON t.nomor_induk_siswa = s.nomor_induk_siswa
		WHERE p.id_pembayaran = ?
	`, idPembayaran).Scan(
		&p.IDPembayaran,
		&p.IDTagihan,
		&p.NamaAnak,
		&p.JumlahBayar,
		&p.TglPembayaran,
		&p.StatusBayar,
		&p.CreatedAt,
	)
	if err != nil {
		return nil, fmt.Errorf("error query pembayaran: %v", err)
	}

	return &p, nil
}

// GetPembayaranByTagihan mengambil pembayaran berdasarkan tagihan
func GetPembayaranByTagihan(db *sql.DB, idTagihan int) ([]models.PembayaranDetail, error) {
	rows, err := db.Query(`
		SELECT 
			p.id_pembayaran,
			p.id_tagihan,
			s.nama_siswa AS nama_anak,
			p.jumlah_bayar,
			p.tgl_pembayaran,
			p.status_bayar,
			p.created_at
		FROM pembayaran p
		LEFT JOIN tagihan t ON p.id_tagihan = t.id_tagihan
		LEFT JOIN siswa s ON t.nomor_induk_siswa = s.nomor_induk_siswa
		WHERE p.id_tagihan = ?
		ORDER BY p.created_at DESC
	`, idTagihan)
	if err != nil {
		return nil, fmt.Errorf("error query pembayaran: %v", err)
	}
	defer rows.Close()

	var pembayaran []models.PembayaranDetail
	for rows.Next() {
		var p models.PembayaranDetail
		err := rows.Scan(
			&p.IDPembayaran,
			&p.IDTagihan,
			&p.NamaAnak,
			&p.JumlahBayar,
			&p.TglPembayaran,
			&p.StatusBayar,
			&p.CreatedAt,
		)
		if err != nil {
			return nil, fmt.Errorf("error scan pembayaran: %v", err)
		}
		pembayaran = append(pembayaran, p)
	}

	return pembayaran, nil
}

// UpdatePembayaranStatus update status pembayaran
func UpdatePembayaranStatus(db *sql.DB, idPembayaran int, statusBayar string) error {
	var err error
	if statusBayar == "diterima" {
		_, err = db.Exec(
			"UPDATE pembayaran SET status_bayar = ?, tgl_pembayaran = CURDATE(), paid_at = NOW(), updated_at = NOW() WHERE id_pembayaran = ?",
			statusBayar,
			idPembayaran,
		)
	} else {
		_, err = db.Exec(
			"UPDATE pembayaran SET status_bayar = ?, updated_at = NOW() WHERE id_pembayaran = ?",
			statusBayar,
			idPembayaran,
		)
	}
	if err != nil {
		return fmt.Errorf("error update pembayaran: %v", err)
	}

	// Update status tagihan jika pembayaran sudah lunas
	var idTagihan int
	err = db.QueryRow("SELECT id_tagihan FROM pembayaran WHERE id_pembayaran = ?", idPembayaran).Scan(&idTagihan)
	if err != nil {
		return fmt.Errorf("error get id tagihan: %v", err)
	}

	// Check apakah semua pembayaran sudah diterima
	var totalTagihan float64
	var totalBayar float64
	var periode string
	var status string
	err = db.QueryRow("SELECT jumlah_tagihan, periode, status FROM tagihan WHERE id_tagihan = ?", idTagihan).Scan(&totalTagihan, &periode, &status)
	if err != nil {
		return fmt.Errorf("error get total tagihan: %v", err)
	}
	totalWajibBayar := utils.TotalWithLateFee(totalTagihan, periode, status, time.Now())

	err = db.QueryRow(
		"SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayaran WHERE id_tagihan = ? AND status_bayar = 'diterima'",
		idTagihan,
	).Scan(&totalBayar)
	if err != nil {
		return fmt.Errorf("error get total bayar: %v", err)
	}

	// Update status tagihan
	if totalBayar >= totalWajibBayar {
		_, err = db.Exec(
			"UPDATE tagihan SET status = 'lunas', payment_status = 'lunas', payment_date = COALESCE(payment_date, NOW()), updated_at = NOW() WHERE id_tagihan = ?",
			idTagihan,
		)
		if err != nil {
			return fmt.Errorf("error update tagihan status: %v", err)
		}
	}

	return nil
}
