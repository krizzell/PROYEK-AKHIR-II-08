import 'package:flutter/material.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../models/perkembangan_model.dart';
import '../services/api_services.dart';
import 'perkembangan_screen.dart';
import 'pembayaran_screen.dart';
import 'pengumuman_screen.dart';
import 'history_screen.dart';
import 'login_screen.dart';
import 'package:fl_chart/fl_chart.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  String _namaAnak = 'Bintang';
  String _kelas = 'Kelas A';

  List<PembayaranModel> _payments = [];
  List<PengumumanModel> _pengumuman = [];
  List<PerkembanganModel> _perkembanganData = [];

  List<PerkembanganModel> _getDataPerTahun() {
    if (_perkembanganData.isEmpty) return [];
    final tahunTerbaru = _perkembanganData.last.tahun;
    final dataTahun = _perkembanganData
        .where((e) => e.tahun == tahunTerbaru)
        .toList();
    dataTahun.sort((a, b) => a.bulan.compareTo(b.bulan));
    return dataTahun;
  }

  PembayaranModel? get _tagihan {
    for (final p in _payments) {
      if (p.isBelum) return p;
    }
    return null;
  }

  @override
  void initState() {
    super.initState();

    final user = ApiService.userInfo;
    if (user != null) {
      _namaAnak = user['nama_siswa'].toString();
      final className = user['kelas']?.toString() ?? 'Kelas A';
      final guruName = user['nama_guru']?.toString() ?? 'Bu Wina';
      _kelas = "$className - $guruName";
    }

    _loadData();
    _syncProfile();
    _listenNotifikasi(); // ← TAMBAHAN
  }

  // ← TAMBAHAN: Listen notifikasi FCM untuk auto-refresh
  void _listenNotifikasi() {
    // Saat app di FOREGROUND
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      final data = message.data;
      if (data['type'] == 'payment_success') {
        print('🔔 Notifikasi pembayaran diterima, refresh dashboard...');
        _loadData();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('✅ Pembayaran berhasil dikonfirmasi!'),
              backgroundColor: Colors.green,
              duration: Duration(seconds: 3),
            ),
          );
        }
      }
    });

    // Saat app di BACKGROUND lalu diklik notifikasinya
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      final data = message.data;
      if (data['type'] == 'payment_success') {
        print('🔔 App dibuka dari notifikasi pembayaran, refresh dashboard...');
        _loadData();
      }
    });
  }

  void _syncProfile() async {
    await ApiService.fetchProfile();
    if (mounted) {
      final user = ApiService.userInfo;
      if (user != null) {
        setState(() {
          _namaAnak = user['nama_siswa'].toString();
          final className = user['nama_kelas']?.toString() ??
              user['kelas']?.toString() ??
              'Kelas A';
          final guruName = user['nama_guru']?.toString() ?? 'Bu Wina';
          _kelas = "$className - $guruName";
        });
      }
    }
  }

  void _loadData() async {
    final p = await ApiService.getPembayaran();
    final pg = await ApiService.getPengumuman();
    final perkembangan = await ApiService.getPerkembangan();

    if (mounted) {
      setState(() {
        _payments = p;
        _pengumuman = pg.take(2).toList();
        _perkembanganData = perkembangan;
      });
    }
  }

  String getStatus(String kategori) {
    try {
      final item = _perkembanganData.firstWhere(
        (e) => e.kategori.toLowerCase().contains(kategori.toLowerCase()),
      );
      switch (item.statusUtama) {
        case "BSH":
          return "Baik";
        case "MB":
          return "Mulai";
        case "BB":
          return "Kurang";
        default:
          return item.statusUtama;
      }
    } catch (e) {
      return "-";
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF3ED),
      body: SafeArea(
        child: RefreshIndicator( // ← TAMBAHAN: pull-to-refresh manual
          color: const Color(0xFFE57A32),
          onRefresh: () async {
            await Future.wait([
              ApiService.getPembayaran().then((p) {
                if (mounted) setState(() => _payments = p);
              }),
              ApiService.getPengumuman().then((pg) {
                if (mounted) setState(() => _pengumuman = pg.take(2).toList());
              }),
              ApiService.getPerkembangan().then((pk) {
                if (mounted) setState(() => _perkembanganData = pk);
              }),
            ]);
          },
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(), // ← wajib untuk RefreshIndicator
            child: Column(
              children: [
                _header(),
                const SizedBox(height: 20),
                _menuCards(),
                const SizedBox(height: 20),
                _perkembangan(),
                const SizedBox(height: 20),
                _pengumumanUI(),
                const SizedBox(height: 20),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _header() {
    return Container(
      height: 160,
      padding: const EdgeInsets.symmetric(horizontal: 20),
      decoration: const BoxDecoration(
        color: Color(0xFFE57A32),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(24),
          bottomRight: Radius.circular(24),
        ),
      ),
      child: Center(
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            const CircleAvatar(
              radius: 28,
              backgroundColor: Colors.white,
              child: Icon(Icons.person, color: Colors.grey, size: 28),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    "Halo",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 1),
                  InkWell(
                    borderRadius: BorderRadius.circular(8),
                    onTap: _showSwitchAccount,
                    child: Row(
                      children: [
                        Flexible(
                          child: Text(
                            _namaAnak,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 22,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                        const SizedBox(width: 4),
                        const Icon(
                          Icons.keyboard_arrow_down,
                          color: Colors.white,
                          size: 22,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 5),
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(
                          Icons.school,
                          size: 14,
                          color: Color(0xFFE57A32),
                        ),
                        const SizedBox(width: 5),
                        Text(
                          _kelas,
                          style: const TextStyle(
                            color: Color(0xFFE58A45),
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 1),
            Padding(
              padding: const EdgeInsets.only(right: 13),
              child: Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.25),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.notifications_none,
                  color: Colors.white,
                  size: 22,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showSwitchAccount() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return Container(
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 20),
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Stack(
                alignment: Alignment.center,
                children: [
                  const Center(
                    child: Text(
                      "Pilih Anak",
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                  Positioned(
                    right: 0,
                    child: IconButton(
                      onPressed: () => Navigator.pop(context),
                      icon: const Icon(Icons.close),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 6),
              const Text(
                "Pilih akun anak untuk melihat informasi dan aktivitasnya",
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 12, color: Colors.grey),
              ),
              const SizedBox(height: 18),
              GestureDetector(
                onTap: () => Navigator.pop(context),
                child: Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFFF3E8),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: const Color(0xFFE57A32)),
                  ),
                  child: Row(
                    children: [
                      const CircleAvatar(
                        radius: 22,
                        backgroundColor: Color(0xFFEFEFEF),
                        child: Icon(Icons.person, color: Colors.grey),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              _namaAnak,
                              style: const TextStyle(
                                fontWeight: FontWeight.w600,
                                fontSize: 14,
                              ),
                            ),
                            const SizedBox(height: 3),
                            Row(
                              children: [
                                const Icon(Icons.school,
                                    size: 14, color: Color(0xFFE57A32)),
                                const SizedBox(width: 4),
                                Text(
                                  _kelas,
                                  style: const TextStyle(
                                    fontSize: 11,
                                    color: Color(0xFFE57A32),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      const Icon(Icons.check_circle, color: Color(0xFFE57A32)),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
              GestureDetector(
                onTap: () {
                  Navigator.pop(context);
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const LoginScreen()),
                  );
                },
                child: Container(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF9F9F9),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: Colors.grey.shade300),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: const [
                      Icon(Icons.add, size: 18, color: Color(0xFFE57A32)),
                      SizedBox(width: 6),
                      Text(
                        "Tambah Akun Anak Baru",
                        style: TextStyle(
                          color: Color(0xFFE57A32),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFFF5F5F5),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: const [
                    Icon(Icons.verified_user, size: 18),
                    SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        "Data setiap anak terpisah dan aman. Pastikan informasi yang dimasukkan benar.",
                        style: TextStyle(fontSize: 11),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _menuCards() {
    final bool lunas = _tagihan == null;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(
        children: [
          Expanded(
            child: _cardMenu(
              title: "Bayar SPP",
              subtitle: "Pembayaran SPP bulanan mudah dan cepat",
              icon: Icons.account_balance_wallet_outlined,
              badge: lunas ? "Lunas" : "Belum",
              badgeColor: lunas ? Colors.green : Colors.red,
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => PembayaranScreen(tagihan: _tagihan),
                  ),
                );
              },
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: _cardMenu(
              title: "Riwayat Pembayaran",
              subtitle: "Lihat riwayat pembayaran",
              icon: Icons.receipt_long,
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const HistoryScreen()),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _cardMenu({
    required String title,
    required String subtitle,
    required IconData icon,
    String? badge,
    Color? badgeColor,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        height: 180,
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          boxShadow: [
            BoxShadow(
              color: Colors.grey.withOpacity(0.15),
              blurRadius: 8,
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                CircleAvatar(
                  radius: 28,
                  backgroundColor: const Color(0xFFFCEBDC),
                  child: Icon(icon, size: 28, color: const Color(0xFFE98943)),
                ),
                if (badge != null)
                  Padding(
                    padding: const EdgeInsets.only(bottom: 35),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: badgeColor,
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Text(
                        badge,
                        style:
                            const TextStyle(color: Colors.white, fontSize: 12),
                      ),
                    ),
                  ),
              ],
            ),
            const Spacer(),
            Text(title,
                style: const TextStyle(
                    fontWeight: FontWeight.bold, fontSize: 18)),
            const SizedBox(height: 6),
            Text(
              subtitle,
              style: const TextStyle(color: Colors.grey, fontSize: 13),
            ),
          ],
        ),
      ),
    );
  }

  Widget _perkembangan() {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const PerkembanganScreen()),
        );
      },
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 16),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: const BoxDecoration(
                        color: Color(0xFFFCEBDC),
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.show_chart,
                        color: Color(0xFFE58A45),
                        size: 24,
                      ),
                    ),
                    const SizedBox(width: 10),
                    const Text(
                      "Perkembangan Anak",
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                      ),
                    ),
                  ],
                ),
                const Text(
                  "Detail",
                  style: TextStyle(color: Color(0xFFE58A45), fontSize: 12),
                ),
              ],
            ),
            const SizedBox(height: 8),
            const Text(
              "Ringkasan perkembangan anak pada bulan ini",
              style: TextStyle(fontSize: 12, color: Colors.grey),
            ),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFFFF7F2),
                borderRadius: BorderRadius.circular(16),
              ),
              child: _buildChart(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildChart() {
    final data = _getDataPerTahun();

    if (data.isEmpty) {
      return const SizedBox(
        height: 160,
        child: Center(child: Text("Belum ada data")),
      );
    }

    double convertNilai(String status) {
      switch (status) {
        case "BB": return 1;
        case "MB": return 2;
        case "BSH": return 3;
        case "BSB": return 4;
        default: return 1;
      }
    }

    String convertLabel(double value) {
      switch (value.toInt()) {
        case 1: return "BB";
        case 2: return "MB";
        case 3: return "BSH";
        case 4: return "BSB";
        default: return "-";
      }
    }

    const namaBulan = [
      "", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
      "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
    ];

    return SizedBox(
      height: 170,
      child: LineChart(
        LineChartData(
          minY: 1,
          maxY: 4,
          gridData: FlGridData(
            show: true,
            drawVerticalLine: false,
            horizontalInterval: 1,
            getDrawingHorizontalLine: (value) {
              return FlLine(
                color: const Color(0xFFE58A45).withOpacity(0.15),
                strokeWidth: 1,
              );
            },
          ),
          borderData: FlBorderData(show: false),
          titlesData: FlTitlesData(
            topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
            rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
            leftTitles: AxisTitles(
              sideTitles: SideTitles(
                showTitles: true,
                interval: 1,
                reservedSize: 45,
                getTitlesWidget: (value, meta) {
                  return Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: Text(
                      convertLabel(value),
                      style: const TextStyle(fontSize: 10),
                    ),
                  );
                },
              ),
            ),
            bottomTitles: AxisTitles(
              sideTitles: SideTitles(
                showTitles: true,
                interval: 1,
                reservedSize: 30,
                getTitlesWidget: (value, meta) {
                  final bulan = value.toInt() + 1;
                  if (bulan >= 1 && bulan <= 12) {
                    return Padding(
                      padding: const EdgeInsets.only(top: 6),
                      child: Text(
                        namaBulan[bulan],
                        style: const TextStyle(fontSize: 10),
                      ),
                    );
                  }
                  return const Text("");
                },
              ),
            ),
          ),
          lineTouchData: LineTouchData(
            handleBuiltInTouches: true,
            touchTooltipData: LineTouchTooltipData(
              tooltipRoundedRadius: 12,
              tooltipPadding: const EdgeInsets.all(10),
              tooltipMargin: 8,
              getTooltipColor: (touchedSpot) => Colors.white,
              getTooltipItems: (spots) {
                final latest = data.last;
                return spots.map((spot) {
                  final index = spot.x.toInt();
                  final item = data[index];
                  final isCurrentMonth = item.bulan == latest.bulan &&
                      item.tahun == latest.tahun;

                  String label;
                  switch (item.statusUtama) {
                    case "BB": label = "BB (Belum Berkembang)"; break;
                    case "MB": label = "MB (Mulai Berkembang)"; break;
                    case "BSH": label = "BSH (Berkembang Sesuai Harapan)"; break;
                    case "BSB": label = "BSB (Berkembang Sangat Baik)"; break;
                    default: label = item.statusUtama;
                  }

                  if (isCurrentMonth) {
                    return LineTooltipItem(
                      "${namaBulan[item.bulan]} ${item.tahun}\n$label",
                      const TextStyle(
                        color: Colors.black,
                        fontWeight: FontWeight.w600,
                        fontSize: 12,
                      ),
                    );
                  }
                  return LineTooltipItem(
                    item.statusUtama,
                    const TextStyle(
                      color: Colors.black,
                      fontWeight: FontWeight.bold,
                    ),
                  );
                }).toList();
              },
            ),
          ),
          lineBarsData: [
            LineChartBarData(
              spots: data.map((e) {
                return FlSpot(
                  (e.bulan - 1).toDouble(),
                  convertNilai(e.statusUtama),
                );
              }).toList(),
              isCurved: true,
              color: const Color(0xFFE58A45),
              barWidth: 3,
              dotData: FlDotData(
                show: true,
                checkToShowDot: (spot, barData) => true,
                getDotPainter: (spot, percent, bar, index) {
                  return FlDotCirclePainter(
                    radius: 5,
                    color: const Color(0xFFE58A45),
                    strokeWidth: 2,
                    strokeColor: Colors.white,
                  );
                },
              ),
              belowBarData: BarAreaData(
                show: true,
                color: const Color(0xFFE58A45).withOpacity(0.15),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _pengumumanUI() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: const BoxDecoration(
                      color: Color(0xFFFCEBDC),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.campaign,
                      color: Color(0xFFE58A45),
                      size: 25,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    "Pengumuman",
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                    ),
                  ),
                ],
              ),
              GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) => const PengumumanScreen()),
                  );
                },
                child: const Text(
                  "Lihat semua",
                  style: TextStyle(color: Color(0xFFE58A45), fontSize: 12),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          ..._pengumuman.map(
            (e) => Column(
              children: [
                GestureDetector(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => const PengumumanScreen()),
                    );
                  },
                  child: ListTile(
                    contentPadding: EdgeInsets.zero,
                    leading: const Icon(
                      Icons.circle,
                      size: 10,
                      color: Colors.orange,
                    ),
                    title: Text(
                      e.judul,
                      style: const TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 14,
                      ),
                    ),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          e.waktuUnggah,
                          style: const TextStyle(fontSize: 11),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          e.deskripsi,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontSize: 12,
                            color: Colors.grey,
                          ),
                        ),
                      ],
                    ),
                    trailing: Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.orange.shade100,
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: const Text(
                        "Baru",
                        style: TextStyle(fontSize: 11),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}