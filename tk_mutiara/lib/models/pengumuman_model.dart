import 'dart:convert';

class PengumumanModel {
  static const String imageBaseUrl = 'https://admin.tkmutiara.my.id/storage/';

  final int idPengumuman;
  final int idGuru;
  final String namaGuru;
  final String judul;
  final String media;
  final List<String> mediaPaths;
  final String waktuUnggah;
  final String tampilSampai;
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
    this.tampilSampai = '',
    required this.deskripsi,
    required this.createdAt,
    required this.updatedAt,
  });

  static String getImageUrl(String media) {
    if (media.isEmpty) return '';

    try {
      final decoded = jsonDecode(media);
      if (decoded is List && decoded.isNotEmpty) {
        return getImageUrlFromPath(decoded.first.toString());
      }
    } catch (_) {
      return getImageUrlFromPath(media);
    }

    return getImageUrlFromPath(media);
  }

  static String getImageUrlFromPath(String path) {
    if (path.isEmpty) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path;
    }

    final cleanPath = path.startsWith('/') ? path.substring(1) : path;
    if (cleanPath.startsWith('storage/')) {
      return Uri.encodeFull('https://admin.tkmutiara.my.id/$cleanPath');
    }

    return Uri.encodeFull('$imageBaseUrl$cleanPath');
  }

  // Factory untuk parsing dari API
  factory PengumumanModel.fromJson(Map<String, dynamic> json) {
    List<String> allPaths = [];
    String mediaValue = '';

    if (json['media'] is List) {
      final List mediaList = json['media'];
      allPaths = mediaList.map((e) => e.toString()).toList();
      if (allPaths.isNotEmpty) mediaValue = allPaths[0];
    } else {
      final rawMedia = json['media']?.toString() ?? '';
      mediaValue = rawMedia;

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
      tampilSampai: json['tampil_sampai'] ?? '',
      deskripsi: json['deskripsi'] ?? '',
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
    );
  }

  bool get isVisibleOnMobile {
    if (tampilSampai.trim().isEmpty) return true;

    final normalized = tampilSampai.trim().replaceFirst(' ', 'T');
    final parsed = DateTime.tryParse(normalized);
    if (parsed == null) return true;

    return !parsed.isBefore(DateTime.now());
  }

  PengumumanModel copyWith({
    int? idPengumuman,
    int? idGuru,
    String? namaGuru,
    String? judul,
    String? media,
    List<String>? mediaPaths,
    String? waktuUnggah,
    String? tampilSampai,
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
      tampilSampai: tampilSampai ?? this.tampilSampai,
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
        tampilSampai: '2026-04-22 10:30:00',
        deskripsi: 'Libur sekolah akan diadakan dari tanggal 20-25 April 2026',
        createdAt: '2026-04-15 10:30:00',
        updatedAt: '2026-04-15 10:30:00',
      ),
    ];
  }
}
