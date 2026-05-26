# Dokumentasi Implementasi Firebase

Dokumen ini menjelaskan penerapan Firebase pada aplikasi TK Mutiara, khususnya untuk Firebase Cloud Messaging atau FCM.

## Tujuan

Firebase digunakan untuk:

- Inisialisasi Firebase pada aplikasi Flutter.
- Mengambil FCM token perangkat.
- Menyimpan FCM token ke backend setelah user login.
- Menerima notifikasi foreground dan background.
- Menampilkan notifikasi lokal saat pesan FCM diterima.
- Memperbarui data pembayaran ketika notifikasi pembayaran sukses diterima.

## Dependency

File:

`pubspec.yaml`

Dependency:

```yaml
firebase_core: ^4.7.0
firebase_messaging: ^16.2.0
flutter_local_notifications: ^17.2.4
```

Fungsi:

- `firebase_core`: inisialisasi Firebase.
- `firebase_messaging`: menerima FCM notification.
- `flutter_local_notifications`: menampilkan notifikasi lokal di perangkat.

## File Konfigurasi Firebase

File Android:

`android/app/google-services.json`

File ini berisi konfigurasi Firebase untuk aplikasi Android.

Gradle plugin:

`android/app/build.gradle.kts`

```kotlin
id("com.google.gms.google-services")
```

Project Gradle:

`android/build.gradle.kts`

```kotlin
classpath("com.google.gms:google-services:4.4.2")
```

## 1. Inisialisasi Firebase

File:

`lib/main.dart`

Implementasi:

```dart
await Firebase.initializeApp();
FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
await ApiService.loadSession();
await NotificationService.init();
```

Penjelasan:

- Firebase diinisialisasi sebelum aplikasi dijalankan.
- Background handler didaftarkan untuk menangani pesan saat aplikasi berjalan di background.
- Session user dimuat terlebih dahulu.
- Notification service diinisialisasi.

## 2. Background Message Handler

File:

`lib/services/notification_service.dart`

Implementasi:

```dart
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
}
```

Penjelasan:

Handler ini berjalan ketika aplikasi menerima FCM saat background. Di dalamnya Firebase diinisialisasi kembali agar service dapat berjalan di isolate background.

## 3. NotificationService

File:

`lib/services/notification_service.dart`

Instance Firebase Messaging:

```dart
static final FirebaseMessaging _messaging = FirebaseMessaging.instance;
```

Fungsi:

Object `_messaging` digunakan untuk request permission, mengambil token FCM, mendengarkan token refresh, dan mendengarkan pesan foreground.

## 4. Request Permission Notification

File:

`lib/services/notification_service.dart`

Implementasi:

```dart
NotificationSettings settings = await _messaging.requestPermission(
  alert: true,
  badge: true,
  sound: true,
  provisional: false,
);
```

Penjelasan:

Kode ini meminta izin kepada user agar aplikasi dapat menampilkan notifikasi.

## 5. Local Notification

File:

`lib/services/notification_service.dart`

Implementasi:

```dart
final FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin =
    FlutterLocalNotificationsPlugin();
```

Channel Android:

```dart
const AndroidNotificationChannel(
  _channelId,
  _channelName,
  description: _channelDesc,
  importance: Importance.high,
)
```

Penjelasan:

`flutter_local_notifications` digunakan agar notifikasi tetap dapat ditampilkan dengan baik, terutama saat aplikasi foreground.

## 6. Mengambil FCM Token

File:

`lib/services/notification_service.dart`

Implementasi:

```dart
final String? token = await _messaging.getToken();
```

Penjelasan:

FCM token adalah identitas perangkat yang digunakan backend untuk mengirim notifikasi ke user tertentu.

## 7. Simpan FCM Token Setelah Login

File:

`lib/services/api_services.dart`

Setelah login berhasil:

```dart
await NotificationService.saveTokenAfterLogin();
```

File:

`lib/services/notification_service.dart`

Implementasi:

```dart
static Future<void> saveTokenAfterLogin() async {
  final String? fcmToken = await _messaging.getToken();
  if (fcmToken != null) {
    await ApiService.saveFcmToken(fcmToken);
  }
}
```

Penjelasan:

Token FCM baru disimpan ke backend setelah user berhasil login agar backend tahu token tersebut milik akun yang mana.

## 8. API Simpan FCM Token

File:

`lib/services/api_services.dart`

Implementasi:

```dart
static Future<void> saveFcmToken(String fcmToken, {int retryCount = 0}) async
```

Endpoint:

```text
POST /api/user/fcm-token
```

Body:

```dart
jsonEncode({'fcm_token': fcmToken})
```

Penjelasan:

Token dikirim ke backend menggunakan auth token user.

## 9. Token Refresh Listener

File:

`lib/services/notification_service.dart`

Implementasi:

```dart
_messaging.onTokenRefresh.listen((String newToken) {
  if (ApiService.token != null) {
    ApiService.saveFcmToken(newToken);
  }
});
```

Penjelasan:

Jika token FCM berubah, aplikasi menyimpan token baru ke backend selama user sudah login.

## 10. Foreground Message

File:

`lib/services/notification_service.dart`

Implementasi:

```dart
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  flutterLocalNotificationsPlugin.show(...);
});
```

Penjelasan:

Saat aplikasi sedang dibuka dan menerima FCM, notifikasi tetap ditampilkan menggunakan local notification.

## 11. Notifikasi Pembayaran Di Dashboard

File:

`lib/screens/dashboard_screen.dart`

Implementasi:

```dart
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  final data = message.data;
  if (data['type'] == 'payment_success') {
    _loadData();
    ApiService.notifyPaymentUpdated();
  }
});
```

Penjelasan:

Jika notifikasi bertipe `payment_success`, dashboard memuat ulang data pembayaran dan menampilkan feedback kepada user.

## Alur Firebase FCM

```text
Aplikasi dibuka
        ↓
Firebase.initializeApp()
        ↓
NotificationService.init()
        ↓
Request permission notification
        ↓
Ambil FCM token
        ↓
User login
        ↓
saveTokenAfterLogin()
        ↓
Token dikirim ke backend
        ↓
Backend dapat mengirim notifikasi ke perangkat user
```

## Kesimpulan

Firebase pada aplikasi ini digunakan untuk fitur push notification. Implementasi utama berada di `main.dart`, `notification_service.dart`, `api_services.dart`, dan `dashboard_screen.dart`.
