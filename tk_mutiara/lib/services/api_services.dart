import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../models/perkembangan_model.dart';

class ApiService {
  // Override tanpa ubah source code:
  // flutter run --dart-define=API_BASE_URL=http://<IP_LAPTOP>:8000
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.153.188.204:8000',
  );
  static const String imageBaseUrl = String.fromEnvironment(
    'IMAGE_BASE_URL',
    defaultValue: baseUrl,
  );

  // Simpan token & user data setelah login
  static String? _token;
  static Map<String, dynamic>? _user;
  static String? _nomorIndukSiswa;
  static final ValueNotifier<int> paymentRefreshNotifier = ValueNotifier<int>(0);

  // Getter untuk user data
  static Map<String, dynamic>? get userInfo => _user;
  static String? get token => _token;
  static String? get nomorIndukSiswa => _nomorIndukSiswa;

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
    if (wrapped != null && (wrapped.containsKey('success') || wrapped.containsKey('status'))) {
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

  static String _extractMessage(dynamic decoded, {String fallback = 'Terjadi kesalahan'}) {
    final envelope = _extractEnvelope(decoded);
    return (envelope['message'] ?? envelope['error'] ?? fallback).toString();
  }

  // LOGIN~
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      print('=== LOGIN REQUEST ===');
      print('URL: $baseUrl/api/login');
      print('Email: $email');
      
      final res = await http.post(
        Uri.parse('$baseUrl/api/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      ).timeout(
        const Duration(seconds: 10),
        onTimeout: () {
          throw Exception('Request timeout - Backend tidak merespons');
        },
      );

      print('=== RESPONSE ===');
      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      final data = jsonDecode(res.body);
      print('Parsed Data: $data');

      final envelope = _extractEnvelope(data);
      final userData = _asMap(envelope['user'] ?? envelope['data']?['user'] ?? data['user']) ?? {};

      if (res.statusCode == 200 && _isSuccess(data)) {
        _token = (envelope['token'] ?? data['token'])?.toString();
        _user = userData;  // SAVE USER DATA
        _nomorIndukSiswa = (userData['nomor_induk_siswa'] ?? envelope['nomor_induk_siswa'] ?? data['nomor_induk_siswa'])?.toString();  // SAVE NOMOR INDUK SISWA
        print('✓ Token saved: $_token');
        print('✓ User saved: $_user');
        print('✓ Nomor Induk Siswa saved: $_nomorIndukSiswa');
        return {
          'success': true,
          'token': _token,
          'user': _user ?? {},
          'message': _extractMessage(data, fallback: 'Login berhasil')
        };
      } else {
        final errorMsg = _extractMessage(data, fallback: 'Login gagal');
        print('✗ Login Error: $errorMsg');
        return {
          'success': false,
          'message': errorMsg,
        };
      }
    } catch (e) {
      print('✗ Exception: $e');
      return {
        'success': false,
        'message': 'Koneksi error: $e'
      };
    }
  }

  // LOGOUT
  static void logout() {
    _token = null;
    _user = null;
    _nomorIndukSiswa = null;
  }

  // PENGUMUMAN
  static Future<List<PengumumanModel>> getPengumuman() async {
    try {
      print('=== GET PENGUMUMAN ===');
      print('Token: $_token');
      print('URL: $baseUrl/api/pengumuman');
      
      final res = await http.get(
        Uri.parse('$baseUrl/api/pengumuman'),
        headers: _headers,
      ).timeout(
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
            .map((e) => PengumumanModel.fromJson(Map<String, dynamic>.from(e as Map)))
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
      print('URL: $baseUrl/api/perkembangan?nomor_induk_siswa=$_nomorIndukSiswa');
      
      if (_nomorIndukSiswa == null) {
        throw Exception('Siswa belum login atau nomor_induk_siswa tidak tersimpan');
      }
      
      final res = await http.get(
        Uri.parse('$baseUrl/api/perkembangan?nomor_induk_siswa=$_nomorIndukSiswa'),
        headers: _headers,
      ).timeout(
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
              .map((e) => PerkembanganModel.fromJson(Map<String, dynamic>.from(e as Map)))
              .toList();
          print('✓ Loaded ${result.length} perkembangan records');
          return result;
        } else {
          final message = _extractMessage(envelope, fallback: 'Error loading perkembangan');
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
      print('URL: $baseUrl/api/tagihan?nomor_induk_siswa=$_nomorIndukSiswa');
      
      if (_nomorIndukSiswa == null) {
        throw Exception('Siswa belum login atau nomor_induk_siswa tidak tersimpan');
      }

      final res = await http.get(
        Uri.parse('$baseUrl/api/tagihan?nomor_induk_siswa=$_nomorIndukSiswa'),
        headers: _headers,
      ).timeout(
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
          return data.map((e) => PembayaranModel.fromJson(Map<String, dynamic>.from(e as Map))).toList();
        } else {
          throw Exception(_extractMessage(decoded, fallback: 'Error loading tagihan'));
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
      print('=== CREATE PEMBAYARAN ===');
      print('URL: $baseUrl/api/create-payment');
      print('invoice_id: $id');
      print('payment_method: $metode');

      final res = await http.post(
        Uri.parse('$baseUrl/api/create-payment'),
        headers: _headers,
        body: jsonEncode({
          'invoice_id': int.tryParse(id) ?? 0,
          'user_id': _user?['id_akun'],
          'payment_method': metode,
          'auto_success': true,
        }),
      ).timeout(
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
          'snap_token': payload['snap_token'],
          'redirect_url': payload['redirect_url'],
          'transaction_id': payload['transaction_id'],
          'order_id': payload['order_id'],
          'payment_status': payload['payment_status'],
          'auto_success': payload['auto_success'] ?? false,
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal membuat transaksi pembayaran',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server: $e'};
    }
  }

  static Future<Map<String, dynamic>> cekStatusPembayaran(String transactionId) async {
    try {
      final res = await http.get(
        Uri.parse('$baseUrl/api/pembayaran/$transactionId/status'),
        headers: _headers,
      ).timeout(
        const Duration(seconds: 10),
        onTimeout: () => throw Exception('Request timeout'),
      );

      final data = jsonDecode(res.body);
      if (res.statusCode == 200 && data['status'] == 'success') {
        return {'success': true, 'data': data['data']};
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

      final res = await http.get(
        Uri.parse('$baseUrl/api/profile'),
        headers: _headers,
      ).timeout(
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
          throw Exception(_extractMessage(data, fallback: 'Error loading profile'));
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

      final res = await http.put(
        Uri.parse('$baseUrl/api/profile/password'),
        headers: _headers,
        body: jsonEncode({
          'old_password': oldPassword,
          'new_password': newPassword,
        }),
      ).timeout(
        const Duration(seconds: 10),
        onTimeout: () => throw Exception('Request timeout'),
      );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (_isSuccess(data)) {
          print('✓ Password updated successfully');
          return {'success': true, 'message': _extractMessage(data, fallback: 'Password berhasil diubah')};
        } else {
          return {'success': false, 'message': _extractMessage(data, fallback: 'Error updating password')};
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
  static Future<Map<String, dynamic>> updateProfile(Map<String, dynamic> data) async {
    try {
      print('=== UPDATE PROFILE ===');
      print('URL: $baseUrl/api/profile');
      print('Data: $data');

      final res = await http.put(
        Uri.parse('$baseUrl/api/profile'),
        headers: _headers,
        body: jsonEncode(data),
      ).timeout(
        const Duration(seconds: 10),
        onTimeout: () => throw Exception('Request timeout'),
      );

      print('Status: ${res.statusCode}');
      print('Body: ${res.body}');

      if (res.statusCode == 200) {
        final responseData = jsonDecode(res.body);
        if (_isSuccess(responseData)) {
          print('✓ Profile updated successfully');
          return {'success': true, 'message': _extractMessage(responseData, fallback: 'Profil berhasil diubah')};
        } else {
          return {'success': false, 'message': _extractMessage(responseData, fallback: 'Error updating profile')};
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