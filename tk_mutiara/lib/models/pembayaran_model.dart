import '../services/api_services.dart';

class PembayaranModel {
  final int idTagihan;
  final String nomorIndukSiswa;
  final String namaSiswa;
  final String kelas;
  final int jumlahTagihan;
  final int dendaKeterlambatan;
  final int totalPembayaran;
  final String periode;
  final String paymentStatus;
  final String transactionId;
  final String paymentMethod;
  final String paymentDate;
  final String createdAt;
  final String updatedAt;

  PembayaranModel({
    required this.idTagihan,
    required this.nomorIndukSiswa,
    required this.namaSiswa,
    required this.kelas,
    required this.jumlahTagihan,
    this.dendaKeterlambatan = 0,
    int? totalPembayaran,
    required this.periode,
    required this.paymentStatus,
    this.transactionId = '',
    this.paymentMethod = '',
    this.paymentDate = '',
    this.createdAt = '',
    this.updatedAt = '',
  }) : totalPembayaran = totalPembayaran ?? jumlahTagihan;

  factory PembayaranModel.fromJson(Map<String, dynamic> json) {
    final rawPeriode = (json['periode'] ?? '').toString();
    final normalizedStatus =
        (json['status_tagihan'] ??
                json['payment_status'] ??
                json['status'] ??
                'belum_bayar')
            .toString();
    final jumlahTagihan = json['jumlah_tagihan'] is int
        ? json['jumlah_tagihan'] as int
        : ((json['jumlah_tagihan'] is double)
              ? (json['jumlah_tagihan'] as double).round()
              : (double.tryParse(
                      (json['jumlah_tagihan'] ?? '0').toString(),
                    )?.round() ??
                    0));
    final serverDenda = json['denda_keterlambatan'] is int
        ? json['denda_keterlambatan'] as int
        : ((json['denda_keterlambatan'] is double)
              ? (json['denda_keterlambatan'] as double).round()
              : (double.tryParse(
                      (json['denda_keterlambatan'] ?? '0').toString(),
                    )?.round() ??
                    0));
    final dendaKeterlambatan = serverDenda > 0
        ? serverDenda
        : _calculateLateFee(rawPeriode, normalizedStatus);
    final serverTotal = json['total_pembayaran'] is int
        ? json['total_pembayaran'] as int
        : ((json['total_pembayaran'] is double)
              ? (json['total_pembayaran'] as double).round()
              : (double.tryParse(
                  (json['total_pembayaran'] ?? '').toString(),
                )?.round()));

    return PembayaranModel(
      idTagihan: json['id_tagihan'] is int
          ? json['id_tagihan'] as int
          : int.tryParse((json['id_tagihan'] ?? '0').toString()) ?? 0,
      nomorIndukSiswa: (json['nomor_induk_siswa'] ?? '').toString(),
      namaSiswa:
          (json['nama_siswa'] ??
                  json['nama_anak'] ??
                  ApiService.userInfo?['nama_siswa'] ??
                  '')
              .toString(),
      kelas: (json['kelas'] ?? ApiService.userInfo?['kelas'] ?? '').toString(),
      jumlahTagihan: jumlahTagihan,
      dendaKeterlambatan: dendaKeterlambatan,
      totalPembayaran: serverTotal ?? jumlahTagihan + dendaKeterlambatan,
      periode: rawPeriode,
      paymentStatus: normalizedStatus,
      transactionId: (json['transaction_id'] ?? '').toString(),
      paymentMethod: (json['payment_method'] ?? '').toString(),
      paymentDate: (json['payment_date'] ?? '').toString(),
      createdAt: (json['created_at'] ?? '').toString(),
      updatedAt: (json['updated_at'] ?? json['paid_at'] ?? '').toString(),
    );
  }

  static int _calculateLateFee(String periode, String status) {
    if (status.toLowerCase() == 'lunas') return 0;

    final clean = periode
        .toLowerCase()
        .replaceFirst(RegExp(r'^spp\s+'), '')
        .trim();
    final monthMap = {
      'januari': 1,
      'jan': 1,
      'februari': 2,
      'feb': 2,
      'maret': 3,
      'mar': 3,
      'april': 4,
      'apr': 4,
      'mei': 5,
      'may': 5,
      'juni': 6,
      'jun': 6,
      'juli': 7,
      'jul': 7,
      'agustus': 8,
      'agu': 8,
      'aug': 8,
      'september': 9,
      'sep': 9,
      'oktober': 10,
      'okt': 10,
      'oct': 10,
      'november': 11,
      'nov': 11,
      'desember': 12,
      'des': 12,
      'dec': 12,
    };

    int? month;
    for (final entry in monthMap.entries) {
      if (clean.contains(entry.key)) {
        month = entry.value;
        break;
      }
    }

    final yearMatch = RegExp(r'(20\d{2})').firstMatch(clean);
    final year = int.tryParse(yearMatch?.group(1) ?? '');
    if (month == null || year == null) return 0;

    final dueDate = DateTime(year, month, 10, 23, 59, 59);
    return DateTime.now().isAfter(dueDate) ? 20000 : 0;
  }

  String get id => idTagihan.toString();

  String get periodeBersih {
    var value = periode.trim().replaceAll(RegExp(r'\s+'), ' ');

    if (value.toLowerCase().startsWith('spp ')) {
      value = value.substring(4).trim();
    }

    return value;
  }

  String get periodeLabel {
    final value = periodeBersih;
    return value.isEmpty ? 'SPP' : 'SPP $value';
  }

  String get bulan {
    final value = periodeBersih;
    if (value.contains(' ')) {
      return value.split(' ').first;
    }
    return value;
  }

  String get tahun {
    final value = periodeBersih;
    if (value.contains(' ')) {
      final parts = value.split(' ');
      return parts.length > 1 ? parts.last : '';
    }
    return '';
  }

  int get nominal => totalPembayaran;

  String get status => isLunas ? 'lunas' : 'belum';

  String get tanggalBayar {
    if (paymentDate.isNotEmpty) return paymentDate;
    return '';
  }

  String get metodePembayaran => paymentMethod;

  String get kodeTransaksi => transactionId;

  String get nominalFormatted {
    return formatRupiah(totalPembayaran);
  }

  String get jumlahTagihanFormatted => formatRupiah(jumlahTagihan);
  String get dendaFormatted => formatRupiah(dendaKeterlambatan);

  String formatRupiah(int value) {
    final n = value.toString();
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

  static List<PembayaranModel> dummyHistory() => [];
}
