# TK Swasta Mutiara Balige

## Deskripsi Proyek
TK Swasta Mutiara Balige merupakan aplikasi monitoring dan administrasi sekolah berbasis mobile yang dikembangkan untuk mendukung proses komunikasi, 
pemantauan perkembangan siswa, serta pengelolaan administrasi sekolah secara digital.
Aplikasi ini dirancang untuk membantu orang tua dalam memantau perkembangan anak, menerima informasi sekolah, serta melakukan pembayaran SPP secara online. 
Selain itu, sistem menyediakan dashboard administrasi berbasis web yang digunakan oleh guru dan kepala sekolah untuk mengelola data siswa, perkembangan siswa, 
pengumuman sekolah, pembayaran SPP, dan perpindahan kelas.


## Teknologi yang Digunakan
### Mobile Application
- Flutter
- Dart
- Firebase Cloud Messaging (FCM)

### Backend API
- Golang
- Gin Framework
- JWT Authentication

### Web Dashboard
- Laravel
- PHP 8.3
- Blade Template

### Database
- MySQL

### Infrastruktur
- Ubuntu Server
- Nginx
- Let's Encrypt SSL
- Cloud VPS


## Fitur Sistem
### Orang Tua
- Login ke aplikasi mobile
- Melihat perkembangan anak
- Melihat pengumuman sekolah
- Melakukan pembayaran SPP
- Melihat riwayat pembayaran
- Menerima notifikasi pembayaran dan pengumuman
- Mengubah password akun

### Guru
- Mengelola perkembangan siswa
- Mengelola pengumuman sekolah
- Melihat data siswa berdasarkan kelas
- Memverifikasi pembayaran SPP
- Mengajukan perpindahan kelas siswa

### Kepala Sekolah
- Mengelola data guru
- Mengelola data siswa
- Mengelola data kelas
- Mengelola akun pengguna
- Mengelola tagihan SPP
- Memantau pembayaran SPP
- Menyetujui atau menolak pengajuan perpindahan kelas
- Memantau perkembangan siswa


## Arsitektur Sistem
Sistem terdiri dari tiga komponen utama:

### 1. Mobile Application (Flutter)
Digunakan oleh orang tua untuk mengakses informasi perkembangan siswa, pembayaran SPP, riwayat pembayaran, dan pengumuman sekolah.

### 2. Backend API (Golang)
Berfungsi sebagai layanan REST API yang menghubungkan aplikasi mobile dengan database serta menangani proses autentikasi, pembayaran, dan notifikasi.

### 3. Dashboard Administrasi (Laravel)
Digunakan oleh guru dan kepala sekolah untuk mengelola seluruh data operasional sekolah.


## Deployment Dashboard Admin
https://admin.tkmutiara.my.id

## Tim Pengembang
Kelompok 08 PA II
- Krisna Putra Immanuel 
- Binsar Immanuel Siregar
- Soniaa
- Yohanna Agatha
- Haryati Hutapea

