package services

import (
	"bytes"
	"crypto"
	"crypto/rand"
	"crypto/rsa"
	"crypto/sha512"
	"crypto/x509"
	"database/sql"
	"encoding/base64"
	"encoding/hex"
	"encoding/json"
	"encoding/pem"
	"fmt"
	"io"
	"net/http"
	"os"
	"strings"
	"time"

	"tk_mutiara_backend/config"
	"tk_mutiara_backend/models"
	"tk_mutiara_backend/repository"
)

type midtransSnapRequest struct {
	TransactionDetails midtransTransactionDetails `json:"transaction_details"`
	CustomerDetails    midtransCustomerDetails    `json:"customer_details"`
	ItemDetails        []midtransItemDetail       `json:"item_details"`
}

type midtransTransactionDetails struct {
	OrderID     string  `json:"order_id"`
	GrossAmount float64 `json:"gross_amount"`
}

type midtransCustomerDetails struct {
	FirstName string `json:"first_name"`
}

type midtransItemDetail struct {
	ID       string  `json:"id"`
	Price    float64 `json:"price"`
	Quantity int     `json:"quantity"`
	Name     string  `json:"name"`
}

type midtransSnapResponse struct {
	Token       string `json:"token"`
	RedirectURL string `json:"redirect_url"`
}

func CreateMidtransTransaction(db *sql.DB, nomorIndukSiswa string, idTagihan int) (*models.CreateMidtransTransactionResponse, error) {
	if strings.TrimSpace(config.AppConfig.MidtransServerKey) == "" {
		return nil, fmt.Errorf("MIDTRANS_SERVER_KEY belum diatur")
	}

	tagihan, err := repository.GetTagihanForPaymentByIDAndSiswa(db, idTagihan, nomorIndukSiswa)
	if err != nil {
		return nil, err
	}

	if tagihan.StatusTagihan == "lunas" {
		return nil, fmt.Errorf("tagihan sudah lunas")
	}

	orderID := fmt.Sprintf("TAGIHAN-%d-%d", tagihan.IDTagihan, time.Now().Unix())

	payload := midtransSnapRequest{
		TransactionDetails: midtransTransactionDetails{
			OrderID:     orderID,
			GrossAmount: tagihan.JumlahTagihan,
		},
		CustomerDetails: midtransCustomerDetails{
			FirstName: strings.TrimSpace(tagihan.NamaOrangtua),
		},
		ItemDetails: []midtransItemDetail{
			{
				ID:       fmt.Sprintf("tagihan-%d", tagihan.IDTagihan),
				Price:    tagihan.JumlahTagihan,
				Quantity: 1,
				Name:     fmt.Sprintf("SPP %s - %s", tagihan.Periode, tagihan.NamaSiswa),
			},
		},
	}

	if payload.CustomerDetails.FirstName == "" {
		payload.CustomerDetails.FirstName = "Orangtua"
	}

	snapResp, err := requestMidtransSnap(payload)
	if err != nil {
		return nil, err
	}

	idPembayaran, err := repository.CreatePembayaranPending(db, tagihan.IDTagihan, tagihan.JumlahTagihan, orderID)
	if err != nil {
		return nil, err
	}

	if err := repository.UpdatePembayaranSnapResponse(db, idPembayaran, snapResp.Token, snapResp.RedirectURL); err != nil {
		return nil, err
	}

	return &models.CreateMidtransTransactionResponse{
		IDTagihan:     tagihan.IDTagihan,
		IDPembayaran:  idPembayaran,
		OrderID:       orderID,
		SnapToken:     snapResp.Token,
		RedirectURL:   snapResp.RedirectURL,
		Amount:        tagihan.JumlahTagihan,
		StatusTagihan: tagihan.StatusTagihan,
		StatusBayar:   "menunggu",
		ClientKey:     config.AppConfig.MidtransClientKey,
	}, nil
}

func requestMidtransSnap(payload midtransSnapRequest) (*midtransSnapResponse, error) {
	baseURL := "https://app.sandbox.midtrans.com"
	if strings.EqualFold(config.AppConfig.MidtransEnvironment, "production") {
		baseURL = "https://app.midtrans.com"
	}

	body, err := json.Marshal(payload)
	if err != nil {
		return nil, fmt.Errorf("gagal encode payload midtrans: %w", err)
	}

	req, err := http.NewRequest(http.MethodPost, baseURL+"/snap/v1/transactions", bytes.NewBuffer(body))
	if err != nil {
		return nil, fmt.Errorf("gagal membuat request midtrans: %w", err)
	}

	auth := base64.StdEncoding.EncodeToString([]byte(config.AppConfig.MidtransServerKey + ":"))
	req.Header.Set("Authorization", "Basic "+auth)
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")

	client := &http.Client{Timeout: 20 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		return nil, fmt.Errorf("gagal menghubungi midtrans: %w", err)
	}
	defer resp.Body.Close()

	respBytes, _ := io.ReadAll(resp.Body)
	if resp.StatusCode < 200 || resp.StatusCode >= 300 {
		return nil, fmt.Errorf("midtrans response %d: %s", resp.StatusCode, string(respBytes))
	}

	var parsed midtransSnapResponse
	if err := json.Unmarshal(respBytes, &parsed); err != nil {
		return nil, fmt.Errorf("gagal parsing response midtrans: %w", err)
	}
	if strings.TrimSpace(parsed.Token) == "" {
		return nil, fmt.Errorf("snap token tidak diterima dari midtrans")
	}

	return &parsed, nil
}

func getFcmTokenByOrderID(db *sql.DB, orderID string) (string, error) {
	query := `
		SELECT a.fcm_token
		FROM akun a
		JOIN siswa s ON a.nomor_induk_siswa = s.nomor_induk_siswa
		JOIN tagihan t ON t.nomor_induk_siswa = s.nomor_induk_siswa
		JOIN pembayaran p ON p.id_tagihan = t.id_tagihan
		WHERE p.midtrans_order_id = ?
		LIMIT 1
	`
	var fcmToken sql.NullString
	err := db.QueryRow(query, orderID).Scan(&fcmToken)
	if err != nil {
		return "", fmt.Errorf("gagal ambil fcm token: %w", err)
	}
	if !fcmToken.Valid || strings.TrimSpace(fcmToken.String) == "" {
		return "", fmt.Errorf("fcm token tidak tersedia")
	}
	return fcmToken.String, nil
}

func getAccessToken() (string, error) {
	serviceAccountPath := config.AppConfig.FCMServiceAccountPath
	if serviceAccountPath == "" {
		serviceAccountPath = "firebase-service-account.json"
	}

	data, err := os.ReadFile(serviceAccountPath)
	if err != nil {
		return "", fmt.Errorf("gagal baca service account: %w", err)
	}

	var sa struct {
		ClientEmail string `json:"client_email"`
		PrivateKey  string `json:"private_key"`
		TokenURI    string `json:"token_uri"`
	}
	if err := json.Unmarshal(data, &sa); err != nil {
		return "", fmt.Errorf("gagal parse service account: %w", err)
	}

	now := time.Now()
	claims := map[string]interface{}{
		"iss":   sa.ClientEmail,
		"scope": "https://www.googleapis.com/auth/firebase.messaging",
		"aud":   sa.TokenURI,
		"iat":   now.Unix(),
		"exp":   now.Add(time.Hour).Unix(),
	}

	header := base64.RawURLEncoding.EncodeToString([]byte(`{"alg":"RS256","typ":"JWT"}`))
	claimsBytes, _ := json.Marshal(claims)
	payload := base64.RawURLEncoding.EncodeToString(claimsBytes)
	signingInput := header + "." + payload

	block, _ := pem.Decode([]byte(sa.PrivateKey))
	if block == nil {
		return "", fmt.Errorf("gagal decode private key PEM")
	}
	privateKey, err := x509.ParsePKCS8PrivateKey(block.Bytes)
	if err != nil {
		return "", fmt.Errorf("gagal parse private key: %w", err)
	}

	rsaKey, ok := privateKey.(*rsa.PrivateKey)
	if !ok {
		return "", fmt.Errorf("private key bukan RSA")
	}

	h := crypto.SHA256.New()
	h.Write([]byte(signingInput))
	signature, err := rsa.SignPKCS1v15(rand.Reader, rsaKey, crypto.SHA256, h.Sum(nil))
	if err != nil {
		return "", fmt.Errorf("gagal sign JWT: %w", err)
	}

	jwt := signingInput + "." + base64.RawURLEncoding.EncodeToString(signature)

	formData := "grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Ajwt-bearer&assertion=" + jwt
	resp, err := http.Post(sa.TokenURI, "application/x-www-form-urlencoded", strings.NewReader(formData))
	if err != nil {
		return "", fmt.Errorf("gagal request access token: %w", err)
	}
	defer resp.Body.Close()

	var tokenResp struct {
		AccessToken string `json:"access_token"`
	}
	if err := json.NewDecoder(resp.Body).Decode(&tokenResp); err != nil {
		return "", fmt.Errorf("gagal parse token response: %w", err)
	}
	if tokenResp.AccessToken == "" {
		return "", fmt.Errorf("access token kosong")
	}

	return tokenResp.AccessToken, nil
}

func sendFCMNotification(fcmToken, title, body string) error {
	projectID := "proyek-akhir-ii-12e39"

	accessToken, err := getAccessToken()
	if err != nil {
		return fmt.Errorf("gagal dapat access token: %w", err)
	}

	payload := map[string]interface{}{
		"message": map[string]interface{}{
			"token": fcmToken,
			"notification": map[string]string{
				"title": title,
				"body":  body,
			},
			"data": map[string]string{
				"type": "payment_success",
			},
		},
	}

	bodyBytes, err := json.Marshal(payload)
	if err != nil {
		return fmt.Errorf("gagal encode FCM payload: %w", err)
	}

	url := fmt.Sprintf("https://fcm.googleapis.com/v1/projects/%s/messages:send", projectID)
	req, err := http.NewRequest(http.MethodPost, url, bytes.NewBuffer(bodyBytes))
	if err != nil {
		return fmt.Errorf("gagal membuat request FCM: %w", err)
	}

	req.Header.Set("Authorization", "Bearer "+accessToken)
	req.Header.Set("Content-Type", "application/json")

	client := &http.Client{Timeout: 10 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		return fmt.Errorf("gagal mengirim FCM: %w", err)
	}
	defer resp.Body.Close()

	respBytes, _ := io.ReadAll(resp.Body)
	if resp.StatusCode < 200 || resp.StatusCode >= 300 {
		return fmt.Errorf("FCM response %d: %s", resp.StatusCode, string(respBytes))
	}

	fmt.Printf("✓ FCM sent successfully: %s\n", string(respBytes))
	return nil
}

func fetchMidtransStatusAPI(orderID string) (*models.MidtransNotification, error) {
	baseURL := "https://api.sandbox.midtrans.com"
	if strings.EqualFold(config.AppConfig.MidtransEnvironment, "production") {
		baseURL = "https://api.midtrans.com"
	}

	req, err := http.NewRequest(http.MethodGet, baseURL+"/v2/"+orderID+"/status", nil)
	if err != nil {
		return nil, fmt.Errorf("gagal membuat request midtrans status: %w", err)
	}

	auth := base64.StdEncoding.EncodeToString([]byte(config.AppConfig.MidtransServerKey + ":"))
	req.Header.Set("Authorization", "Basic "+auth)
	req.Header.Set("Accept", "application/json")

	client := &http.Client{Timeout: 15 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		return nil, fmt.Errorf("gagal menghubungi midtrans status api: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode < 200 || resp.StatusCode >= 300 {
		respBytes, _ := io.ReadAll(resp.Body)
		return nil, fmt.Errorf("midtrans API response %d: %s", resp.StatusCode, string(respBytes))
	}

	var parsed models.MidtransNotification
	if err := json.NewDecoder(resp.Body).Decode(&parsed); err != nil {
		return nil, fmt.Errorf("gagal parsing response midtrans status: %w", err)
	}

	return &parsed, nil
}

func processMidtransStatus(db *sql.DB, payload *models.MidtransNotification) error {
	rawPayload, _ := json.Marshal(payload)
	if err := repository.UpdatePembayaranMidtransStatus(
		db,
		payload.OrderID,
		payload.TransactionID,
		payload.TransactionStatus,
		payload.PaymentType,
		payload.FraudStatus,
		string(rawPayload),
	); err != nil {
		return err
	}

	if isMidtransSuccess(payload.TransactionStatus, payload.FraudStatus) {
		if err := repository.MarkPembayaranLunasByOrderID(db, payload.OrderID); err != nil {
			return err
		}

		fcmToken, err := getFcmTokenByOrderID(db, payload.OrderID)
		if err != nil {
			fmt.Printf("⚠ Gagal ambil FCM token untuk order %s: %v\n", payload.OrderID, err)
		} else {
			if err := sendFCMNotification(
				fcmToken,
				"Pembayaran Berhasil ✅",
				fmt.Sprintf("Pembayaran SPP untuk order %s telah dikonfirmasi.", payload.OrderID),
			); err != nil {
				fmt.Printf("⚠ Gagal kirim FCM untuk order %s: %v\n", payload.OrderID, err)
			}
		}
	}

	if err := repository.SyncTagihanStatusByOrderID(db, payload.OrderID); err != nil {
		return err
	}

	return nil
}

func HandleMidtransWebhook(db *sql.DB, payload *models.MidtransNotification) error {
	if !isValidMidtransSignature(payload) {
		return fmt.Errorf("signature key midtrans tidak valid")
	}

	return processMidtransStatus(db, payload)
}

func isValidMidtransSignature(payload *models.MidtransNotification) bool {
	raw := payload.OrderID + payload.StatusCode + payload.GrossAmount + config.AppConfig.MidtransServerKey
	hash := sha512.Sum512([]byte(raw))
	expected := hex.EncodeToString(hash[:])
	return strings.EqualFold(expected, payload.SignatureKey)
}

func isMidtransSuccess(status, fraudStatus string) bool {
	s := strings.ToLower(strings.TrimSpace(status))
	f := strings.ToLower(strings.TrimSpace(fraudStatus))

	if s == "settlement" {
		return true
	}

	if s == "capture" {
		if f == "" || f == "accept" {
			return true
		}
	}

	return false
}

func GetPaymentStatusByTagihan(db *sql.DB, nomorIndukSiswa string, idTagihan int) (*models.PaymentStatusResponse, error) {
	if idTagihan <= 0 {
		return nil, fmt.Errorf("id tagihan tidak valid")
	}
	if strings.TrimSpace(nomorIndukSiswa) == "" {
		return nil, fmt.Errorf("nomor induk siswa tidak valid")
	}

	res, err := repository.GetPaymentStatusByTagihanAndSiswa(db, idTagihan, nomorIndukSiswa)
	if err != nil {
		return nil, err
	}

	if res.StatusTagihan != "lunas" && res.OrderID != "" {
		midtransStatus, errAPI := fetchMidtransStatusAPI(res.OrderID)
		if errAPI == nil && midtransStatus != nil && midtransStatus.TransactionStatus != "" {
			_ = processMidtransStatus(db, midtransStatus)

			newRes, errDB := repository.GetPaymentStatusByTagihanAndSiswa(db, idTagihan, nomorIndukSiswa)
			if errDB == nil {
				return newRes, nil
			}
		}
	}

	return res, nil
}
