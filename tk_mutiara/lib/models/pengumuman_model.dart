import 'dart:convert';
import '../services/api_services.dart';

class PengumumanModel {
  final int idPengumuman;
  final int idGuru;
  final String namaGuru;
  final String judul;
  final String media;
  final List<String> mediaPaths; // All media paths for multi-image support
  final String waktuUnggah;
  final String deskripsi;
  final String createdAt;
  final String updatedAt;

  const PengumumanModel({
    required this.idPengumuman,
    required this.idGuru,
    required this.namaGuru,
    required this.judul,
    required this.media,
    this.mediaPaths = const [],
    required this.waktuUnggah,
    required this.deskripsi,
    required this.createdAt,
    required this.updatedAt,
  });

  static String getImageUrl(String mediaPath) {
    if (mediaPath.isEmpty) {
      print('📸 Image URL: Empty path');
      return '';
    }

    // Check if media is already full URL (starts with http)
    if (mediaPath.startsWith('http')) {
      print('📸 Image URL (full): $mediaPath');
      return mediaPath;
    }

    // Handle JSON array string from Laravel/Go Backend
    // Example: ["pengumuman/xxx.jpg"]
    String cleanPath = mediaPath;
    if (mediaPath.trim().startsWith('[') && mediaPath.trim().endsWith(']')) {
      try {
        final List<dynamic> paths = jsonDecode(mediaPath);
        if (paths.isNotEmpty) {
          cleanPath = paths[0].toString();
        }
      } catch (e) {
        print('Error parsing media JSON: $e');
      }
    }

    // Clean up if path starts with storage/ to avoid double storage/
    if (cleanPath.startsWith('storage/')) {
      cleanPath = cleanPath.replaceFirst('storage/', '');
    }

    // If only filename/path, prepend the base URL with storage path
    final url = '${ApiService.imageBaseUrl}/storage/$cleanPath';

    print('📸 Image URL (constructed): $url');
    print('📸 Media Path Original: $mediaPath');
    print('📸 Media Path Cleaned: $cleanPath');
    return url;
  }

  // Factory untuk parsing dari API
  factory PengumumanModel.fromJson(Map<String, dynamic> json) {
    // Parse all media paths for multi-image support
    List<String> allPaths = [];
    String mediaValue = '';

    if (json['media'] is List) {
      final List mediaList = json['media'];
      allPaths = mediaList.map((e) => e.toString()).toList();
      if (allPaths.isNotEmpty) mediaValue = allPaths[0];
    } else {
      final rawMedia = json['media']?.toString() ?? '';
      mediaValue = rawMedia;
      // Try to parse JSON array string like '["path1.jpg","path2.jpg"]'
      if (rawMedia.trim().startsWith('[') && rawMedia.trim().endsWith(']')) {
        try {
          final List<dynamic> parsed = jsonDecode(rawMedia);
          allPaths = parsed.map((e) => e.toString()).toList();
          if (allPaths.isNotEmpty) mediaValue = allPaths[0];
        } catch (_) {
          if (rawMedia.isNotEmpty) allPaths = [rawMedia];
        }
      } else if (rawMedia.isNotEmpty) {
        allPaths = [rawMedia];
      }
    }

    return PengumumanModel(
      idPengumuman: json['id_pengumuman'] ?? 0,
      idGuru: json['id_guru'] ?? 0,
      namaGuru: json['nama_guru'] ?? 'Admin',
      judul: json['judul'] ?? 'Tidak ada judul',
      media: mediaValue,
      mediaPaths: allPaths,
      waktuUnggah: json['waktu_unggah'] ?? '',
      deskripsi: json['deskripsi'] ?? '',
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
    );
  }

  // Method copyWith untuk immutability
  PengumumanModel copyWith({
    int? idPengumuman,
    int? idGuru,
    String? namaGuru,
    String? judul,
    String? media,
    List<String>? mediaPaths,
    String? waktuUnggah,
    String? deskripsi,
    String? createdAt,
    String? updatedAt,
  }) {
    return PengumumanModel(
      idPengumuman: idPengumuman ?? this.idPengumuman,
      idGuru: idGuru ?? this.idGuru,
      namaGuru: namaGuru ?? this.namaGuru,
      judul: judul ?? this.judul,
      media: media ?? this.media,
      mediaPaths: mediaPaths ?? this.mediaPaths,
      waktuUnggah: waktuUnggah ?? this.waktuUnggah,
      deskripsi: deskripsi ?? this.deskripsi,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }

  // Dummy data untuk testing
  static List<PengumumanModel> dummyData() {
    return [
      PengumumanModel(
        idPengumuman: 1,
        idGuru: 1,
        namaGuru: 'Ibu Ani',
        judul: 'Pengumuman Libur Sekolah',
        media: '',
        waktuUnggah: '2026-04-15 10:30:00',
        deskripsi: 'Libur sekolah akan diadakan dari tanggal 20-25 April 2026',
        createdAt: '2026-04-15 10:30:00',
        updatedAt: '2026-04-15 10:30:00',
      ),
    ];
  }
}