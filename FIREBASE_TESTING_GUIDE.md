# Firebase Notification Testing Guide

## 🔥 Quick Fixes Applied (May 23, 2026)

### Changes Summary
1. **Flutter FCM Token Saving** - Now has retry logic (max 3 attempts) with detailed logging
2. **Backend FCM Handler** - Added comprehensive error logging to identify issues
3. **Firebase Service** - Complete logging pipeline for JWT generation → FCM API → notification delivery

---

## 📋 TESTING CHECKLIST

### Phase 1: Backend Verification
```
✓ Go build successful
✓ Backend running on :8081
✓ Logs show detailed output
```

### Phase 2: Flutter Login Flow
```
User: siswa@example.com
Pass: siswa123

Expected logs in Flutter console:
✓ FCM Token dari Firebase: [token...]
✓ SAVE FCM TOKEN (Attempt 1/3)
✓ Response Status: 200
✓ FCM token berhasil disimpan ke server
```

### Phase 3: Backend FCM Token Handler
```
Expected backend logs:
✓ === SAVE FCM TOKEN REQUEST ===
✓ User ID: [id]
✓ FCM Token: [token...]...
✓ Update successful - 1 row(s) affected
```

### Phase 4: Complete Payment Flow
```
1. Login as siswa with valid credentials
2. Navigate to SPP/Tagihan section
3. Complete payment via Midtrans
4. Confirm payment in Midtrans sandbox dashboard

Expected backend logs:
✓ ╔════════════════════════════════════════════════════════════╗
✓ ║      PROCESS MIDTRANS PAYMENT STATUS                       ║
✓ ║      PROCESS COMPLETE                                      ║
✓ ╚════════════════════════════════════════════════════════════╝

✓ === GET FCM TOKEN BY ORDER ID ===
✓ ✓ FCM token ditemukan: [token...]

✓ === GET FIREBASE ACCESS TOKEN ===
✓ ✓ Access token acquired successfully!

✓ === SEND FCM NOTIFICATION ===
✓ ✓✓ FCM notification sent successfully!
```

### Phase 5: Verify in Flutter App
```
Expected notification appearance:
- Notification title: "Pembayaran Berhasil ✅"
- Notification body: "Pembayaran SPP untuk order [ID] telah dikonfirmasi."

Notification should appear:
- Foreground: In-app notification popup
- Background: System notification in notification tray
- Killed: System notification in notification tray
```

---

## 🔍 DEBUGGING - If Something Fails

### Issue: "❌ User tidak ditemukan" in backend
```
Cause: User ID not passed to SaveFcmTokenHandler
Check: Authorization header & JWT token validity
Solution: Ensure middleware properly extracts user_id
```

### Issue: "❌ FCM token kosong/null untuk order" 
```
Cause: FCM token not saved in database OR incorrect order ID
Debug:
  1. Check akun.fcm_token in database:
     SELECT id_akun, nomor_induk_siswa, fcm_token FROM akun WHERE fcm_token IS NOT NULL;
  
  2. Check pembayaran table:
     SELECT id_pembayaran, midtrans_order_id, id_tagihan FROM pembayaran WHERE midtrans_order_id = 'ORDER_ID';
```

### Issue: "❌ Failed to get access token"
```
Cause: Firebase service account file missing or invalid JSON
Check: 
  1. File exists: tk_mutiara/backend/proyek-akhir-ii-12e39-firebase-adminsdk-fbsvc-bf8132898f.json
  2. File is valid JSON
  3. .env has correct path: FIREBASE_SERVICE_ACCOUNT_PATH=...
```

### Issue: "FCM response 400: ..." in logs
```
Cause: Firebase project ID mismatch or invalid FCM token
Check:
  1. Project ID in code: proyek-akhir-ii-12e39
  2. Firebase console project matches
  3. Android app SHA-1 fingerprint registered
```

---

## 📊 LOGGING OUTPUT INTERPRETATION

### ✅ Success Pattern (what to look for)
```
✓✓✓ FCM notification sent successfully!
✓ Response Body: {"name":"projects/proyek-akhir-ii-12e39/messages/..."}
```

### ❌ Failure Patterns (what to avoid)
```
❌ No logs at all → processMidtransStatus() not called
❌ "gagal ambil FCM token" → Database query failed
❌ "⚠ Debug: User mungkin belum save FCM token" → FCM token saving failed during login
❌ "400 Bad Request" → Firebase credentials issue
```

---

## 🚀 QUICK FIX VERIFICATION

### 1. Backend Compilation
```bash
cd tk_mutiara/backend
go build -o tk_mutiara_backend.exe .
```
✅ Result: Executable created successfully

### 2. Flutter Analysis  
```bash
cd tk_mutiara
flutter analyze --no-pub
```
✅ Result: No critical errors (only linting warnings)

---

## 📝 TEST SCENARIOS

### Scenario 1: Happy Path
```
1. App launches → WelcomeScreen with 3-second delay
2. User logs in → FCM token automatically saved
3. User pays → Backend receives webhook
4. Notification appears on device
```

### Scenario 2: Network Timeout Recovery
```
1. User logs in, network temporarily fails
2. saveFcmToken() retries after 2 seconds
3. Eventually succeeds (or fails after max 3 attempts)
4. Logs show retry attempts
```

### Scenario 3: User Already Paid (Edge Case)
```
1. User attempts payment already marked as "lunas"
2. Webhook still received but notification may not send
3. Check logs for: "Payment not successful yet"
```

---

## ⚙️ ENVIRONMENT VARIABLES TO VERIFY

### .env file (tk_mutiara/backend/.env)
```
FIREBASE_SERVICE_ACCOUNT_PATH=proyek-akhir-ii-12e39-firebase-adminsdk-fbsvc-bf8132898f.json
MIDTRANS_SERVER_KEY=Mid-server-...
MIDTRANS_IS_PRODUCTION=false
```

### API_BASE_URL (Flutter)
```
http://192.168.83.220:8081  ← Backend address
```

---

## 📞 SUPPORT CHECKLIST

Before declaring "Firebase fixed", verify:
- [x] Backend compiles without errors
- [x] Flutter app compiles without critical errors  
- [x] Firebase service account file exists and readable
- [x] FCM token saving has retry logic
- [ ] Login flow shows FCM token saving logs
- [ ] Payment completion shows all 5 notification sending stages
- [ ] Device receives notification (foreground + background)
- [ ] Database has non-null fcm_token values after login

---

## 🔗 KEY FILES MODIFIED

1. **tk_mutiara/lib/services/api_services.dart**
   - saveFcmToken() with retry logic
   - Background save after login

2. **tk_mutiara/backend/handlers/profile_handler.go**
   - SaveFcmTokenHandler with detailed logging
   - Row affected check

3. **tk_mutiara/backend/services/payment_gateway_service.go**
   - getAccessToken() with step logging
   - getFcmTokenByOrderID() with debug info
   - sendFCMNotification() with full request/response logging
   - processMidtransStatus() with ASCII section headers

---

Generated: May 23, 2026
Status: ✅ All quick fixes implemented and compiled successfully
