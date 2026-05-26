import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../models/perkembangan_model.dart';
import 'notification_service.dart';

class ApiService {
  static const String _tokenKey = 'auth_token';
  static const String _userKey = 'user_data';
  static const String _nomorIndukSiswaKey = 'nomor_induk_siswa';

  // Android emulator: flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8081
  // Device fisik (satu WiFi): flutter run --dart-define=API_BASE_URL=http://192.168.83.220:8081
  // flutter run --dart-define=IMAGE_BASE_URL=http://192.168.83.220:8000  => untuk gambar
  // php artisan serve --host=0.0.0.0 --port=8000 => untuk backend
  // php artisan storage:link => untuk membuat link storage

  static const String _configuredBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: '',
  );

  static const String _configuredImageBaseUrl = String.fromEnvironment(
    'IMAGE_BASE_URL',
    defaultValue: '',
  );

  static const List<String> _backendCandidates = [
    'http://192.168.83.220:8081',
    // 'http://10.0.2.2:8081',
  ];

  static String _activeBaseUrl =
      _configuredBaseUrl.isNotEmpty ? _configuredBaseUrl : _backendCandidates[0];

  static String get baseUrl => _activeBaseUrl;
  static String get imageBaseUrl {
    if (_configuredImageBaseUrl.isNotEmpty) {
      return _configuredImageBaseUrl;
    }
    return _activeBaseUrl.replaceFirst(':8081', ':8000');
  }

  // Simpan token & user data setelah login
  static String? _token;
  static Map<String, dynamic>? _user;
  static String? _nomorIndukSiswa;
  static final ValueNotifier<int> paymentRefreshNotifier = ValueNotifier<int>(
    0,
  );

  // Getter untuk user data
  static Map<String, dynamic>? get userInfo => _user;
  static String? get token => _token;
  static String? get nomorIndukSiswa => _nomorIndukSiswa;
  static bool get isLoggedIn => _token != null && _token!.isNotEmpty;

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

  static void notifyPaymentUpdated() {
    paymentRefreshNotifier.value = paymentRefreshNotifier.value + 1;
  }

  static Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };

  static Map<String, dynamic>? _asMap(dynamic value) {
    if (value is Map<String, dynamic>) return value;
    if (value is Map) return Map<String, dynamic>.from(value);
    return null;
  }

  static List<dynamic> _asList(dynamic value) {
    if (value is List) return value;
    return const [];
  }

  static Map<String, dynamic> _extractEnvelope(dynamic decoded) {
    final map = _asMap(decoded);
    if (map == null) return {};

    final data = map['data'];
    final wrapped = _asMap(data);
    if (wrapped != null &&
        (wrapped.containsKey('success') || wrapped.containsKey('status'))) {
      return wrapped;
    }

    return map;
  }

  static List<dynamic> _extractList(dynamic decoded) {
    final envelope = _extractEnvelope(decoded);
    final data = envelope['data'];
    if (data is List) return data;
    return _asList(decoded);
  }

  static Map<String, dynamic> _extractMap(dynamic decoded) {
    final envelope = _extractEnvelope(decoded);
    final data = envelope['data'];
    final dataMap = _asMap(data);
    if (dataMap != null) return dataMap;
    return envelope;
  }

  static bool _isSuccess(dynamic decoded) {
    final envelope = _extractEnvelope(decoded);
    final success = envelope['success'];
    if (success is bool) return success;
    final status = envelope['status']?.toString().toLowerCase();
    return status == 'success';
  }

  static String _extractMessage(
    dynamic decoded, {
    String fallback = 'Terjadi kesalahan',
  }) {
    final envelope = _extractEnvelope(decoded);
    return (envelope['message'] ?? envelope['error'] ?? fallback).toString();
  }

  static String _preferredBackendUrl() {
    final configured = _configuredBaseUrl.trim();
    if (configured.isNotEmpty) {
      return configured;
    }
    return _backendCandidates.first;
  }

  static List<String> _candidateBackendUrls() {
    final urls = <String>[];
    final preferred = _preferredBackendUrl();
    if (preferred.isNotEmpty) {
      urls.add(preferred);
    }
    urls.addAll(_backendCandidates);

    final unique = <String>[];
    for (final url in urls) {
      final trimmed = url.trim();
      if (trimmed.isNotEmpty && !unique.contains(trimmed)) {
        unique.add(trimmed);
      }
    }
    return unique;
  }

  static Future<http.Response> _postLoginWithFallback({
    required String email,
    required String password,
  }) async {
    Object? lastError;
    for (final url in _candidateBackendUrls()) {
      try {
        print('Coba backend: $url/login');
        final res = await http
            .post(
              Uri.parse('$url/login'),
              headers: {'Content-Type': 'application/json'},
              body: jsonEncode({'email': email, 'password': password}),
            )
            .timeout(
              const Duration(seconds: 6),
              onTimeout: () => throw Exception('Request timeout - Backend tidak merespons'),
            );

        _activeBaseUrl = url;
        return res;
      } catch (e) {
        lastError = e;
      }
    }

    throw Exception(
      'Request timeout - Backend tidak merespons. Pastikan adb reverse aktif atau API_BASE_URL diarahkan ke backend yang benar. Last error: $lastError',
    );
  }

  // LOGIN
  static Future<Map<String, dynamic>> login(
    String email,
    String password,
  ) async {
    try {
      print('=== LOGIN REQUEST ===');
      print('URL candidates: ${_candidateBackendUrls().join(', ')}');
      print('Email: $email');

      final res = await _postLoginWithFallback(
        email: email,
        password: password,
      );
      print('Pakai backend aktif: $baseUrl');

      print('=== RESPONSE ===');
      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      final data = jsonDecode(res.body);
      print('Parsed Data: $data');

      final envelope = _extractEnvelope(data);
      final userData =
          _asMap(
            envelope['user'] ?? envelope['data']?['user'] ?? data['user'],
          ) ??
          {};

      if (res.statusCode == 200 && _isSuccess(data)) {
        _token = (envelope['token'] ?? data['token'])?.toString();
        _user = userData;
        _nomorIndukSiswa =
            (userData['nomor_induk_siswa'] ??
                    envelope['nomor_induk_siswa'] ??
                    data['nomor_induk_siswa'])
                ?.toString(); // SAVE NOMOR INDUK SISWA
        print('✓ Token saved: $_token');
        print('✓ User saved: $_user');
        print('✓ Nomor Induk Siswa saved: $_nomorIndukSiswa');

        await _saveSession();

        // Save FCM token SETELAH auth token tersedia
        // Gunakan NotificationService.saveTokenAfterLogin() yang await-able
        try {
          await NotificationService.saveTokenAfterLogin();
        } catch (e) {
          print('⚠ FCM token save error (non-blocking): $e');
        }
        return {
          'success': true,
          'token': _token,
          'user': _user ?? {},
          'message': _extractMessage(data, fallback: 'Login berhasil'),
        };
      } else {
        final errorMsg = _extractMessage(data, fallback: 'Login gagal');
        print('✗ Login Error: $errorMsg');
        return {'success': false, 'message': errorMsg};
      }
    } catch (e) {
      print('✗ Exception: $e');
      return {'success': false, 'message': 'Koneksi error: $e'};
    }
  }

  // CHANGE PASSWORD (Public)
  static Future<Map<String, dynamic>> changePassword({
    required String username,
    required String oldPassword,
    required String newPassword,
    required String confirmPassword,
  }) async {
    try {
      print('=== CHANGE PASSWORD REQUEST ===');
      print('URL: $imageBaseUrl/api/change-password');
      print('Username: $username');

      final res = await http.post(
        Uri.parse('$imageBaseUrl/api/change-password'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'username': username,
          'old_password': oldPassword,
          'new_password': newPassword,
          'new_password_confirmation': confirmPassword,
        }),
      ).timeout(
        const Duration(seconds: 10),
        onTimeout: () => throw Exception('Request timeout'),
      );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      final data = jsonDecode(res.body);
      if (res.statusCode == 200 && _isSuccess(data)) {
        return {
          'success': true,
          'message': _extractMessage(data, fallback: 'Password berhasil diubah'),
        };
      } else {
        return {
          'success': false,
          'message': _extractMessage(data, fallback: 'Gagal mengubah password'),
        };
      }
    } catch (e) {
      print('✗ Exception: $e');
      return {'success': false, 'message': 'Koneksi error: $e'};
    }
  }

  // LOGOUT
  static Future<void> logout() async {
    _token = null;
    _user = null;
    _nomorIndukSiswa = null;

    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_userKey);
    await prefs.remove(_nomorIndukSiswaKey);
  }

  static Future<void> saveFcmToken(String fcmToken, {int retryCount = 0}) async {
    try {
      print('=== SAVE FCM TOKEN (Attempt ${retryCount + 1}/3) ===');
      print('URL: $baseUrl/api/user/fcm-token');
      print('FCM Token: $fcmToken');
      print('Auth Token: ${_token?.substring(0, 20)}...');

      if (_token == null) {
        print('⚠ User belum login, FCM token tidak disimpan');
        // Retry setelah 2 detik
        if (retryCount < 3) {
          await Future.delayed(const Duration(seconds: 2));
          return saveFcmToken(fcmToken, retryCount: retryCount + 1);
        }
        return;
      }

      final res = await http
          .post(
            Uri.parse('$baseUrl/api/user/fcm-token'),
            headers: {
              'Content-Type': 'application/json',
              'Authorization': 'Bearer $_token',
            },
            body: jsonEncode({'fcm_token': fcmToken}),
          )
          .timeout(const Duration(seconds: 10));

      print('✓ Response Status: ${res.statusCode}');
      print('Response Body: ${res.body}');

      if (res.statusCode == 200 || res.statusCode == 201) {
        print('✓ FCM token berhasil disimpan ke server');
      } else if (res.statusCode == 401 || res.statusCode == 403) {
        print('✗ Unauthorized (401/403) - Session mungkin expired');
      } else {
        print('⚠ Failed to save FCM token: ${res.statusCode}');
        if (res.statusCode >= 500 && retryCount < 2) {
          await Future.delayed(const Duration(seconds: 3));
          return saveFcmToken(fcmToken, retryCount: retryCount + 1);
        }
      }
    } catch (e) {
      print('✗ Exception saat save FCM: $e');
      if (retryCount < 2) {
        print('↻ Retry dalam 3 detik...');
        await Future.delayed(const Duration(seconds: 3));
        return saveFcmToken(fcmToken, retryCount: retryCount + 1);
      }
    }
  }

  // PROFILE
  static Future<void> fetchProfile() async {
    if (_token == null) return;
    try {
      final res = await http
          .get(Uri.parse('$baseUrl/api/profile'), headers: _headers)
          .timeout(const Duration(seconds: 10));

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        if (_isSuccess(decoded)) {
          final envelope = _extractEnvelope(decoded);
          final profileData = _asMap(envelope['data']);
          if (profileData != null) {
            _user = {
              ...?_user,
              ...profileData,
              'kelas':
                  profileData['nama_kelas'] ??
                  profileData['kelas'] ??
                  _user?['kelas'],
            };
          }
        }
      }
    } catch (e) {
      print('Error fetching profile: $e');
    }
  }

  // PENGUMUMAN
  static Future<List<PengumumanModel>> getPengumuman() async {
    try {
      print('=== GET PENGUMUMAN ===');
      print('Token: $_token');
      print('URL: $baseUrl/api/pengumuman');

      final res = await http
          .get(Uri.parse('$baseUrl/api/pengumuman'), headers: _headers)
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () => throw Exception('Request timeout'),
          );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        final List data = _extractList(decoded);
        print('Data count: ${data.length}');

        final result = data
            .map(
              (e) =>
                  PengumumanModel.fromJson(Map<String, dynamic>.from(e as Map)),
            )
            .toList();
        print('✓ Loaded ${result.length} pengumuman records');
        return result;
      } else if (res.statusCode == 401) {
        print('✗ Unauthorized - Token expired');
        throw Exception('Sesi habis, silakan login ulang');
      } else {
        print('✗ Error status: ${res.statusCode}');
        throw Exception('Server error: ${res.statusCode}');
      }
    } catch (e) {
      print('✗ Exception: $e');
      return PengumumanModel.dummyData();
    }
  }

  // PERKEMBANGAN
  static Future<List<PerkembanganModel>> getPerkembangan() async {
    try {
      print('=== GET PERKEMBANGAN ===');
      print('Token: $_token');
      print('Nomor Induk Siswa: $_nomorIndukSiswa');
      print(
        'URL: $baseUrl/api/perkembangan?nomor_induk_siswa=$_nomorIndukSiswa',
      );

      if (_nomorIndukSiswa == null) {
        throw Exception(
          'Siswa belum login atau nomor_induk_siswa tidak tersimpan',
        );
      }

      final res = await http
          .get(
            Uri.parse(
              '$baseUrl/api/perkembangan?nomor_induk_siswa=$_nomorIndukSiswa',
            ),
            headers: _headers,
          )
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () => throw Exception('Request timeout'),
          );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        final envelope = _extractEnvelope(decoded);
        print('Decoded: $decoded');

        if (_isSuccess(decoded)) {
          final List data = _extractList(decoded);
          print('Data count: ${data.length}');

          final result = data
              .map(
                (e) => PerkembanganModel.fromJson(
                  Map<String, dynamic>.from(e as Map),
                ),
              )
              .toList();
          print('✓ Loaded ${result.length} perkembangan records');
          return result;
        } else {
          final message = _extractMessage(
            envelope,
            fallback: 'Error loading perkembangan',
          );
          print('API error: $message');
          throw Exception('API error: $message');
        }
      } else if (res.statusCode == 401) {
        print('✗ Unauthorized - Token expired');
        throw Exception('Sesi habis, silakan login ulang');
      } else {
        print('✗ Error status: ${res.statusCode}');
        throw Exception('Server error: ${res.statusCode}');
      }
    } catch (e) {
      print('✗ Exception: $e');
      rethrow;
    }
  }

  // PEMBAYARAN
  static Future<List<PembayaranModel>> getPembayaran() async {
    try {
      print('=== GET PEMBAYARAN (TAGIHAN) ===');
      print('Nomor Induk Siswa: $_nomorIndukSiswa');
      print('URL: $baseUrl/api/tagihan');

      if (_nomorIndukSiswa == null) {
        throw Exception(
          'Siswa belum login atau nomor_induk_siswa tidak tersimpan',
        );
      }

      final res = await http
          .get(Uri.parse('$baseUrl/api/tagihan'), headers: _headers)
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () => throw Exception('Request timeout'),
          );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);

        if (_isSuccess(decoded)) {
          final List data = _extractList(decoded);
          print('✓ Loaded ${data.length} tagihan records');
          return data
              .map(
                (e) => PembayaranModel.fromJson(
                  Map<String, dynamic>.from(e as Map),
                ),
              )
              .toList();
        } else {
          throw Exception(
            _extractMessage(decoded, fallback: 'Error loading tagihan'),
          );
        }
      } else if (res.statusCode == 401) {
        throw Exception('Sesi habis, silakan login ulang');
      }
      return [];
    } catch (e) {
      print('✗ Exception: $e');
      return [];
    }
  }

  // BAYAR SPP
  static Future<Map<String, dynamic>> bayarSPP(String id, String metode) async {
    try {
      print('=== CREATE MIDTRANS TRANSACTION ===');
      print('URL: $baseUrl/api/payment/create-transaction');
      print('id_tagihan: $id');
      print('payment_method: $metode');

      final int idTagihan = int.tryParse(id) ?? 0;
      if (idTagihan <= 0) {
        return {'success': false, 'message': 'ID tagihan tidak valid'};
      }

      final res = await http
          .post(
            Uri.parse('$baseUrl/api/payment/create-transaction'),
            headers: _headers,
            body: jsonEncode({'id_tagihan': idTagihan}),
          )
          .timeout(
            const Duration(seconds: 15),
            onTimeout: () => throw Exception('Request timeout'),
          );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');
      final data = jsonDecode(res.body);

      if (res.statusCode == 200 && _isSuccess(data)) {
        final payload = _extractMap(data);
        return {
          'success': true,
          'id_tagihan': payload['id_tagihan'],
          'id_pembayaran': payload['id_pembayaran'],
          'snap_token': payload['snap_token'],
          'redirect_url': payload['redirect_url'],
          'order_id': payload['order_id'],
          'status_tagihan': payload['status_tagihan'],
          'status_bayar': payload['status_bayar'],
          'amount': payload['amount'],
          'client_key': payload['client_key'],
        };
      } else {
        return {
          'success': false,
          'message': _extractMessage(
            data,
            fallback: 'Gagal membuat transaksi pembayaran',
          ),
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server: $e'};
    }
  }

  static Future<Map<String, dynamic>> cekStatusPembayaran(
    String idTagihan,
  ) async {
    try {
      final int parsed = int.tryParse(idTagihan) ?? 0;
      if (parsed <= 0) {
        return {'success': false, 'message': 'ID tagihan tidak valid'};
      }

      final res = await http
          .get(
            Uri.parse('$baseUrl/api/payment/status/$parsed'),
            headers: _headers,
          )
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () => throw Exception('Request timeout'),
          );

      final data = jsonDecode(res.body);
      if (res.statusCode == 200 && _isSuccess(data)) {
        return {'success': true, 'data': _extractMap(data)};
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal cek status pembayaran',
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal cek status: $e'};
    }
  }

  // PROFILE
  static Future<Map<String, dynamic>> getProfile() async {
    try {
      print('=== GET PROFILE ===');
      print('Token: $_token');
      print('URL: $baseUrl/api/profile');

      final res = await http
          .get(Uri.parse('$baseUrl/api/profile'), headers: _headers)
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () => throw Exception('Request timeout'),
          );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (_isSuccess(data)) {
          print('✓ Profile loaded successfully');
          return {'success': true, 'data': _extractMap(data)};
        } else {
          throw Exception(
            _extractMessage(data, fallback: 'Error loading profile'),
          );
        }
      } else if (res.statusCode == 401) {
        throw Exception('Sesi habis, silakan login ulang');
      } else {
        throw Exception('Server error: ${res.statusCode}');
      }
    } catch (e) {
      print('✗ Exception: $e');
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  // UPDATE PASSWORD
  static Future<Map<String, dynamic>> updatePassword(
    String oldPassword,
    String newPassword,
  ) async {
    try {
      print('=== UPDATE PASSWORD ===');
      print('URL: $baseUrl/api/profile/password');

      final res = await http
          .put(
            Uri.parse('$baseUrl/api/profile/password'),
            headers: _headers,
            body: jsonEncode({
              'old_password': oldPassword,
              'new_password': newPassword,
            }),
          )
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () => throw Exception('Request timeout'),
          );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (_isSuccess(data)) {
          print('✓ Password updated successfully');
          return {
            'success': true,
            'message': _extractMessage(
              data,
              fallback: 'Password berhasil diubah',
            ),
          };
        } else {
          return {
            'success': false,
            'message': _extractMessage(
              data,
              fallback: 'Error updating password',
            ),
          };
        }
      } else if (res.statusCode == 401) {
        return {'success': false, 'message': 'Password lama tidak sesuai'};
      } else {
        return {'success': false, 'message': 'Server error: ${res.statusCode}'};
      }
    } catch (e) {
      print('✗ Exception: $e');
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  // UPDATE PROFILE
  static Future<Map<String, dynamic>> updateProfile(
    Map<String, dynamic> data,
  ) async {
    try {
      print('=== UPDATE PROFILE ===');
      print('URL: $baseUrl/api/profile');
      print('Data: $data');

      final res = await http
          .put(
            Uri.parse('$baseUrl/api/profile'),
            headers: _headers,
            body: jsonEncode(data),
          )
          .timeout(
            const Duration(seconds: 10),
            onTimeout: () => throw Exception('Request timeout'),
          );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      if (res.statusCode == 200) {
        final responseData = jsonDecode(res.body);
        if (_isSuccess(responseData)) {
          print('✓ Profile updated successfully');
          return {
            'success': true,
            'message': _extractMessage(
              responseData,
              fallback: 'Profil berhasil diubah',
            ),
          };
        } else {
          return {
            'success': false,
            'message': _extractMessage(
              responseData,
              fallback: 'Error updating profile',
            ),
          };
        }
      } else if (res.statusCode == 401) {
        return {'success': false, 'message': 'Sesi habis, silakan login ulang'};
      } else {
        return {'success': false, 'message': 'Server error: ${res.statusCode}'};
      }
    } catch (e) {
      print('✗ Exception: $e');
      return {'success': false, 'message': 'Error: $e'};
    }
  }
}
