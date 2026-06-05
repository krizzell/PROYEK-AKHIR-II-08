class PerkembanganKategoriModel {
  final int idCategori;
  final int idPerkembangan;
  final String namaKategori;
  final int nilai;
  final String statusUtama;
  final String deskripsi;
  final String createdAt;
  final String updatedAt;

  PerkembanganKategoriModel({
    required this.idCategori,
    required this.idPerkembangan,
    required this.namaKategori,
    required this.nilai,
    required this.statusUtama,
    required this.deskripsi,
    required this.createdAt,
    required this.updatedAt,
  });

  factory PerkembanganKategoriModel.fromJson(Map<String, dynamic> json) {
    return PerkembanganKategoriModel(
      idCategori: json['id_perkembangan_kategori'] ?? 0,
      idPerkembangan: json['id_perkembangan'] ?? 0,
      namaKategori: json['nama_kategori'] ?? '',
      nilai: json['nilai'] ?? 0,
      statusUtama: json['status_utama'] ?? 'BSH',
      deskripsi: json['deskripsi'] ?? '',
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
    );
  }
}

class PerkembanganModel {
  final int idPerkembangan;
  final int idGuru;
  final String nomorIndukSiswa;
  final String namaAnak;
  final String namaGuru;
  final String kelas;
  final int bulan;
  final int tahun;
  final String kategori;
  final String deskripsi;
  final String templateDeskripsi;
  final String statusUtama;
  final double rataRataKelas;
  final String createdAt;
  final String updatedAt;
  final List<PerkembanganKategoriModel> kategoriDetails;

  PerkembanganModel({
    required this.idPerkembangan,
    required this.idGuru,
    required this.nomorIndukSiswa,
    required this.namaAnak,
    required this.namaGuru,
    required this.kelas,
    required this.bulan,
    required this.tahun,
    required this.kategori,
    required this.deskripsi,
    required this.templateDeskripsi,
    required this.statusUtama,
    required this.rataRataKelas,
    required this.createdAt,
    required this.updatedAt,
    required this.kategoriDetails,
  });

  factory PerkembanganModel.fromJson(Map<String, dynamic> json) {
    var kategoris = json['kategori_details'] as List? ?? [];
    return PerkembanganModel(
      idPerkembangan: json['id_perkembangan'] ?? 0,
      idGuru: json['id_guru'] ?? 0,
      nomorIndukSiswa: json['nomor_induk_siswa'] ?? '',
      namaAnak: json['nama_siswa'] ?? '',
      namaGuru: json['nama_guru'] ?? '',
      kelas: json['kelas'] ?? '',
      bulan: json['bulan'] ?? 0,
      tahun: json['tahun'] ?? 0,
      kategori: json['kategori'] ?? '',
      deskripsi: json['deskripsi'] ?? '',
      templateDeskripsi: json['template_deskripsi'] ?? '',
      statusUtama: json['status_utama'] ?? 'BSH',
      rataRataKelas: _toDouble(json['rata_rata_kelas']),
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
      kategoriDetails: kategoris
          .map((k) => PerkembanganKategoriModel.fromJson(k))
          .toList(),
    );
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0;
    if (value is num) return value.toDouble();
    return double.tryParse(value.toString()) ?? 0;
  }

  int get nilaiChart {
    switch (statusUtama) {
      case "BB":
        return 1;
      case "MB":
        return 2;
      case "BSH":
        return 3;
      case "BSB":
        return 4;
      default:
        return 0;
    }
  }
}
