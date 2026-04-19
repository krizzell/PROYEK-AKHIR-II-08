import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../models/perkembangan_model.dart';

class ApiService {
  static const String baseUrl = 'http://192.168.145.220:8081';  // disesuaikan sama ipv4 masing-masing
  static const String imageBaseUrl = 'http://192.168.145.220:8000';  // ini port untuk gambar, disesuaikan sama ipv4 masing-masing

  // Simpan token & user data setelah login
  static String? _token;
  static Map<String, dynamic>? _user;

  // Getter untuk user data
  static Map<String, dynamic>? get userInfo => _user;
  static String? get token => _token;

  static Map<String, String> get _headers => {
        'Content-Type': 'application/json',
        if (_token != null) 'Authorization': 'Bearer $_token',
      };

  // LOGIN
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      print('=== LOGIN REQUEST ===');
      print('URL: $baseUrl/login');
      print('Email: $email');
      
      final res = await http.post(
        Uri.parse('$baseUrl/login'),
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
        print('✓ Token saved: $_token');
        print('✓ User saved: $_user');
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
      print('URL: $baseUrl/api/perkembangan');
      
      final res = await http.get(
        Uri.parse('$baseUrl/api/perkembangan'),
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
          print('API error: ${decoded['error']}');
          throw Exception('API error: ${decoded['error']}');
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
      final res = await http.get(
        Uri.parse('$baseUrl/pembayaran'),
        headers: _headers,
      );

      if (res.statusCode == 200) {
        final List data = jsonDecode(res.body);
        return data.map((e) => PembayaranModel.fromJson(e)).toList();
      } else if (res.statusCode == 401) {
        throw Exception('Sesi habis, silakan login ulang');
      }
      return [];
    } catch (e) {
      return PembayaranModel.dummyHistory();
    }
  }

  // BAYAR SPP
  static Future<Map<String, dynamic>> bayarSPP(String id, String metode) async {
    try {
      final res = await http.post(
        Uri.parse('$baseUrl/pembayaran/bayar'),
        headers: _headers,
        body: jsonEncode({'id': id, 'metode': metode}),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200) {
        return {'success': true, 'kode_transaksi': data['kode_transaksi']};
      } else {
        return {'success': false, 'message': data['error']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server'};
    }
  }
}