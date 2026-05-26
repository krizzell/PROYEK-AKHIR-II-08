# Dokumentasi Implementasi Google Maps Flutter

Dokumen ini menjelaskan penerapan Google Maps Flutter pada aplikasi TK Mutiara. Peta digunakan untuk menampilkan lokasi sekolah pada halaman dashboard.

## Tujuan

Google Maps Flutter digunakan untuk:

- Menampilkan peta lokasi sekolah.
- Menampilkan marker lokasi TK Mutiara.
- Mengaktifkan interaksi peta seperti zoom dan scroll.
- Mengambil lokasi perangkat user jika izin diberikan.
- Menampilkan overlay alamat sekolah.

## Dependency

File:

`pubspec.yaml`

Dependency:

```yaml
google_maps_flutter: ^2.17.0
location: ^8.0.1
geocoding: ^4.0.0
```

Fungsi:

- `google_maps_flutter`: menampilkan Google Map.
- `location`: mengambil lokasi perangkat.
- `geocoding`: konversi koordinat ke alamat, meskipun pada implementasi saat ini tidak digunakan aktif untuk mengganti alamat sekolah.

## File Implementasi

File utama:

`lib/screens/dashboard_screen.dart`

Import:

```dart
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:location/location.dart' as loc;
import 'package:geocoding/geocoding.dart' as geo;
```

## 1. State Google Maps

File:

`lib/screens/dashboard_screen.dart`

Implementasi:

```dart
GoogleMapController? _mapController;
final loc.Location _location = loc.Location();
LatLng? _currentPosition;
String _currentAddress = "TK Swasta Mutiara Balige, Jl. TD Pardede, Toba";
bool _isMapLoading = true;
```

Penjelasan:

- `_mapController`: mengontrol kamera Google Map.
- `_location`: object untuk mengakses lokasi perangkat.
- `_currentPosition`: menyimpan lokasi user jika tersedia.
- `_currentAddress`: alamat yang ditampilkan pada overlay map.
- `_isMapLoading`: status loading lokasi.

## 2. Koordinat TK Mutiara

File:

`lib/screens/dashboard_screen.dart`

Implementasi:

```dart
final LatLng _tkMutiaraLocation = const LatLng(2.3287092, 99.0686357);
```

Penjelasan:

Koordinat ini menjadi titik utama peta dan marker sekolah.

## 3. Mengambil Lokasi User

File:

`lib/screens/dashboard_screen.dart`

Fungsi:

```dart
Future<void> _getCurrentLocation() async
```

Alur:

1. Cek apakah GPS aktif.
2. Jika belum aktif, minta user mengaktifkan GPS.
3. Cek permission lokasi.
4. Jika permission belum diberikan, minta izin lokasi.
5. Ambil latitude dan longitude perangkat.
6. Simpan ke `_currentPosition`.

Potongan kode:

```dart
serviceEnabled = await _location.serviceEnabled();
permissionGranted = await _location.hasPermission();
final locationData = await _location.getLocation();
```

Penjelasan:

Lokasi user digunakan untuk mengaktifkan fitur `myLocationEnabled` pada Google Map jika lokasi tersedia.

## 4. Pemanggilan Lokasi Saat Dashboard Dibuka

File:

`lib/screens/dashboard_screen.dart`

Implementasi di `initState()`:

```dart
_getCurrentLocation();
```

Penjelasan:

Saat dashboard pertama kali dibuka, aplikasi mencoba mengambil lokasi perangkat.

## 5. Widget GoogleMap

File:

`lib/screens/dashboard_screen.dart`

Implementasi utama:

```dart
GoogleMap(
  padding: const EdgeInsets.only(bottom: 56),
  initialCameraPosition: CameraPosition(
    target: _tkMutiaraLocation,
    zoom: 16,
  ),
  myLocationEnabled: _currentPosition != null,
  myLocationButtonEnabled: false,
  zoomControlsEnabled: true,
  mapToolbarEnabled: true,
  scrollGesturesEnabled: true,
  zoomGesturesEnabled: true,
  markers: {
    Marker(
      markerId: const MarkerId('tk_mutiara'),
      position: _tkMutiaraLocation,
      infoWindow: const InfoWindow(
        title: 'TK Swasta Mutiara Balige',
        snippet: 'Lokasi Sekolah',
      ),
    ),
  },
  onMapCreated: (controller) => _mapController = controller,
)
```

Penjelasan:

`GoogleMap` menampilkan peta dengan posisi awal di TK Mutiara. Marker ditampilkan pada koordinat sekolah.

## 6. Marker Lokasi Sekolah

File:

`lib/screens/dashboard_screen.dart`

Implementasi:

```dart
Marker(
  markerId: const MarkerId('tk_mutiara'),
  position: _tkMutiaraLocation,
  infoWindow: const InfoWindow(
    title: 'TK Swasta Mutiara Balige',
    snippet: 'Lokasi Sekolah',
  ),
  icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueRed),
)
```

Penjelasan:

Marker digunakan untuk menandai lokasi sekolah di peta.

## 7. Kontrol Kamera Map

File:

`lib/screens/dashboard_screen.dart`

Implementasi:

```dart
_mapController?.animateCamera(
  CameraUpdate.newLatLngZoom(_tkMutiaraLocation, 16),
);
```

Penjelasan:

Saat icon lokasi ditekan, kamera peta diarahkan kembali ke lokasi TK Mutiara.

## 8. Gesture Recognizer

File:

`lib/screens/dashboard_screen.dart`

Implementasi:

```dart
gestureRecognizers: {
  Factory<OneSequenceGestureRecognizer>(() => EagerGestureRecognizer()),
},
```

Penjelasan:

Kode ini membuat Google Map tetap dapat menerima gesture scroll dan zoom meskipun berada di dalam scroll view dashboard.

## 9. Overlay Alamat

File:

`lib/screens/dashboard_screen.dart`

Implementasi:

```dart
Positioned(
  bottom: 12,
  left: 12,
  right: 12,
  child: Container(
    child: Row(
      children: [
        Icon(Icons.location_on_rounded),
        Text(_currentAddress),
      ],
    ),
  ),
)
```

Penjelasan:

Overlay alamat ditampilkan di bagian bawah peta agar user tetap melihat keterangan lokasi sekolah.

## Alur Google Maps

```text
Dashboard dibuka
        ↓
_getCurrentLocation()
        ↓
Cek GPS dan permission
        ↓
Ambil lokasi user jika diizinkan
        ↓
GoogleMap ditampilkan
        ↓
Kamera diarahkan ke koordinat TK Mutiara
        ↓
Marker sekolah ditampilkan
```

## Kesimpulan

Google Maps Flutter pada aplikasi ini digunakan untuk menampilkan lokasi sekolah di dashboard. Implementasi utama berada pada `dashboard_screen.dart`, dengan dukungan dependency `google_maps_flutter`, `location`, dan `geocoding`.
