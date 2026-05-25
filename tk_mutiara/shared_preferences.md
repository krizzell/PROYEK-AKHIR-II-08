# Dokumentasi Implementasi SharedPreferences Login

Dokumentasi ini menjelaskan implementasi `SharedPreferences` pada fitur login aplikasi TK Mutiara. Implementasi ini digunakan untuk menyimpan session login secara lokal, sehingga user tidak perlu login ulang ketika aplikasi ditutup lalu dibuka kembali.

## Tujuan Implementasi

SharedPreferences digunakan untuk menyimpan data login sederhana di perangkat user. Data yang disimpan adalah:

- Token login user
- Data user
- Nomor induk siswa

Dengan implementasi ini, aplikasi dapat melakukan pengecekan session saat pertama kali dibuka.

## Dependency

File:

`tk_mutiara/pubspec.yaml`

Bagian dependency:

```yaml
dependencies:
  shared_preferences: ^2.5.5
```

Package `shared_preferences` digunakan untuk menyimpan data key-value secara lokal di perangkat.

## File Utama Implementasi

Implementasi utama SharedPreferences berada pada file:

`tk_mutiara/lib/services/api_services.dart`

File ini dipilih karena seluruh proses login, penyimpanan token, logout, dan akses data user berada di dalam `ApiService`.

## 1. Import SharedPreferences

File:

`tk_mutiara/lib/services/api_services.dart`

Lokasi kode:

Baris 4

```dart
import 'package:shared_preferences/shared_preferences.dart';
```

Fungsi kode:

Import ini digunakan agar class `ApiService` dapat menggunakan fitur penyimpanan lokal dari package `shared_preferences`.

## 2. Key Untuk Menyimpan Data Session

File:

`tk_mutiara/lib/services/api_services.dart`

Lokasi kode:

Baris 11 sampai 13

```dart
static const String _tokenKey = 'auth_token';
static const String _userKey = 'user_data';
static const String _nomorIndukSiswaKey = 'nomor_induk_siswa';
```

Fungsi kode:

Kode ini mendefinisikan nama key yang digunakan untuk menyimpan data ke SharedPreferences.

Penjelasan key:

- `_tokenKey`: menyimpan token autentikasi dari backend
- `_userKey`: menyimpan data user dalam bentuk JSON string
- `_nomorIndukSiswaKey`: menyimpan nomor induk siswa

## 3. Getter Status Login

File:

`tk_mutiara/lib/services/api_services.dart`

Lokasi kode:

Baris 43

```dart
static bool get isLoggedIn => _token != null && _token!.isNotEmpty;
```

Fungsi kode:

Kode ini digunakan untuk mengecek apakah user masih memiliki token login. Jika token tidak null dan tidak kosong, maka user dianggap masih login.

Getter ini digunakan pada splash screen untuk menentukan apakah user diarahkan ke halaman utama atau ke halaman login.

## 4. Load Session Saat Aplikasi Dibuka

File:

`tk_mutiara/lib/services/api_services.dart`

Lokasi kode:

Baris 45 sampai 59

```dart
static Future<void> loadSession() async {
  final prefs = await SharedPreferences.getInstance();
  _token = prefs.getString(_tokenKey);
  _nomorIndukSiswa = prefs.getString(_nomorIndukSiswaKey);

  final userJson = prefs.getString(_userKey);
  if (userJson != null && userJson.isNotEmpty) {
    try {
      _user = Map<String, dynamic>.from(jsonDecode(userJson) as Map);
    } catch (e) {
      print('Gagal load user dari SharedPreferences: $e');
      _user = null;
    }
  }
}
```

Fungsi kode:

Fungsi `loadSession()` digunakan untuk mengambil kembali data login yang sebelumnya tersimpan di SharedPreferences.

Data yang diambil:

- Token login dari key `auth_token`
- Nomor induk siswa dari key `nomor_induk_siswa`
- Data user dari key `user_data`

Data user disimpan dalam bentuk JSON string, sehingga saat dibaca kembali perlu dikonversi menggunakan `jsonDecode()`.

## 5. Menyimpan Session Setelah Login Berhasil

File:

`tk_mutiara/lib/services/api_services.dart`

Lokasi kode:

Baris 61 sampai 73

```dart
static Future<void> _saveSession() async {
  final prefs = await SharedPreferences.getInstance();

  if (_token != null) {
    await prefs.setString(_tokenKey, _token!);
  }
  if (_nomorIndukSiswa != null) {
    await prefs.setString(_nomorIndukSiswaKey, _nomorIndukSiswa!);
  }
  if (_user != null) {
    await prefs.setString(_userKey, jsonEncode(_user));
  }
}
```

Fungsi kode:

Fungsi `_saveSession()` digunakan untuk menyimpan data login ke SharedPreferences setelah proses login berhasil.

Data yang disimpan:

- `_token` disimpan sebagai string
- `_nomorIndukSiswa` disimpan sebagai string
- `_user` disimpan sebagai JSON string menggunakan `jsonEncode()`

## 6. Pemanggilan Save Session Di Proses Login

File:

`tk_mutiara/lib/services/api_services.dart`

Lokasi kode:

Baris 189

```dart
await _saveSession();
```

Fungsi kode:

Kode ini dipanggil setelah login berhasil dan setelah data `_token`, `_user`, dan `_nomorIndukSiswa` berhasil diisi dari response backend.

Urutan proses login:

1. User memasukkan username dan password.
2. Aplikasi mengirim request login ke backend.
3. Backend mengembalikan token dan data user.
4. Token dan data user disimpan ke variable static di `ApiService`.
5. Fungsi `_saveSession()` dipanggil.
6. Data login disimpan ke SharedPreferences.
7. User diarahkan ke halaman utama.

## 7. Menghapus Session Saat Logout

File:

`tk_mutiara/lib/services/api_services.dart`

Lokasi kode:

Baris 263 sampai 272

```dart
static Future<void> logout() async {
  _token = null;
  _user = null;
  _nomorIndukSiswa = null;

  final prefs = await SharedPreferences.getInstance();
  await prefs.remove(_tokenKey);
  await prefs.remove(_userKey);
  await prefs.remove(_nomorIndukSiswaKey);
}
```

Fungsi kode:

Fungsi `logout()` digunakan untuk menghapus data session user.

Data yang dihapus:

- Token login
- Data user
- Nomor induk siswa

Session dihapus dari dua tempat:

- Variable memory di `ApiService`
- Penyimpanan lokal SharedPreferences

Efeknya, setelah logout user tidak akan masuk otomatis lagi saat aplikasi dibuka ulang.

## 8. Load Session Dipanggil Saat Aplikasi Start

File:

`tk_mutiara/lib/main.dart`

Lokasi kode:

Baris 15

```dart
await ApiService.loadSession();
```

Fungsi kode:

Kode ini dijalankan saat aplikasi pertama kali dibuka, sebelum `runApp()` dipanggil.

Tujuannya agar aplikasi membaca session yang tersimpan terlebih dahulu sebelum menentukan halaman awal user.

Alur pada `main.dart`:

1. Flutter binding diinisialisasi.
2. Firebase diinisialisasi.
3. Background handler Firebase Messaging dipasang.
4. Session login dibaca dari SharedPreferences.
5. Notification service diinisialisasi.
6. Aplikasi dijalankan.

## 9. Auto Login Setelah Splash Screen

File:

`tk_mutiara/lib/screens/welcome_screen.dart`

Lokasi kode:

Baris 56

```dart
ApiService.isLoggedIn ? const MainNavigationScreen() : const LoginScreen()
```

Fungsi kode:

Kode ini menentukan halaman tujuan setelah splash screen.

Kondisi:

- Jika `ApiService.isLoggedIn` bernilai `true`, user langsung diarahkan ke `MainNavigationScreen`.
- Jika `ApiService.isLoggedIn` bernilai `false`, user diarahkan ke `LoginScreen`.

Dengan bagian ini, fitur auto-login dapat berjalan.

## 10. Pemanggilan Logout Dari Dashboard

File:

`tk_mutiara/lib/screens/dashboard_screen.dart`

Lokasi kode:

Baris 582

```dart
await ApiService.logout();
```

Fungsi kode:

Kode ini dipanggil ketika user memilih keluar dari aplikasi melalui dashboard. Karena `logout()` bersifat async, maka pemanggilannya menggunakan `await`.

Setelah session dihapus, user diarahkan kembali ke halaman awal.

## 11. Pemanggilan Logout Dari Profil

File:

`tk_mutiara/lib/screens/profil_screen.dart`

Lokasi kode:

Baris 905

```dart
await ApiService.logout();
```

Fungsi kode:

Kode ini dipanggil ketika user logout dari halaman profil. Setelah data session dihapus dari SharedPreferences, aplikasi mengarahkan user kembali ke halaman login.

## Alur Lengkap Implementasi

### A. Login Pertama Kali

1. User membuka aplikasi.
2. Splash screen tampil.
3. `ApiService.loadSession()` belum menemukan token.
4. User diarahkan ke halaman login.
5. User memasukkan username dan password.
6. Login berhasil.
7. Token, data user, dan nomor induk siswa disimpan ke SharedPreferences.
8. User diarahkan ke halaman utama.

### B. Aplikasi Dibuka Ulang

1. User membuka aplikasi kembali.
2. `main.dart` memanggil `ApiService.loadSession()`.
3. Token dibaca dari SharedPreferences.
4. `ApiService.isLoggedIn` bernilai `true`.
5. Setelah splash screen, user langsung diarahkan ke halaman utama.

### C. Logout

1. User menekan tombol logout.
2. Aplikasi memanggil `ApiService.logout()`.
3. Token, data user, dan nomor induk siswa dihapus dari memory.
4. Token, data user, dan nomor induk siswa dihapus dari SharedPreferences.
5. User diarahkan ke halaman login atau welcome.
6. Saat aplikasi dibuka ulang, user tidak auto-login lagi.

## Kesimpulan

Implementasi SharedPreferences pada aplikasi TK Mutiara digunakan untuk menyimpan session login secara lokal. Bagian utama implementasi berada di `api_services.dart`, sedangkan pemanggilan session dilakukan di `main.dart` dan pengecekan login dilakukan di `welcome_screen.dart`.

Dengan implementasi ini, user tidak perlu login ulang selama token masih tersimpan dan user belum melakukan logout.
