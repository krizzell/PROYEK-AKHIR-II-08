import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../models/perkembangan_model.dart';

class ApiService {
  static const String baseUrl = 'http://192.168.98.220:8000';  // IP lokal dengan port 8000
  static const String imageBaseUrl = 'http://192.168.98.220:8000';  // port 8000 untuk Laravel

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

      if (res.statusCode == 200 && data['success'] == true) {
        _token = data['token'];
        _user = data['user'];  // SAVE USER DATA
        _nomorIndukSiswa = data['user']['nomor_induk_siswa'];  // SAVE NOMOR INDUK SISWA
        print('✓ Token saved: $_token');
        print('✓ User saved: $_user');
        print('✓ Nomor Induk Siswa saved: $_nomorIndukSiswa');
        return {
          'success': true,
          'token': data['token'],
          'user': data['user'] ?? {},
          'message': 'Login berhasil'
        };
      } else {
        final errorMsg = data['error'] ?? data['message'] ?? 'Login gagal';
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
        final List data = jsonDecode(res.body);
        print('Data count: ${data.length}');
        
        final result = data.map((e) => PengumumanModel.fromJson(e)).toList();
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
        final Map<String, dynamic> decoded = jsonDecode(res.body);
        print('Decoded: $decoded');
        
        if (decoded['success'] == true) {
          final List data = decoded['data'] ?? [];
          print('Data count: ${data.length}');
          
          final result = data.map((e) => PerkembanganModel.fromJson(e)).toList();
          print('✓ Loaded ${result.length} perkembangan records');
          return result;
        } else {
          print('API error: ${decoded['message']}');
          throw Exception('API error: ${decoded['message']}');
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
        final Map<String, dynamic> decoded = jsonDecode(res.body);
        
        if (decoded['status'] == 'success') {
          final List data = decoded['data'] ?? [];
          print('✓ Loaded ${data.length} tagihan records');
          return data.map((e) => PembayaranModel.fromJson(e)).toList();
        } else {
          throw Exception(decoded['message'] ?? 'Error loading tagihan');
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

      if (res.statusCode == 200 && data['status'] == 'success') {
        final payload = data['data'] ?? {};
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
        if (data['success'] == true) {
          print('✓ Profile loaded successfully');
          return {'success': true, 'data': data['data']};
        } else {
          throw Exception(data['error'] ?? 'Error loading profile');
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
        if (data['success'] == true) {
          print('✓ Password updated successfully');
          return {'success': true, 'message': data['message']};
        } else {
          return {'success': false, 'message': data['error'] ?? 'Error updating password'};
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
        if (responseData['success'] == true) {
          print('✓ Profile updated successfully');
          return {'success': true, 'message': responseData['message']};
        } else {
          return {'success': false, 'message': responseData['error'] ?? 'Error updating profile'};
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