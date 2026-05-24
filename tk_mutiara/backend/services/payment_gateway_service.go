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
	"net/url"
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
	fmt.Printf("\n=== GET FCM TOKEN BY ORDER ID ===\n")
	fmt.Printf("Order ID: %s\n", orderID)
	
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
		if err == sql.ErrNoRows {
			fmt.Printf("❌ Order ID '%s' tidak ditemukan di database\n", orderID)
			fmt.Printf("⚠ Debug: Cek apakah pembayaran sudah disimpan dengan midtrans_order_id\n")
		} else {
			fmt.Printf("❌ Database error: %v\n", err)
		}
		return "", fmt.Errorf("gagal ambil fcm token: %w", err)
	}
	
	if !fcmToken.Valid || strings.TrimSpace(fcmToken.String) == "" {
		fmt.Printf("❌ FCM token kosong/null untuk order %s\n", orderID)
		fmt.Printf("⚠ Debug: User mungkin belum save FCM token setelah login\n")
		return "", fmt.Errorf("fcm token tidak tersedia")
	}
	
	fmt.Printf("✓ FCM token ditemukan: %s...\n", fcmToken.String[:min(20, len(fcmToken.String))])
	return fcmToken.String, nil
}

func getAccessToken() (string, error) {
	fmt.Printf("\n=== GET FIREBASE ACCESS TOKEN ===\n")
	
	serviceAccountPath := config.AppConfig.FCMServiceAccountPath
	if serviceAccountPath == "" {
		serviceAccountPath = "firebase-service-account.json"
	}
	fmt.Printf("Service Account Path: %s\n", serviceAccountPath)

	data, err := os.ReadFile(serviceAccountPath)
	if err != nil {
		fmt.Printf("❌ Failed to read file: %v\n", err)
		return "", fmt.Errorf("gagal baca service account: %w", err)
	}
	fmt.Printf("✓ Service account file read: %d bytes\n", len(data))

	var sa struct {
		ClientEmail string `json:"client_email"`
		PrivateKey  string `json:"private_key"`
		TokenURI    string `json:"token_uri"`
		ProjectID   string `json:"project_id"`
	}
	if err := json.Unmarshal(data, &sa); err != nil {
		fmt.Printf("❌ Failed to parse JSON: %v\n", err)
		return "", fmt.Errorf("gagal parse service account: %w", err)
	}
	fmt.Printf("✓ Service account parsed\n")
	fmt.Printf("  - Client Email: %s\n", sa.ClientEmail)
	fmt.Printf("  - Token URI: %s\n", sa.TokenURI)

	now := time.Now()
	// JWT claims HARUS sesuai Google OAuth2 spec
	claims := map[string]interface{}{
		"iss":   sa.ClientEmail,
		"sub":   sa.ClientEmail,
		"scope": "https://www.googleapis.com/auth/firebase.messaging",
		"aud":   sa.TokenURI, // Token endpoint URL
		"iat":   now.Unix(),
		"exp":   now.Add(time.Hour).Unix(),
	}

	// Create JWT header
	header := base64.RawURLEncoding.EncodeToString([]byte(`{"alg":"RS256","typ":"JWT"}`))
	claimsBytes, _ := json.Marshal(claims)
	payload := base64.RawURLEncoding.EncodeToString(claimsBytes)
	signingInput := header + "." + payload

	fmt.Printf("✓ JWT payload created\n")

	block, _ := pem.Decode([]byte(sa.PrivateKey))
	if block == nil {
		fmt.Printf("❌ Failed to decode PEM private key\n")
		return "", fmt.Errorf("gagal decode private key PEM")
	}
	fmt.Printf("✓ Private key PEM decoded\n")
	
	privateKey, err := x509.ParsePKCS8PrivateKey(block.Bytes)
	if err != nil {
		fmt.Printf("❌ Failed to parse private key: %v\n", err)
		return "", fmt.Errorf("gagal parse private key: %w", err)
	}

	rsaKey, ok := privateKey.(*rsa.PrivateKey)
	if !ok {
		fmt.Printf("❌ Private key is not RSA\n")
		return "", fmt.Errorf("private key bukan RSA")
	}
	fmt.Printf("✓ RSA private key extracted\n")

	// Sign JWT
	h := crypto.SHA256.New()
	h.Write([]byte(signingInput))
	signature, err := rsa.SignPKCS1v15(rand.Reader, rsaKey, crypto.SHA256, h.Sum(nil))
	if err != nil {
		fmt.Printf("❌ Failed to sign JWT: %v\n", err)
		return "", fmt.Errorf("gagal sign JWT: %w", err)
	}
	fmt.Printf("✓ JWT signed successfully\n")

	jwt := signingInput + "." + base64.RawURLEncoding.EncodeToString(signature)
	fmt.Printf("✓ JWT created: %s...\n", jwt[:50])

	// POST to token endpoint with proper form data
	formData := url.Values{}
	formData.Set("grant_type", "urn:ietf:params:oauth:grant-type:jwt-bearer")
	formData.Set("assertion", jwt)
	
	fmt.Printf("✓ Posting to token endpoint...\n")
	fmt.Printf("  URL: %s\n", sa.TokenURI)
	
	resp, err := http.Post(
		sa.TokenURI,
		"application/x-www-form-urlencoded",
		strings.NewReader(formData.Encode()),
	)
	if err != nil {
		fmt.Printf("❌ Failed to post to token URI: %v\n", err)
		return "", fmt.Errorf("gagal request access token: %w", err)
	}
	defer resp.Body.Close()

	respBody, _ := io.ReadAll(resp.Body)
	fmt.Printf("✓ Token endpoint responded with status: %d\n", resp.StatusCode)
	fmt.Printf("  Response: %s\n", string(respBody))

	var tokenResp struct {
		AccessToken string `json:"access_token"`
		TokenType   string `json:"token_type"`
		ExpiresIn   int    `json:"expires_in"`
	}
	if err := json.Unmarshal(respBody, &tokenResp); err != nil {
		fmt.Printf("❌ Failed to decode token response: %v\n", err)
		return "", fmt.Errorf("gagal parse token response: %w", err)
	}
	
	if tokenResp.AccessToken == "" {
		fmt.Printf("❌ Access token is empty\n")
		return "", fmt.Errorf("access token kosong")
	}

	fmt.Printf("✓✓ Access token acquired successfully!\n")
	fmt.Printf("  Token: %s...\n", tokenResp.AccessToken[:30])
	return tokenResp.AccessToken, nil
}

func sendFCMNotification(fcmToken, title, body string) error {
	fmt.Printf("\n=== SEND FCM NOTIFICATION ===\n")
	fmt.Printf("Title: %s\n", title)
	fmt.Printf("Body: %s\n", body)
	fmt.Printf("FCM Token: %s...\n", fcmToken[:min(20, len(fcmToken))])
	
	projectID := "proyek-akhir-ii-12e39"

	accessToken, err := getAccessToken()
	if err != nil {
		fmt.Printf("❌ Failed to get access token: %v\n", err)
		return fmt.Errorf("gagal dapat access token: %w", err)
	}
	fmt.Printf("✓ Access token acquired: %s...\n", accessToken[:min(30, len(accessToken))])

	payload := map[string]interface{}{
		"message": map[string]interface{}{
			"token": fcmToken,
			"notification": map[string]string{
				"title": title,
				"body":  body,
			},
			"android": map[string]interface{}{
				"priority": "high",
				"notification": map[string]interface{}{
					"sound":               "default",
					"click_action":        "FLUTTER_NOTIFICATION_CLICK",
					"channel_id":          "payment_channel",
					"tag":                 "payment",
					"color":               "#FF7A00",
				},
			},
			"data": map[string]string{
				"type": "payment_success",
			},
		},
	}

	bodyBytes, err := json.Marshal(payload)
	if err != nil {
		fmt.Printf("❌ Failed to encode payload: %v\n", err)
		return fmt.Errorf("gagal encode FCM payload: %w", err)
	}
	fmt.Printf("✓ Payload encoded: %d bytes\n", len(bodyBytes))

	url := fmt.Sprintf("https://fcm.googleapis.com/v1/projects/%s/messages:send", projectID)
	req, err := http.NewRequest(http.MethodPost, url, bytes.NewBuffer(bodyBytes))
	if err != nil {
		fmt.Printf("❌ Failed to create request: %v\n", err)
		return fmt.Errorf("gagal membuat request FCM: %w", err)
	}

	req.Header.Set("Authorization", "Bearer "+accessToken)
	req.Header.Set("Content-Type", "application/json")
	fmt.Printf("✓ Request prepared for: %s\n", url)

	client := &http.Client{Timeout: 10 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		fmt.Printf("❌ Request failed: %v\n", err)
		return fmt.Errorf("gagal mengirim FCM: %w", err)
	}
	defer resp.Body.Close()

	respBytes, _ := io.ReadAll(resp.Body)
	fmt.Printf("✓ Response Status: %d\n", resp.StatusCode)
	fmt.Printf("✓ Response Body: %s\n", string(respBytes))
	
	if resp.StatusCode < 200 || resp.StatusCode >= 300 {
		fmt.Printf("❌ FCM API returned error %d\n", resp.StatusCode)
		
		// Parse error detail dari FCM response
		var fcmError struct {
			Error struct {
				Code    int    `json:"code"`
				Message string `json:"message"`
				Status  string `json:"status"`
				Details []struct {
					ErrorCode string `json:"errorCode"`
				} `json:"details"`
			} `json:"error"`
		}
		if err := json.Unmarshal(respBytes, &fcmError); err == nil {
			fmt.Printf("   → Error Code: %d\n", fcmError.Error.Code)
			fmt.Printf("   → Error Status: %s\n", fcmError.Error.Status)
			fmt.Printf("   → Error Message: %s\n", fcmError.Error.Message)
			for _, d := range fcmError.Error.Details {
				if d.ErrorCode != "" {
					fmt.Printf("   → FCM Error Code: %s\n", d.ErrorCode)
					if d.ErrorCode == "UNREGISTERED" {
						fmt.Printf("   ⚠ FCM token sudah expired/invalid! User perlu re-login agar token diperbarui.\n")
					}
				}
			}
		}
		
		return fmt.Errorf("FCM response %d: %s", resp.StatusCode, string(respBytes))
	}

	fmt.Printf("✓✓ FCM notification sent successfully!\n")
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
	fmt.Printf("\n\n╔════════════════════════════════════════════════════════════╗\n")
	fmt.Printf("║      PROCESS MIDTRANS PAYMENT STATUS                       ║\n")
	fmt.Printf("╚════════════════════════════════════════════════════════════╝\n")
	fmt.Printf("Order ID: %s\n", payload.OrderID)
	fmt.Printf("Transaction Status: %s\n", payload.TransactionStatus)
	fmt.Printf("Fraud Status: %s\n", payload.FraudStatus)
	fmt.Printf("Payment Type: %s\n", payload.PaymentType)
	
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
		fmt.Printf("❌ Failed to update pembayaran status: %v\n", err)
		return err
	}
	fmt.Printf("✓ Pembayaran status updated\n")

	if isMidtransSuccess(payload.TransactionStatus, payload.FraudStatus) {
		fmt.Printf("✓✓ Payment marked as SUCCESS - will send notification\n")
		
		if err := repository.MarkPembayaranLunasByOrderID(db, payload.OrderID); err != nil {
			fmt.Printf("❌ Failed to mark as lunas: %v\n", err)
			return err
		}
		fmt.Printf("✓ Pembayaran marked as LUNAS\n")

		fcmToken, err := getFcmTokenByOrderID(db, payload.OrderID)
		if err != nil {
			fmt.Printf("⚠ Could not get FCM token: %v\n", err)
			fmt.Printf("⚠ Notification WILL NOT BE SENT\n")
		} else {
			fmt.Printf("✓ FCM token obtained, attempting to send notification...\n")
			if err := sendFCMNotification(
				fcmToken,
				"Pembayaran Berhasil ✅",
				fmt.Sprintf("Pembayaran SPP untuk order %s telah dikonfirmasi.", payload.OrderID),
			); err != nil {
				fmt.Printf("❌ Failed to send FCM notification: %v\n", err)
			} else {
				fmt.Printf("✓✓✓ Notification sent successfully!\n")
			}
		}
	} else {
		fmt.Printf("⚠ Payment not successful yet (status: %s, fraud: %s)\n", payload.TransactionStatus, payload.FraudStatus)
	}

	if err := repository.SyncTagihanStatusByOrderID(db, payload.OrderID); err != nil {
		fmt.Printf("⚠ Failed to sync tagihan status: %v\n", err)
		return err
	}
	fmt.Printf("✓ Tagihan status synced\n")
	fmt.Printf("╔════════════════════════════════════════════════════════════╗\n")
	fmt.Printf("║         PROCESS COMPLETE                                   ║\n")
	fmt.Printf("╚════════════════════════════════════════════════════════════╝\n\n")

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
