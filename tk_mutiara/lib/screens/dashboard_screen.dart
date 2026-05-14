import 'package:flutter/material.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../models/perkembangan_model.dart';
import '../services/api_services.dart';
import 'main_navigation_screen.dart';
import 'change_password_screen.dart';
import 'welcome_screen.dart';
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
      body: RefreshIndicator(
          color: AppTheme.primary,
          edgeOffset: 80, // Spinner muncul di bawah profil agar tidak menutupi wajah/nama
          onRefresh: () async {
            await Future.wait([
              ApiService.getPembayaran().then((p) { if (mounted) setState(() => _payments = p); }),
              ApiService.getPengumuman().then((pg) { if (mounted) setState(() => _pengumuman = pg.take(2).toList()); }),
              ApiService.getPerkembangan().then((pk) { if (mounted) setState(() => _perkembanganData = pk); }),
            ]);
          },
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(parent: ClampingScrollPhysics()),
            child: Column(
              children: [
                _wrappedHeader(),
                // Body content area that wraps 'inward' into the header
                Transform.translate(
                  offset: const Offset(0, -32),
                  child: Container(
                    padding: const EdgeInsets.only(top: 32),
                    decoration: const BoxDecoration(
                      color: Color(0xFFF7F8FC),
                      borderRadius: BorderRadius.only(
                        topLeft: Radius.circular(40),
                        topRight: Radius.circular(40),
                      ),
                    ),
                    child: Column(
                      children: [
                        if (_tagihan != null) ...[
                          _tagihanCard(),
                          const SizedBox(height: 20),
                        ],
                        _perkembangan(),
                        const SizedBox(height: 20),
                        _pengumumanUI(),
                        const SizedBox(height: 24),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
    );
  }

  // ── WRAPPED HEADER ──
  Widget _wrappedHeader() {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft, end: Alignment.bottomRight,
          colors: [Color(0xFFFF6B1A), Color(0xFFFF8840), Color(0xFFFFA05C)],
          stops: [0.0, 0.55, 1.0],
        ),
        borderRadius: BorderRadius.zero,
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(22, 12, 22, 64),
          child: Column(
            children: [
              // Profile + Notification row
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(3),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.white.withOpacity(0.3), width: 2),
                    ),
                    child: CircleAvatar(
                      radius: 26,
                      backgroundColor: Colors.white.withOpacity(0.2),
                      child: Text(
                        _namaAnak.isNotEmpty ? _namaAnak[0].toUpperCase() : 'B',
                        style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w900),
                      ),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(_getGreeting(), style: TextStyle(color: Colors.white.withOpacity(0.8), fontSize: 13, fontWeight: FontWeight.w500)),
                        const SizedBox(height: 2),
                        InkWell(
                          onTap: _showSwitchAccount,
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Flexible(child: Text(_namaAnak, style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900, letterSpacing: -0.5), overflow: TextOverflow.ellipsis)),
                              const SizedBox(width: 4),
                              Icon(Icons.keyboard_arrow_down_rounded, color: Colors.white.withOpacity(0.8), size: 18),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), borderRadius: BorderRadius.circular(14)),
                    child: const Icon(Icons.notifications_none_rounded, color: Colors.white, size: 22),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              // Class badge
              Align(
                alignment: Alignment.centerLeft,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
                  decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), borderRadius: BorderRadius.circular(25)),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.school_rounded, size: 14, color: Colors.white.withOpacity(0.9)),
                      const SizedBox(width: 8),
                      Flexible(child: Text(_kelas, style: TextStyle(color: Colors.white.withOpacity(0.95), fontSize: 11, fontWeight: FontWeight.w700), overflow: TextOverflow.ellipsis)),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),
              // Payment & History Cards
              Row(
                children: [
                  Expanded(child: _wrappedActionCard(
                    title: 'Bayar SPP',
                    subtitle: 'Pembayaran SPP bulanan',
                    icon: Icons.account_balance_wallet_rounded,
                    onTap: () {
                      Navigator.push(context, PageRouteBuilder(
                        pageBuilder: (_, __, ___) => PembayaranScreen(onBackPressed: () => Navigator.pop(context)),
                        transitionDuration: Duration.zero, reverseTransitionDuration: Duration.zero,
                      ));
                    },
                    badge: _tagihan == null ? 'Lunas' : 'Belum Bayar',
                    badgeColor: _tagihan == null ? const Color(0xFF4ADE80) : const Color(0xFFF87171),
                  )),
                  const SizedBox(width: 14),
                  Expanded(child: _wrappedActionCard(
                    title: 'Riwayat',
                    subtitle: 'Lihat riwayat pembayaran',
                    icon: Icons.receipt_long_rounded,
                    onTap: () {
                      Navigator.push(context, PageRouteBuilder(
                        pageBuilder: (_, __, ___) => HistoryScreen(onBackPressed: () => Navigator.pop(context)),
                        transitionDuration: Duration.zero, reverseTransitionDuration: Duration.zero,
                      ));
                    },
                  )),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _wrappedActionCard({required String title, required String subtitle, required IconData icon, required VoidCallback onTap, String? badge, Color? badgeColor}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 12, offset: const Offset(0, 4))],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(color: AppTheme.primary.withOpacity(0.08), borderRadius: BorderRadius.circular(14)),
                  child: Icon(icon, color: AppTheme.primary, size: 22),
                ),
                if (badge != null)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(color: badgeColor!.withOpacity(0.12), borderRadius: BorderRadius.circular(10)),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(width: 5, height: 5, decoration: BoxDecoration(color: badgeColor, shape: BoxShape.circle)),
                        const SizedBox(width: 4),
                        Text(badge, style: TextStyle(color: badgeColor, fontSize: 10, fontWeight: FontWeight.w800)),
                      ],
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 16),
            Text(title, style: const TextStyle(color: AppTheme.textDark, fontSize: 15, fontWeight: FontWeight.w900, letterSpacing: -0.3)),
            const SizedBox(height: 2),
            Text(subtitle, style: const TextStyle(color: AppTheme.textLight, fontSize: 10, height: 1.4)),
          ],
        ),
      ),
    );
  }

  // ── PROFILE OPTIONS SHEET (UBAR PASSWORD & LOGOUT) ──
  void _showSwitchAccount() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return Container(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 32),
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(32)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(width: 40, height: 4, decoration: BoxDecoration(color: AppTheme.textLight.withOpacity(0.3), borderRadius: BorderRadius.circular(4))),
              const SizedBox(height: 24),
              const Text("Pengaturan Akun", style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: -0.5)),
              const SizedBox(height: 8),
              Text("Kelola keamanan dan sesi akun Anda", style: TextStyle(fontSize: 13, color: AppTheme.textMedium.withOpacity(0.8))),
              const SizedBox(height: 32),
              
              // 1. Change Password Option
              _buildOptionItem(
                title: "Ubah Password",
                subtitle: "Ganti kata sandi akun Anda",
                icon: Icons.lock_reset_rounded,
                color: AppTheme.primary,
                onTap: () {
                  Navigator.pop(context);
                  Navigator.push(context, MaterialPageRoute(builder: (_) => const ChangePasswordScreen()));
                },
              ),
              const SizedBox(height: 12),
              
              // 2. Logout Option
              _buildOptionItem(
                title: "Keluar Aplikasi",
                subtitle: "Akhiri sesi dan keluar",
                icon: Icons.logout_rounded,
                color: AppTheme.danger,
                onTap: () {
                  Navigator.pop(context);
                  ApiService.logout();
                  Navigator.pushAndRemoveUntil(
                    context, 
                    MaterialPageRoute(builder: (_) => const WelcomeScreen()),
                    (route) => false,
                  );
                },
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildOptionItem({
    required String title,
    required String subtitle,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: color.withOpacity(0.04),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: color.withOpacity(0.08)),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(icon, color: color, size: 22),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: AppTheme.textDark)),
                  const SizedBox(height: 2),
                  Text(subtitle, style: TextStyle(fontSize: 12, color: AppTheme.textMedium.withOpacity(0.7))),
                ],
              ),
            ),
            Icon(Icons.arrow_forward_ios_rounded, size: 14, color: AppTheme.textLight.withOpacity(0.4)),
          ],
        ),
      ),
    );
  }

  // ── PENGUMUMAN SECTION (PREMIUM FEED) ──
  Widget _pengumumanUI() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Row(
                children: [
                  Text(
                    "Pengumuman",
                    style: TextStyle(
                      fontWeight: FontWeight.w900,
                      fontSize: 18,
                      color: AppTheme.textDark,
                      letterSpacing: -0.5,
                    ),
                  ),
                ],
              ),
              GestureDetector(
                onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const PengumumanScreen())),
                child: const Row(
                  children: [
                    Text(
                      "Lihat semua",
                      style: TextStyle(
                        color: AppTheme.primary,
                        fontSize: 13,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    SizedBox(width: 4),
                    Icon(Icons.arrow_forward_rounded, size: 14, color: AppTheme.primary),
                  ],
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 8),
        if (_pengumuman.isEmpty)
          Container(
            width: double.infinity,
            margin: const EdgeInsets.symmetric(horizontal: 20),
            padding: const EdgeInsets.symmetric(vertical: 40),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: const Color(0xFFF1F5F9)),
            ),
            child: Column(
              children: [
                Icon(Icons.campaign_outlined, size: 48, color: AppTheme.textLight.withOpacity(0.3)),
                const SizedBox(height: 12),
                const Text('Belum ada pengumuman', style: TextStyle(color: AppTheme.textMedium, fontSize: 13, fontWeight: FontWeight.w500)),
              ],
            ),
          )
        else
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Column(
              children: [
                // 1. Featured Item
                _buildFeaturedPengumuman(_pengumuman[0]),
                
                // 2. Secondary Items
                if (_pengumuman.length > 1) ...[
                  const SizedBox(height: 12),
                  Container(
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 20, offset: const Offset(0, 4)),
                      ],
                    ),
                    child: Column(
                      children: _pengumuman.sublist(1).asMap().entries.map((entry) {
                        final isLast = entry.key == _pengumuman.length - 2;
                        return Column(
                          children: [
                            _buildSecondaryPengumuman(entry.value),
                            if (!isLast)
                              Padding(
                                padding: const EdgeInsets.symmetric(horizontal: 20),
                                child: Divider(height: 1, color: Colors.black.withOpacity(0.05)),
                              ),
                          ],
                        );
                      }).toList(),
                    ),
                  ),
                ],
              ],
            ),
          ),
      ],
    );
  }

  Widget _buildFeaturedPengumuman(PengumumanModel e) {
    return GestureDetector(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => PengumumanScreen(idPengumuman: e.idPengumuman))),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              AppTheme.primary.withOpacity(0.08),
              AppTheme.primary.withOpacity(0.03),
            ],
          ),
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: AppTheme.primary.withOpacity(0.12)),
          boxShadow: [
            BoxShadow(color: AppTheme.primary.withOpacity(0.04), blurRadius: 20, offset: const Offset(0, 8)),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppTheme.primary.withOpacity(0.15),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Text(
                    "TERBARU",
                    style: TextStyle(
                      color: AppTheme.primary,
                      fontSize: 10,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 0.5,
                    ),
                  ),
                ),
                const Spacer(),
                Icon(Icons.arrow_forward_ios_rounded, size: 14, color: AppTheme.primary.withOpacity(0.5)),
              ],
            ),
            const SizedBox(height: 16),
            Text(
              e.judul,
              style: const TextStyle(
                fontWeight: FontWeight.w900,
                fontSize: 18,
                color: AppTheme.textDark,
                letterSpacing: -0.4,
                height: 1.3,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              e.deskripsi,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                fontSize: 13,
                color: AppTheme.textDark.withOpacity(0.7),
                height: 1.6,
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Icon(Icons.calendar_today_rounded, size: 12, color: AppTheme.textLight.withOpacity(0.8)),
                const SizedBox(width: 6),
                Text(
                  e.waktuUnggah,
                  style: TextStyle(
                    fontSize: 11,
                    color: AppTheme.textLight.withOpacity(0.9),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSecondaryPengumuman(PengumumanModel e) {
    return GestureDetector(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => PengumumanScreen(idPengumuman: e.idPengumuman))),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(20),
        color: Colors.transparent, // For hit testing
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    e.judul,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      fontWeight: FontWeight.w700,
                      fontSize: 14,
                      color: AppTheme.textDark,
                      letterSpacing: -0.2,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    e.waktuUnggah,
                    style: TextStyle(
                      fontSize: 11,
                      color: AppTheme.textLight.withOpacity(0.7),
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 12),
            Icon(Icons.arrow_forward_ios_rounded, size: 12, color: AppTheme.textLight.withOpacity(0.4)),
          ],
        ),
      ),
    );
  }

  // ── TAGIHAN CARD ──
  Widget _tagihanCard() {
    if (_tagihan == null) return const SizedBox.shrink();
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: AppTheme.danger.withOpacity(0.1)),
        boxShadow: [
          BoxShadow(color: AppTheme.danger.withOpacity(0.05), blurRadius: 16, offset: const Offset(0, 4)),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: AppTheme.danger.withOpacity(0.1),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Icon(Icons.warning_amber_rounded, color: AppTheme.danger, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("Tagihan Belum Bayar", style: TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: AppTheme.textDark)),
                const SizedBox(height: 2),
                Text("Segera lakukan pembayaran untuk ${_tagihan!.periode}", style: const TextStyle(color: AppTheme.textMedium, fontSize: 11)),
              ],
            ),
          ),
          const SizedBox(width: 8),
          ElevatedButton(
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => PembayaranScreen(tagihan: _tagihan))),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.danger,
              foregroundColor: Colors.white,
              elevation: 0,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 0),
              minimumSize: const Size(0, 36),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            child: const Text("Bayar", style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
          ),
        ],
      ),
    );
  }

  // ── PERKEMBANGAN SECTION (ULTRA PREMIUM) ──
  Widget _perkembangan() {
    final data = _getDataPerTahun();
    final latestStatus = data.isNotEmpty ? data.last.statusUtama : '-';
    
    return GestureDetector(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const PerkembanganScreen())),
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(28),
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 30, offset: const Offset(0, 10)),
            BoxShadow(color: AppTheme.primary.withOpacity(0.04), blurRadius: 15, offset: const Offset(0, 5)),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 24, 24, 16),
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
                            decoration: BoxDecoration(
                              color: AppTheme.primary.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: const Icon(Icons.analytics_rounded, color: AppTheme.primary, size: 20),
                          ),
                          const SizedBox(width: 12),
                          const Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text("Perkembangan", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textDark, letterSpacing: -0.5)),
                              Text("Statistik pencapaian anak", style: TextStyle(fontSize: 11, color: AppTheme.textMedium, fontWeight: FontWeight.w500)),
                            ],
                          ),
                        ],
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),
                  Container(
                    height: 180,
                    width: double.infinity,
                    padding: const EdgeInsets.only(right: 16, top: 10),
                    child: _buildChart(),
                  ),
                  const SizedBox(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        decoration: BoxDecoration(
                          color: const Color(0xFFF7F8FC),
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: Row(
                          children: [
                            const Text("Lihat Laporan Detail", style: TextStyle(color: AppTheme.primary, fontSize: 12, fontWeight: FontWeight.w700)),
                            const SizedBox(width: 6),
                            Icon(Icons.arrow_forward_rounded, size: 14, color: AppTheme.primary.withOpacity(0.8)),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case "BSB": return const Color(0xFF3B82F6);
      case "BSH": return const Color(0xFF10B981);
      case "MB": return const Color(0xFFF59E0B);
      case "BB": return const Color(0xFFEF4444);
      default: return AppTheme.textMedium;
    }
  }

  Widget _buildChart() {
    final data = _getDataPerTahun();
    if (data.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.bubble_chart_rounded, size: 48, color: AppTheme.textLight.withOpacity(0.3)),
            const SizedBox(height: 12),
            const Text("Belum ada statistik tersedia", style: TextStyle(color: AppTheme.textLight, fontSize: 13, fontWeight: FontWeight.w500)),
          ],
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

    const namaBulan = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];

    return LineChart(
      LineChartData(
        minY: 0.8,
        maxY: 4.2,
        gridData: FlGridData(
          show: true,
          drawVerticalLine: false,
          horizontalInterval: 1,
          getDrawingHorizontalLine: (value) => FlLine(
            color: Colors.black.withOpacity(0.04),
            strokeWidth: 1,
            dashArray: [5, 5],
          ),
        ),
        borderData: FlBorderData(show: false),
        titlesData: FlTitlesData(
          topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
          rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
          leftTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              interval: 1,
              reservedSize: 35,
              getTitlesWidget: (value, meta) {
                if (value % 1 != 0) return const SizedBox.shrink();
                String label = "";
                switch (value.toInt()) {
                  case 1: label = "BB"; break;
                  case 2: label = "MB"; break;
                  case 3: label = "BSH"; break;
                  case 4: label = "BSB"; break;
                  default: return const SizedBox.shrink();
                }
                return SideTitleWidget(
                  axisSide: meta.axisSide,
                  child: Text(label, style: TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: AppTheme.textLight.withOpacity(0.8))),
                );
              },
            ),
          ),
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              interval: 1,
              reservedSize: 22,
              getTitlesWidget: (value, meta) {
                final index = value.toInt();
                if (index >= 0 && index < data.length) {
                  final bulanIndex = data[index].bulan;
                  if (bulanIndex >= 1 && bulanIndex <= 12) {
                    return SideTitleWidget(
                      axisSide: meta.axisSide,
                      child: Text(namaBulan[bulanIndex], style: TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: AppTheme.textLight.withOpacity(0.8))),
                    );
                  }
                }
                return const Text("");
              },
            ),
          ),
        ),
        lineTouchData: LineTouchData(
          handleBuiltInTouches: true,
          touchTooltipData: LineTouchTooltipData(
            getTooltipColor: (spot) => Colors.white,
            tooltipRoundedRadius: 12,
            tooltipPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            tooltipBorder: BorderSide(color: AppTheme.primary.withOpacity(0.1), width: 1),
            getTooltipItems: (spots) {
              return spots.map((spot) {
                final item = data[spot.x.toInt()];
                String longStatus = "";
                switch (item.statusUtama) {
                  case "BB": longStatus = "Belum Berkembang"; break;
                  case "MB": longStatus = "Mulai Berkembang"; break;
                  case "BSH": longStatus = "Sesuai Harapan"; break;
                  case "BSB": longStatus = "Sangat Baik"; break;
                }
                return LineTooltipItem(
                  "${namaBulan[item.bulan]}\n$longStatus",
                  const TextStyle(color: AppTheme.textDark, fontWeight: FontWeight.w800, fontSize: 11),
                );
              }).toList();
            },
          ),
        ),
        lineBarsData: [
          LineChartBarData(
            spots: data.asMap().entries.map((e) => FlSpot(e.key.toDouble(), convertNilai(e.value.statusUtama))).toList(),
            isCurved: true,
            curveSmoothness: 0.35,
            color: AppTheme.primary,
            barWidth: 3.5,
            isStrokeCapRound: true,
            dotData: FlDotData(
              show: true,
              getDotPainter: (spot, percent, bar, index) => FlDotCirclePainter(
                radius: 4,
                color: Colors.white,
                strokeWidth: 2,
                strokeColor: AppTheme.primary,
              ),
            ),
            belowBarData: BarAreaData(
              show: true,
              gradient: LinearGradient(
                colors: [
                  AppTheme.primary.withOpacity(0.2),
                  AppTheme.primary.withOpacity(0.0),
                ],
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
              ),
            ),
          ),
        ],
      ),
    );
  }
}