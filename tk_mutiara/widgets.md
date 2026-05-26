# Dokumentasi Implementasi Widgets

Dokumen ini menjelaskan penerapan widget pada aplikasi TK Mutiara. Widget digunakan sebagai komponen UI utama, baik dalam bentuk halaman penuh maupun komponen kecil yang dapat digunakan ulang.

## Tujuan

Penerapan widget bertujuan untuk:

- Membagi tampilan aplikasi menjadi komponen yang rapi.
- Memudahkan penggunaan ulang UI.
- Memisahkan halaman, card, button, dan elemen visual lain.
- Membuat kode lebih mudah dibaca dan dirawat.

## Struktur Widget

Folder utama:

```text
lib/
├── screens/
│   ├── dashboard_screen.dart
│   ├── login_screen.dart
│   ├── main_navigation_screen.dart
│   ├── pembayaran_screen.dart
│   ├── pengumuman_screen.dart
│   ├── perkembangan_screen.dart
│   └── ...
│
├── widgets/
│   ├── menu_card.dart
│   └── info_card.dart
```

## 1. Root Widget Aplikasi

File:

`lib/main.dart`

Implementasi:

```dart
class MutiaraApp extends StatelessWidget {
  const MutiaraApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'TK Mutiara',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: const WelcomeScreen(),
    );
  }
}
```

Penjelasan:

`MutiaraApp` adalah root widget aplikasi. Widget ini membungkus seluruh aplikasi menggunakan `MaterialApp`, mengatur theme, dan menentukan halaman awal.

## 2. Screen Widget

Screen widget adalah widget yang mewakili satu halaman penuh.

Contoh file:

- `lib/screens/welcome_screen.dart`
- `lib/screens/login_screen.dart`
- `lib/screens/dashboard_screen.dart`
- `lib/screens/main_navigation_screen.dart`
- `lib/screens/pembayaran_screen.dart`
- `lib/screens/pengumuman_screen.dart`
- `lib/screens/perkembangan_screen.dart`
- `lib/screens/profil_screen.dart`

Contoh:

```dart
class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}
```

Penjelasan:

`DashboardScreen` menggunakan `StatefulWidget` karena memiliki data yang dapat berubah, seperti data pembayaran, pengumuman, perkembangan, notifikasi, dan lokasi peta.

## 3. MenuCard Widget

File:

`lib/widgets/menu_card.dart`

Implementasi utama:

```dart
class MenuCard extends StatefulWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final Color color;
  final Color iconBg;
  final VoidCallback onTap;
  final String? badge;
}
```

Fungsi:

`MenuCard` digunakan sebagai card menu yang dapat ditekan. Widget ini menerima data dari luar, seperti judul, subtitle, icon, warna, event `onTap`, dan badge opsional.

Bagian interaksi:

```dart
void _onTapDown(_) {
  _controller.forward();
  HapticFeedback.lightImpact();
}

void _onTapUp(_) {
  _controller.reverse();
  widget.onTap();
}
```

Penjelasan:

`MenuCard` memakai animasi scale saat ditekan dan memberi haptic feedback agar interaksi terasa lebih hidup.

## 4. InfoCard Widget

File:

`lib/widgets/info_card.dart`

Implementasi utama:

```dart
class InfoCard extends StatelessWidget {
  final String title;
  final String value;
  final String subtitle;
  final IconData icon;
  final Color? color;
  final VoidCallback? onTap;
}
```

Fungsi:

`InfoCard` adalah widget card informasi. Widget ini cocok untuk menampilkan ringkasan data seperti jumlah, status, atau informasi singkat.

Contoh struktur UI:

```dart
GestureDetector(
  onTap: onTap,
  child: Container(
    padding: const EdgeInsets.all(18),
    decoration: BoxDecoration(
      color: AppTheme.white,
      borderRadius: BorderRadius.circular(20),
      boxShadow: AppTheme.cardShadowList,
    ),
  ),
)
```

Penjelasan:

`InfoCard` menggunakan `StatelessWidget` karena tampilannya hanya bergantung pada data yang dikirim dari parent.

## 5. StatefulWidget dan StatelessWidget

Contoh `StatelessWidget`:

```dart
class InfoCard extends StatelessWidget
```

Digunakan ketika widget tidak menyimpan state internal.

Contoh `StatefulWidget`:

```dart
class MenuCard extends StatefulWidget
```

Digunakan ketika widget memiliki state atau animasi internal.

## Kesimpulan

Penerapan widget pada aplikasi ini dibagi menjadi widget halaman dan widget komponen. Screen berada di folder `screens`, sedangkan komponen reusable berada di folder `widgets`. Pendekatan ini membuat kode UI lebih modular dan mudah dikembangkan.
