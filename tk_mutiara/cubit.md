# Dokumentasi Implementasi Cubit

Dokumen ini menjelaskan penerapan Cubit pada aplikasi TK Mutiara. Cubit digunakan untuk mengatur state tab aktif pada bottom navigation bar.

## Tujuan

Implementasi Cubit bertujuan untuk:

- Memindahkan state tab aktif dari `setState` ke Cubit.
- Membuat logic perpindahan tab lebih rapi.
- Memisahkan state management dari tampilan UI.
- Membuat bottom navigation lebih scalable.

## Dependency

File:

`pubspec.yaml`

Dependency:

```yaml
flutter_bloc: ^9.1.1
```

Package `flutter_bloc` digunakan untuk menyediakan `Cubit`, `BlocProvider`, dan `BlocBuilder`.

## Struktur File

```text
lib/
├── cubit/
│   └── bottom_nav/
│       └── bottom_nav_cubit.dart
│
└── screens/
    └── main_navigation_screen.dart
```

## 1. BottomNavCubit

File:

`lib/cubit/bottom_nav/bottom_nav_cubit.dart`

Isi:

```dart
import 'package:flutter_bloc/flutter_bloc.dart';

class BottomNavCubit extends Cubit<int> {
  BottomNavCubit() : super(0);

  void changeTab(int index) {
    emit(index);
  }
}
```

Penjelasan:

- `Cubit<int>` berarti state yang disimpan berupa angka.
- State awal adalah `0`, yaitu tab Beranda.
- Method `changeTab(int index)` digunakan untuk mengganti tab aktif.
- `emit(index)` mengirim state baru ke UI.

## 2. Integrasi Cubit Di MainNavigationScreen

File:

`lib/screens/main_navigation_screen.dart`

Instance Cubit:

```dart
final BottomNavCubit _bottomNavCubit = BottomNavCubit();
```

Dispose Cubit:

```dart
@override
void dispose() {
  _bottomNavCubit.close();
  super.dispose();
}
```

Penjelasan:

Cubit dibuat di dalam `MainNavigationScreen` dan ditutup saat screen tidak digunakan lagi.

## 3. BlocProvider dan BlocBuilder

File:

`lib/screens/main_navigation_screen.dart`

Implementasi:

```dart
return BlocProvider.value(
  value: _bottomNavCubit,
  child: BlocBuilder<BottomNavCubit, int>(
    builder: (context, currentIndex) {
      return Scaffold(
        body: IndexedStack(index: currentIndex, children: _screens),
        bottomNavigationBar: _buildBottomNav(currentIndex),
      );
    },
  ),
);
```

Penjelasan:

- `BlocProvider.value` menyediakan Cubit ke widget tree.
- `BlocBuilder` membaca state tab aktif.
- `currentIndex` berasal dari state Cubit.
- `IndexedStack` menampilkan halaman sesuai index aktif.
- Bottom navigation juga menerima `currentIndex` agar warna/icon aktif berubah.

## 4. Mengubah Tab

File:

`lib/screens/main_navigation_screen.dart`

Tab biasa:

```dart
onTap: () => _bottomNavCubit.changeTab(index)
```

Tombol tengah Bayar SPP:

```dart
onTap: () => _bottomNavCubit.changeTab(2)
```

Tombol back dari halaman tab ke Beranda:

```dart
void _goBack() {
  _bottomNavCubit.changeTab(0);
}
```

Penjelasan:

Perpindahan tab tidak lagi memakai `setState`. Setiap tab berubah melalui Cubit.

## 5. Alur Cubit Bottom Navigation

```text
User menekan tab
        ↓
_bottomNavCubit.changeTab(index)
        ↓
Cubit emit index baru
        ↓
BlocBuilder rebuild
        ↓
IndexedStack menampilkan halaman sesuai index
        ↓
Bottom bar menampilkan icon dan label aktif
```

## Before dan After

Sebelum Cubit:

```dart
int _currentIndex = 0;

onTap: () => setState(() => _currentIndex = index);
```

Sesudah Cubit:

```dart
onTap: () => _bottomNavCubit.changeTab(index);
```

Kesimpulan:

State tab aktif sekarang dikelola oleh `BottomNavCubit`, bukan lagi langsung oleh `setState`.

## Kesimpulan

Implementasi Cubit pada bottom navigation membuat state tab aktif lebih terstruktur. UI bottom navigation tetap sama, tetapi logic perpindahan tab menjadi lebih bersih dan mudah dikembangkan.
