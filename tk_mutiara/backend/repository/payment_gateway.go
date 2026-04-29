package repository

import (
	"database/sql"
	"fmt"
	"tk_mutiara_backend/models"
)

// GetTagihanForPaymentByIDAndSiswa mengambil detail tagihan milik siswa untuk payment flow.
func GetTagihanForPaymentByIDAndSiswa(db *sql.DB, idTagihan int, nomorIndukSiswa string) (*models.TagihanForPayment, error) {
	var item models.TagihanForPayment

	err := db.QueryRow(`
		SELECT
			t.id_tagihan,
			t.nomor_induk_siswa,
			s.nama_siswa,
			s.nama_orgtua,
			t.periode,
			t.jumlah_tagihan,
			t.status
		FROM tagihan t
		JOIN siswa s ON s.nomor_induk_siswa = t.nomor_induk_siswa
		WHERE t.id_tagihan = ? AND t.nomor_induk_siswa = ?
	`, idTagihan, nomorIndukSiswa).Scan(
		&item.IDTagihan,
		&item.NomorIndukSiswa,
		&item.NamaSiswa,
		&item.NamaOrangtua,
		&item.Periode,
		&item.JumlahTagihan,
		&item.StatusTagihan,
	)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, fmt.Errorf("tagihan tidak ditemukan")
		}
		return nil, fmt.Errorf("gagal mengambil data tagihan: %w", err)
	}

	return &item, nil
}

// CreatePembayaranPending membuat row pembayaran pending untuk menunggu webhook Midtrans.
func CreatePembayaranPending(db *sql.DB, idTagihan int, jumlahBayar float64, orderID string) (int64, error) {
	result, err := db.Exec(`
		INSERT INTO pembayaran (
			id_tagihan,
			jumlah_bayar,
			tgl_pembayaran,
			status_bayar,
			midtrans_order_id,
			created_at,
			updated_at
		) VALUES (?, ?, CURDATE(), 'menunggu', ?, NOW(), NOW())
	`, idTagihan, jumlahBayar, orderID)
	if err != nil {
		return 0, fmt.Errorf("gagal membuat pembayaran pending: %w", err)
	}

	id, err := result.LastInsertId()
	if err != nil {
		return 0, fmt.Errorf("gagal membaca id pembayaran: %w", err)
	}

	return id, nil
}

// UpdatePembayaranSnapResponse menyimpan token snap saat transaksi dibuat.
func UpdatePembayaranSnapResponse(db *sql.DB, idPembayaran int64, snapToken, redirectURL string) error {
	_, err := db.Exec(`
		UPDATE pembayaran
		SET snap_token = ?,
			snap_redirect_url = ?,
			updated_at = NOW()
		WHERE id_pembayaran = ?
	`, snapToken, redirectURL, idPembayaran)
	if err != nil {
		return fmt.Errorf("gagal update snap response: %w", err)
	}

	return nil
}

// UpdatePembayaranMidtransStatus memperbarui metadata status Midtrans tanpa mengubah status bisnis tagihan.
func UpdatePembayaranMidtransStatus(db *sql.DB, orderID, txID, txStatus, paymentType, fraudStatus, rawResponse string) error {
	result, err := db.Exec(`
		UPDATE pembayaran
		SET midtrans_transaction_id = ?,
			midtrans_transaction_status = ?,
			midtrans_payment_type = ?,
			midtrans_fraud_status = ?,
			midtrans_raw_response = ?,
			updated_at = NOW()
		WHERE midtrans_order_id = ?
	`, txID, txStatus, paymentType, fraudStatus, rawResponse, orderID)
	if err != nil {
		return fmt.Errorf("gagal update status midtrans: %w", err)
	}

	affected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("gagal membaca rows affected: %w", err)
	}
	if affected == 0 {
		return fmt.Errorf("pembayaran dengan order id %s tidak ditemukan", orderID)
	}
	return nil
}

// MarkPembayaranLunasByOrderID menandai pembayaran diterima dan mencatat waktu bayar.
func MarkPembayaranLunasByOrderID(db *sql.DB, orderID string) error {
	result, err := db.Exec(`
		UPDATE pembayaran
		SET status_bayar = 'diterima',
			tgl_pembayaran = CURDATE(),
			paid_at = NOW(),
			updated_at = NOW()
		WHERE midtrans_order_id = ?
	`, orderID)
	if err != nil {
		return fmt.Errorf("gagal update pembayaran lunas: %w", err)
	}

	affected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("gagal membaca rows affected: %w", err)
	}
	if affected == 0 {
		return fmt.Errorf("pembayaran dengan order id %s tidak ditemukan", orderID)
	}

	return nil
}

// SyncTagihanStatusByOrderID menyinkronkan status tagihan jadi lunas jika total pembayaran diterima sudah cukup.
func SyncTagihanStatusByOrderID(db *sql.DB, orderID string) error {
	var idTagihan int
	err := db.QueryRow("SELECT id_tagihan FROM pembayaran WHERE midtrans_order_id = ? LIMIT 1", orderID).Scan(&idTagihan)
	if err != nil {
		if err == sql.ErrNoRows {
			return fmt.Errorf("pembayaran tidak ditemukan")
		}
		return fmt.Errorf("gagal mengambil id tagihan: %w", err)
	}

	var jumlahTagihan float64
	err = db.QueryRow("SELECT jumlah_tagihan FROM tagihan WHERE id_tagihan = ?", idTagihan).Scan(&jumlahTagihan)
	if err != nil {
		return fmt.Errorf("gagal mengambil jumlah tagihan: %w", err)
	}

	var totalDiterima float64
	err = db.QueryRow(`
		SELECT COALESCE(SUM(jumlah_bayar), 0)
		FROM pembayaran
		WHERE id_tagihan = ? AND status_bayar = 'diterima'
	`, idTagihan).Scan(&totalDiterima)
	if err != nil {
		return fmt.Errorf("gagal menghitung total pembayaran: %w", err)
	}

	newStatus := "belum_bayar"
	if totalDiterima >= jumlahTagihan {
		newStatus = "lunas"
	}

	_, err = db.Exec("UPDATE tagihan SET status = ?, updated_at = NOW() WHERE id_tagihan = ?", newStatus, idTagihan)
	if err != nil {
		return fmt.Errorf("gagal sinkronisasi status tagihan: %w", err)
	}

	return nil
}

// GetPaymentStatusByTagihanAndSiswa untuk endpoint polling app parent.
func GetPaymentStatusByTagihanAndSiswa(db *sql.DB, idTagihan int, nomorIndukSiswa string) (*models.PaymentStatusResponse, error) {
	var item models.PaymentStatusResponse

	err := db.QueryRow(`
		SELECT
			t.id_tagihan,
			t.status,
			COALESCE(p.id_pembayaran, 0) AS id_pembayaran,
			COALESCE(p.midtrans_order_id, '') AS midtrans_order_id,
			COALESCE(p.status_bayar, 'menunggu') AS status_bayar,
			t.jumlah_tagihan,
			COALESCE(p.jumlah_bayar, 0) AS jumlah_bayar
		FROM tagihan t
		LEFT JOIN pembayaran p ON p.id_pembayaran = (
			SELECT px.id_pembayaran
			FROM pembayaran px
			WHERE px.id_tagihan = t.id_tagihan
			ORDER BY px.id_pembayaran DESC
			LIMIT 1
		)
		WHERE t.id_tagihan = ? AND t.nomor_induk_siswa = ?
	`, idTagihan, nomorIndukSiswa).Scan(
		&item.IDTagihan,
		&item.StatusTagihan,
		&item.IDPembayaran,
		&item.OrderID,
		&item.StatusBayar,
		&item.JumlahTagihan,
		&item.JumlahBayar,
	)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, fmt.Errorf("tagihan tidak ditemukan")
		}
		return nil, fmt.Errorf("gagal mengambil status pembayaran: %w", err)
	}

	return &item, nil
}
