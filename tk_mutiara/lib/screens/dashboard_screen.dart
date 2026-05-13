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
import 'package:tk_mutiara/theme/app_theme.dart';

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
    final dataTahun = _perkembanganData.where((e) => e.tahun == tahunTerbaru).toList();
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
    _listenNotifikasi();
  }

  void _listenNotifikasi() {
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      final data = message.data;
      if (data['type'] == 'payment_success') {
        _loadData();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: const Row(children: [Icon(Icons.check_circle_rounded, color: Colors.white, size: 18), SizedBox(width: 8), Text('Pembayaran berhasil dikonfirmasi!')]),
              backgroundColor: AppTheme.success,
              behavior: SnackBarBehavior.floating,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              margin: const EdgeInsets.all(16),
            ),
          );
        }
      }
    });
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      if (message.data['type'] == 'payment_success') _loadData();
    });
  }

  void _syncProfile() async {
    await ApiService.fetchProfile();
    if (mounted) {
      final user = ApiService.userInfo;
      if (user != null) {
        setState(() {
          _namaAnak = user['nama_siswa'].toString();
          final className = user['nama_kelas']?.toString() ?? user['kelas']?.toString() ?? 'Kelas A';
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

  String _getGreeting() {
    final hour = DateTime.now().hour;
    if (hour < 11) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FC),
      body: SafeArea(
        child: RefreshIndicator(
          color: AppTheme.primary,
          onRefresh: () async {
            await Future.wait([
              ApiService.getPembayaran().then((p) { if (mounted) setState(() => _payments = p); }),
              ApiService.getPengumuman().then((pg) { if (mounted) setState(() => _pengumuman = pg.take(2).toList()); }),
              ApiService.getPerkembangan().then((pk) { if (mounted) setState(() => _perkembanganData = pk); }),
            ]);
          },
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
            child: Column(
              children: [
                _header(),
                const SizedBox(height: 20),
                _menuCards(),
                const SizedBox(height: 20),
                _perkembangan(),
                const SizedBox(height: 20),
                _pengumumanUI(),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ── HEADER (ULTRA PREMIUM) ──
  Widget _header() {
    return Container(
      padding: const EdgeInsets.fromLTRB(24, 20, 24, 28),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft, end: Alignment.bottomRight,
          colors: [Color(0xFFFF6B1A), Color(0xFFFF8C42), Color(0xFFFFAA60)],
          stops: [0.0, 0.5, 1.0],
        ),
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(32),
          bottomRight: Radius.circular(32),
        ),
        boxShadow: [
          BoxShadow(color: const Color(0xFFFF6B1A).withOpacity(0.35), blurRadius: 24, offset: const Offset(0, 12)),
          BoxShadow(color: const Color(0xFFFF6B1A).withOpacity(0.15), blurRadius: 40, offset: const Offset(0, 20)),
        ],
      ),
      child: Stack(
        children: [
          // Decorative circles
          Positioned(right: -30, top: -30, child: Container(width: 100, height: 100,
            decoration: BoxDecoration(shape: BoxShape.circle, color: Colors.white.withOpacity(0.06)))),
          Positioned(right: 40, bottom: -20, child: Container(width: 60, height: 60,
            decoration: BoxDecoration(shape: BoxShape.circle, color: Colors.white.withOpacity(0.04)))),
          Positioned(left: -20, bottom: -10, child: Container(width: 80, height: 80,
            decoration: BoxDecoration(shape: BoxShape.circle, color: Colors.white.withOpacity(0.03)))),
          // Content
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(3),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: LinearGradient(
                    begin: Alignment.topLeft, end: Alignment.bottomRight,
                    colors: [Colors.white.withOpacity(0.5), Colors.white.withOpacity(0.15)],
                  ),
                ),
                child: CircleAvatar(
                  radius: 28,
                  backgroundColor: Colors.white.withOpacity(0.2),
                  child: Text(
                    _namaAnak.isNotEmpty ? _namaAnak[0].toUpperCase() : 'B',
                    style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w900, letterSpacing: -0.5),
                  ),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(_getGreeting(),
                      style: TextStyle(color: Colors.white.withOpacity(0.8), fontSize: 13, fontWeight: FontWeight.w500, letterSpacing: 0.2)),
                    const SizedBox(height: 3),
                    InkWell(
                      borderRadius: BorderRadius.circular(8),
                      onTap: _showSwitchAccount,
                      child: Row(
                        children: [
                          Flexible(child: Text(_namaAnak, overflow: TextOverflow.ellipsis,
                            style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900, letterSpacing: -0.5))),
                          const SizedBox(width: 4),
                          Container(
                            padding: const EdgeInsets.all(2),
                            decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(6)),
                            child: Icon(Icons.keyboard_arrow_down_rounded, color: Colors.white.withOpacity(0.9), size: 16),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.15),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.white.withOpacity(0.15)),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.school_rounded, size: 13, color: Colors.white.withOpacity(0.9)),
                          const SizedBox(width: 6),
                          Flexible(child: Text(_kelas, overflow: TextOverflow.ellipsis,
                            style: TextStyle(color: Colors.white.withOpacity(0.95), fontSize: 11, fontWeight: FontWeight.w600))),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              // Frosted glass notification
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.white.withOpacity(0.2)),
                ),
                child: const Icon(Icons.notifications_none_rounded, color: Colors.white, size: 22),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ── SWITCH ACCOUNT SHEET ──
  void _showSwitchAccount() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return Container(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(width: 40, height: 4, decoration: BoxDecoration(color: AppTheme.textLight, borderRadius: BorderRadius.circular(4))),
              const SizedBox(height: 16),
              const Text("Pilih Anak", style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800, color: AppTheme.textDark)),
              const SizedBox(height: 6),
              const Text("Pilih akun anak untuk melihat informasi", style: TextStyle(fontSize: 12, color: AppTheme.textMedium)),
              const SizedBox(height: 18),
              GestureDetector(
                onTap: () => Navigator.pop(context),
                child: Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(colors: [AppTheme.primary.withOpacity(0.06), AppTheme.primary.withOpacity(0.02)]),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: AppTheme.primary.withOpacity(0.3)),
                  ),
                  child: Row(
                    children: [
                      CircleAvatar(
                        radius: 22, backgroundColor: AppTheme.primary.withOpacity(0.1),
                        child: Text(_namaAnak.isNotEmpty ? _namaAnak[0].toUpperCase() : 'B',
                          style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w800, fontSize: 16)),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(_namaAnak, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppTheme.textDark)),
                            const SizedBox(height: 3),
                            Row(children: [
                              const Icon(Icons.school_rounded, size: 13, color: AppTheme.primary),
                              const SizedBox(width: 4),
                              Text(_kelas, style: const TextStyle(fontSize: 11, color: AppTheme.primary)),
                            ]),
                          ],
                        ),
                      ),
                      const Icon(Icons.check_circle_rounded, color: AppTheme.primary, size: 22),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 14),
              GestureDetector(
                onTap: () {
                  Navigator.pop(context);
                  Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()));
                },
                child: Container(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF7F8FC),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: const Color(0xFFE8E8EE)),
                  ),
                  child: const Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.add_rounded, size: 18, color: AppTheme.primary),
                      SizedBox(width: 6),
                      Text("Tambah Akun Anak Baru", style: TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w700, fontSize: 13)),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 14),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: const Color(0xFFF7F8FC), borderRadius: BorderRadius.circular(12)),
                child: const Row(
                  children: [
                    Icon(Icons.verified_user_rounded, size: 16, color: AppTheme.textLight),
                    SizedBox(width: 8),
                    Expanded(child: Text("Data setiap anak terpisah dan aman.", style: TextStyle(fontSize: 11, color: AppTheme.textMedium))),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  // ── MENU CARDS ──
  Widget _menuCards() {
    final bool lunas = _tagihan == null;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Row(
        children: [
          Expanded(child: _cardMenu(
            title: "Bayar SPP",
            subtitle: "Pembayaran SPP bulanan",
            icon: Icons.account_balance_wallet_rounded,
            badge: lunas ? "Lunas" : "Belum Bayar",
            badgeColor: lunas ? AppTheme.success : AppTheme.danger,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => PembayaranScreen(tagihan: _tagihan))),
          )),
          const SizedBox(width: 14),
          Expanded(child: _cardMenu(
            title: "Riwayat",
            subtitle: "Lihat histori pembayaran",
            icon: Icons.receipt_long_rounded,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const HistoryScreen())),
          )),
        ],
      ),
    );
  }

  Widget _cardMenu({required String title, required String subtitle, required IconData icon, String? badge, Color? badgeColor, required VoidCallback onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(22),
          border: Border.all(color: const Color(0xFFF0F0F5), width: 1),
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 20, offset: const Offset(0, 6)),
            BoxShadow(color: AppTheme.primary.withOpacity(0.04), blurRadius: 12, offset: const Offset(0, 3)),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topLeft, end: Alignment.bottomRight,
                      colors: [AppTheme.primary.withOpacity(0.12), AppTheme.primary.withOpacity(0.04)],
                    ),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: AppTheme.primary.withOpacity(0.08)),
                  ),
                  child: Icon(icon, size: 24, color: AppTheme.primary),
                ),
                if (badge != null)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                    decoration: BoxDecoration(
                      color: badgeColor?.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: badgeColor?.withOpacity(0.2) ?? Colors.transparent),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(width: 6, height: 6, decoration: BoxDecoration(
                          color: badgeColor, shape: BoxShape.circle,
                          boxShadow: [BoxShadow(color: badgeColor?.withOpacity(0.5) ?? Colors.transparent, blurRadius: 4)],
                        )),
                        const SizedBox(width: 5),
                        Text(badge, style: TextStyle(color: badgeColor, fontSize: 10, fontWeight: FontWeight.w700)),
                      ],
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 16),
            Text(title, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: AppTheme.textDark, letterSpacing: -0.3)),
            const SizedBox(height: 4),
            Text(subtitle, style: const TextStyle(color: AppTheme.textMedium, fontSize: 12, height: 1.3)),
          ],
        ),
      ),
    );
  }

  // ── PERKEMBANGAN SECTION (ULTRA PREMIUM) ──
  Widget _perkembangan() {
    return GestureDetector(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const PerkembanganScreen())),
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(22),
          border: Border.all(color: const Color(0xFFF0F0F5)),
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 20, offset: const Offset(0, 6)),
            BoxShadow(color: AppTheme.primary.withOpacity(0.04), blurRadius: 12, offset: const Offset(0, 3)),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Top accent bar
            Container(
              height: 4,
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: [AppTheme.primary, AppTheme.primaryLight, AppTheme.primary.withOpacity(0.3)]),
                borderRadius: const BorderRadius.only(topLeft: Radius.circular(22), topRight: Radius.circular(22)),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 18, 20, 20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Container(width: 4, height: 18, decoration: BoxDecoration(color: AppTheme.primary, borderRadius: BorderRadius.circular(2))),
                          const SizedBox(width: 10),
                          const Text("Perkembangan Anak", style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: AppTheme.textDark, letterSpacing: -0.3)),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          gradient: LinearGradient(colors: [AppTheme.primary.withOpacity(0.1), AppTheme.primary.withOpacity(0.04)]),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: AppTheme.primary.withOpacity(0.12)),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text("Detail", style: TextStyle(color: AppTheme.primary, fontSize: 12, fontWeight: FontWeight.w700)),
                            SizedBox(width: 3),
                            Icon(Icons.arrow_forward_ios_rounded, size: 10, color: AppTheme.primary),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  const Text("Ringkasan perkembangan anak", style: TextStyle(fontSize: 12, color: AppTheme.textMedium)),
                  const SizedBox(height: 16),
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: const Color(0xFFF7F8FC),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFEEEFF5)),
                    ),
                    child: _buildChart(),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildChart() {
    final data = _getDataPerTahun();
    if (data.isEmpty) {
      return SizedBox(
        height: 140,
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.show_chart_rounded, size: 40, color: AppTheme.textLight.withOpacity(0.5)),
              const SizedBox(height: 8),
              const Text("Belum ada data", style: TextStyle(color: AppTheme.textMedium, fontSize: 13)),
            ],
          ),
        ),
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

    const namaBulan = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];

    return SizedBox(
      height: 170,
      child: LineChart(
        LineChartData(
          minY: 1,
          maxY: 4,
          gridData: FlGridData(
            show: true, drawVerticalLine: false, horizontalInterval: 1,
            getDrawingHorizontalLine: (value) => FlLine(color: AppTheme.primary.withOpacity(0.06), strokeWidth: 1),
          ),
          borderData: FlBorderData(show: false),
          titlesData: FlTitlesData(
            topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            leftTitles: AxisTitles(
              sideTitles: SideTitles(showTitles: true, interval: 1, reservedSize: 45,
                getTitlesWidget: (value, meta) => Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: Text(convertLabel(value), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w600, color: AppTheme.textMedium)),
                ),
              ),
            ),
            bottomTitles: AxisTitles(
              sideTitles: SideTitles(showTitles: true, interval: 1, reservedSize: 30,
                getTitlesWidget: (value, meta) {
                  final bulan = value.toInt() + 1;
                  if (bulan >= 1 && bulan <= 12) {
                    return Padding(padding: const EdgeInsets.only(top: 6),
                      child: Text(namaBulan[bulan], style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w600, color: AppTheme.textMedium)));
                  }
                  return const Text("");
                },
              ),
            ),
          ),
          lineTouchData: LineTouchData(
            handleBuiltInTouches: true,
            touchTooltipData: LineTouchTooltipData(
              tooltipRoundedRadius: 12, tooltipPadding: const EdgeInsets.all(10), tooltipMargin: 8,
              getTooltipColor: (touchedSpot) => Colors.white,
              getTooltipItems: (spots) {
                final latest = data.last;
                return spots.map((spot) {
                  final index = spot.x.toInt();
                  final item = data[index];
                  final isCurrentMonth = item.bulan == latest.bulan && item.tahun == latest.tahun;
                  String label;
                  switch (item.statusUtama) {
                    case "BB": label = "BB (Belum Berkembang)"; break;
                    case "MB": label = "MB (Mulai Berkembang)"; break;
                    case "BSH": label = "BSH (Sesuai Harapan)"; break;
                    case "BSB": label = "BSB (Sangat Baik)"; break;
                    default: label = item.statusUtama;
                  }
                  if (isCurrentMonth) {
                    return LineTooltipItem("${namaBulan[item.bulan]} ${item.tahun}\n$label",
                      const TextStyle(color: Colors.black, fontWeight: FontWeight.w600, fontSize: 12));
                  }
                  return LineTooltipItem(item.statusUtama, const TextStyle(color: Colors.black, fontWeight: FontWeight.bold));
                }).toList();
              },
            ),
          ),
          lineBarsData: [
            LineChartBarData(
              spots: data.map((e) => FlSpot((e.bulan - 1).toDouble(), convertNilai(e.statusUtama))).toList(),
              isCurved: true, color: AppTheme.primary, barWidth: 3,
              dotData: FlDotData(show: true, checkToShowDot: (spot, barData) => true,
                getDotPainter: (spot, percent, bar, index) => FlDotCirclePainter(radius: 5, color: AppTheme.primary, strokeWidth: 2.5, strokeColor: Colors.white),
              ),
              belowBarData: BarAreaData(show: true, color: AppTheme.primary.withOpacity(0.08)),
            ),
          ],
        ),
      ),
    );
  }

  // ── PENGUMUMAN SECTION (ULTRA PREMIUM) ──
  Widget _pengumumanUI() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: const Color(0xFFF0F0F5)),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 20, offset: const Offset(0, 6)),
          BoxShadow(color: AppTheme.primary.withOpacity(0.04), blurRadius: 12, offset: const Offset(0, 3)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Top accent bar
          Container(
            height: 4,
            decoration: BoxDecoration(
              gradient: LinearGradient(colors: [AppTheme.primary.withOpacity(0.3), AppTheme.primary, AppTheme.primaryLight]),
              borderRadius: const BorderRadius.only(topLeft: Radius.circular(22), topRight: Radius.circular(22)),
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 18, 20, 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Container(width: 4, height: 18, decoration: BoxDecoration(color: AppTheme.primary, borderRadius: BorderRadius.circular(2))),
                        const SizedBox(width: 10),
                        const Text("Pengumuman", style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: AppTheme.textDark, letterSpacing: -0.3)),
                      ],
                    ),
                    GestureDetector(
                      onTap: () => Navigator.push(context, PageRouteBuilder(
                        pageBuilder: (_, __, ___) => const PengumumanScreen(),
                        transitionDuration: Duration.zero, reverseTransitionDuration: Duration.zero,
                      )),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          gradient: LinearGradient(colors: [AppTheme.primary.withOpacity(0.1), AppTheme.primary.withOpacity(0.04)]),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: AppTheme.primary.withOpacity(0.12)),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text("Lihat semua", style: TextStyle(color: AppTheme.primary, fontSize: 12, fontWeight: FontWeight.w700)),
                            SizedBox(width: 3),
                            Icon(Icons.arrow_forward_ios_rounded, size: 10, color: AppTheme.primary),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                if (_pengumuman.isEmpty)
                  Container(
                    padding: const EdgeInsets.symmetric(vertical: 28),
                    child: Center(
                      child: Column(children: [
                        Icon(Icons.campaign_outlined, size: 36, color: AppTheme.textLight.withOpacity(0.4)),
                        const SizedBox(height: 8),
                        const Text('Belum ada pengumuman', style: TextStyle(color: AppTheme.textMedium, fontSize: 13)),
                      ]),
                    ),
                  )
                else
                  ..._pengumuman.map((e) => _buildPengumumanItem(e)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPengumumanItem(PengumumanModel e) {
    return GestureDetector(
      onTap: () => Navigator.push(context, PageRouteBuilder(
        pageBuilder: (_, __, ___) => PengumumanScreen(idPengumuman: e.idPengumuman),
        transitionDuration: Duration.zero, reverseTransitionDuration: Duration.zero,
      )),
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: const Color(0xFFF9FAFB),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFFEEEFF5)),
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Glowing dot indicator
            Container(
              margin: const EdgeInsets.only(top: 5),
              width: 8, height: 8,
              decoration: BoxDecoration(
                color: AppTheme.primary, shape: BoxShape.circle,
                boxShadow: [BoxShadow(color: AppTheme.primary.withOpacity(0.4), blurRadius: 6)],
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(e.judul, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppTheme.textDark, letterSpacing: -0.2)),
                  const SizedBox(height: 5),
                  Text(e.deskripsi, maxLines: 2, overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 12, color: AppTheme.textMedium, height: 1.5)),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      Icon(Icons.access_time_rounded, size: 12, color: AppTheme.textLight.withOpacity(0.6)),
                      const SizedBox(width: 4),
                      Text(e.waktuUnggah, style: TextStyle(fontSize: 10, color: AppTheme.textLight.withOpacity(0.8), fontWeight: FontWeight.w600)),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(width: 8),
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppTheme.primary.withOpacity(0.06),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(Icons.arrow_forward_ios_rounded, size: 12, color: AppTheme.primary),
            ),
          ],
        ),
      ),
    );
  }
}