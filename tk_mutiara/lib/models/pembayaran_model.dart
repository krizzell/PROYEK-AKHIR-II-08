class PembayaranModel {
  final int idTagihan;
  final String nomorIndukSiswa;
  final String namaSiswa;
  final String kelas;
  final int jumlahTagihan;
  final String periode;
  final String paymentStatus; // 'belum_bayar' | 'lunas'
  final String transactionId;
  final String paymentMethod;
  final String paymentDate;
  final String createdAt;

  PembayaranModel({
    required this.idTagihan,
    required this.nomorIndukSiswa,
    required this.namaSiswa,
    required this.kelas,
    required this.jumlahTagihan,
    required this.periode,
    required this.paymentStatus,
    this.transactionId = '',
    this.paymentMethod = '',
    this.paymentDate = '',
    this.createdAt = '',
  });

  factory PembayaranModel.fromJson(Map<String, dynamic> json) {
    final rawPeriode = (json['periode'] ?? '').toString();
    final normalizedStatus = (json['payment_status'] ?? json['status'] ?? 'belum_bayar').toString();

    return PembayaranModel(
      idTagihan: json['id_tagihan'] is int
          ? json['id_tagihan'] as int
          : int.tryParse((json['id_tagihan'] ?? '0').toString()) ?? 0,
      nomorIndukSiswa: (json['nomor_induk_siswa'] ?? '').toString(),
      namaSiswa: (json['nama_siswa'] ?? '').toString(),
      kelas: (json['kelas'] ?? '').toString(),
      jumlahTagihan: json['jumlah_tagihan'] is int
          ? json['jumlah_tagihan'] as int
          : ((json['jumlah_tagihan'] is double)
            ? (json['jumlah_tagihan'] as double).round()
            : (double.tryParse((json['jumlah_tagihan'] ?? '0').toString())?.round() ?? 0)),
      periode: rawPeriode,
      paymentStatus: normalizedStatus,
      transactionId: (json['transaction_id'] ?? '').toString(),
      paymentMethod: (json['payment_method'] ?? '').toString(),
      paymentDate: (json['payment_date'] ?? '').toString(),
      createdAt: (json['created_at'] ?? '').toString(),
    );
  }

  String get id => idTagihan.toString();

  String get bulan {
    if (periode.contains(' ')) {
      return periode.split(' ').first;
    }
    return periode;
  }

  String get tahun {
    if (periode.contains(' ')) {
      final parts = periode.split(' ');
      return parts.length > 1 ? parts.last : '';
    }
    return '';
  }

  int get nominal => jumlahTagihan;

  String get status => isLunas ? 'lunas' : 'belum';

  String get tanggalBayar => paymentDate;

  String get metodePembayaran => paymentMethod;

  String get kodeTransaksi => transactionId;

  String get nominalFormatted {
    final n = jumlahTagihan.toString();
    final buffer = StringBuffer();
    int count = 0;
    for (int i = n.length - 1; i >= 0; i--) {
      if (count > 0 && count % 3 == 0) buffer.write('.');
      buffer.write(n[i]);
      count++;
    }
    return 'Rp ${buffer.toString().split('').reversed.join('')}';
  }

  bool get isLunas => paymentStatus.toLowerCase() == 'lunas';
  bool get isPending => false;
  bool get isBelum => !isLunas;

  // === DUMMY DATA ===
  static List<PembayaranModel> dummyHistory() => [];
}