import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../widgets/menu_card.dart';
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../services/api_services.dart';
import 'perkembangan_screen.dart';
import 'pembayaran_screen.dart';
import 'pengumuman_screen.dart';
import 'history_screen.dart';
import 'profil_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen>
    with SingleTickerProviderStateMixin {

  String _namaAnak = 'Bintang Mutiara';
  String _inisial = 'BM';

  late AnimationController _animController;
  late List<Animation<Offset>> _slideAnims;
  late Animation<double> _fadeAnim;

  final List<PembayaranModel> _payments = PembayaranModel.dummyHistory();
  final List<PengumumanModel> _pengumuman = PengumumanModel.dummyData();

  int get _unreadPengumuman => _pengumuman.where((p) => !p.isRead).length;

  PembayaranModel? get _tagihan =>
      _payments.firstWhere((p) => p.isBelum, orElse: () => _payments.first);

  @override
  void initState() {
    super.initState();
    
    // Get user info from ApiService
    final userInfo = ApiService.userInfo;
    if (userInfo != null) {
      _namaAnak = userInfo['nama_siswa'] ?? 'User';
      // Buat inisial dari nama
      final names = _namaAnak.split(' ');
      _inisial = (names.map((n) => n[0]).join('').toUpperCase());
      print('✓ Dashboard loaded for: $_namaAnak (Inisial: $_inisial)');
    }
    
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    );

    _slideAnims = List.generate(
      6,
      (i) => Tween<Offset>(
        begin: const Offset(0, 0.3),
        end: Offset.zero,
      ).animate(CurvedAnimation(
        parent: _animController,
        curve: Interval(i * 0.1, 0.6 + i * 0.08, curve: Curves.easeOutCubic),
      )),
    );

    _fadeAnim = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(parent: _animController, curve: const Interval(0, 0.6)),
    );

    _animController.forward();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: SafeArea(
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            // === HEADER ===
            SliverToBoxAdapter(
              child: FadeTransition(
                opacity: _fadeAnim,
                child: _buildHeader(),
              ),
            ),

            // === CARD PERKEMBANGAN ANAK (ganti dari SPP) ===
            SliverToBoxAdapter(
              child: SlideTransition(
                position: _slideAnims[0],
                child: FadeTransition(
                  opacity: _fadeAnim,
                  child: _buildPerkembanganCard(),
                ),
              ),
            ),

            // === SECTION TITLE: MENU ===
            SliverToBoxAdapter(
              child: SlideTransition(
                position: _slideAnims[1],
                child: FadeTransition(
                  opacity: _fadeAnim,
                  child: const Padding(
                    padding: EdgeInsets.fromLTRB(20, 24, 20, 12),
                    child: Text(
                      'Menu Utama',
                      style: TextStyle(
                        color: AppTheme.textDark,
                        fontSize: 18,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ),
                ),
              ),
            ),

            // === MENU GRID - 2 BOX SAJA ===
            SliverPadding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              sliver: SliverGrid(
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  crossAxisSpacing: 14,
                  mainAxisSpacing: 14,
                  childAspectRatio: 0.88,
                ),
                delegate: SliverChildListDelegate([
                  // Box 1: Bayar SPP
                  SlideTransition(
                    position: _slideAnims[2],
                    child: FadeTransition(
                      opacity: _fadeAnim,
                      child: MenuCard(
                        title: 'Bayar SPP',
                        subtitle: 'Pembayaran mudah & cepat',
                        icon: Icons.payment_rounded,
                        color: const Color(0xFF22C55E),
                        iconBg: const Color(0xFFDCFCE7),
                        onTap: () => Navigator.push(
                          context,
                          _pageRoute(PembayaranScreen(tagihan: _tagihan!)),
                        ),
                        badge: _tagihan?.isBelum == true ? 'Belum' : null,
                      ),
                    ),
                  ),
                  // Box 2: Riwayat Pembayaran
                  SlideTransition(
                    position: _slideAnims[2],
                    child: FadeTransition(
                      opacity: _fadeAnim,
                      child: MenuCard(
                        title: 'Riwayat\nPembayaran',
                        subtitle: 'Cek histori transaksi SPP',
                        icon: Icons.receipt_long_rounded,
                        color: const Color(0xFFF59E0B),
                        iconBg: const Color(0xFFFEF3C7),
                        onTap: () => Navigator.push(
                          context,
                          _pageRoute(HistoryScreen(payments: _payments)),
                        ),
                      ),
                    ),
                  ),
                ]),
              ),
            ),

            // === SECTION TITLE: PENGUMUMAN ===
            SliverToBoxAdapter(
              child: SlideTransition(
                position: _slideAnims[3],
                child: FadeTransition(
                  opacity: _fadeAnim,
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 24, 20, 12),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Pengumuman',
                          style: TextStyle(
                            color: AppTheme.textDark,
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                        GestureDetector(
                          onTap: () => Navigator.push(
                            context,
                            _pageRoute(
                                PengumumanScreen(pengumuman: _pengumuman)),
                          ),
                          child: Text(
                            'Lihat Semua',
                            style: TextStyle(
                              color: AppTheme.primary,
                              fontSize: 13,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),

            // === LIST PENGUMUMAN LENGKAP ===
            SliverPadding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              sliver: SliverList(
                delegate: SliverChildBuilderDelegate(
                  (context, i) => SlideTransition(
                    position: _slideAnims[4],
                    child: FadeTransition(
                      opacity: _fadeAnim,
                      child: _buildPengumumanItem(_pengumuman[i]),
                    ),
                  ),
                  childCount: _pengumuman.length,
                ),
              ),
            ),

            const SliverToBoxAdapter(child: SizedBox(height: 32)),
          ],
        ),
      ),
    );
  }

  // =============================================
  // CARD PERKEMBANGAN ANAK (menggantikan card SPP)
  // =============================================
  Widget _buildPerkembanganCard() {
  return GestureDetector(
    onTap: () => Navigator.push(
      context,
      _pageRoute(const PerkembanganScreen()),
    ),
    child: Container(
      margin: const EdgeInsets.fromLTRB(20, 20, 20, 0),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFFFF7A2F), // 🔥 warna fix
        borderRadius: BorderRadius.circular(24),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Perkembangan Anak',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  '$_namaAnak ⭐',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 4),
                const Text(
                  'Kelas A · TK Mutiara',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 13,
                  ),
                ),
                const SizedBox(height: 14),

                // tombol putih
                Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 16, vertical: 10),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: const Text(
                    'Lihat Perkembangan →',
                    style: TextStyle(
                      color: Color(0xFFFF7A2F),
                      fontWeight: FontWeight.w700,
                      fontSize: 13,
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(width: 10),

          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(18),
            ),
            child: const Icon(
              Icons.child_care_rounded,
              color: Colors.white,
              size: 30,
            ),
          )
        ],
      ),
    ),
  );
}

  // =============================================
  // HEADER
  // =============================================
  Widget _buildHeader() {
  return Container(
    padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
    child: Row(
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Halo, $_namaAnak! 👋',
                style: const TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w900,
                  color: AppTheme.textDark,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                'Pantau perkembangan $_namaAnak hari ini',
                style: const TextStyle(
                  fontSize: 13,
                  color: AppTheme.textMedium,
                ),
              ),
            ],
          ),
        ),
        _buildAvatar(),
      ],
    ),
  );
}

  // =============================================
  // AVATAR
  // =============================================
  Widget _buildAvatar() {
    return GestureDetector(
      onTap: () => Navigator.push(
        context,
        _pageRoute(ProfilScreen(
          namaAwal: _namaAnak,
          emailAwal: 'anak@tkmutiara.com',
          onProfilUpdated: (nama, email) {
            setState(() {
              _namaAnak = nama;
              _inisial = _getInitials(nama);
            });
          },
        )),
      ),
      child: Container(
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          border: Border.all(color: AppTheme.primary, width: 2.5),
          boxShadow: AppTheme.softShadow,
        ),
        child: CircleAvatar(
          radius: 26,
          backgroundColor: const Color(0xFFFFEDE0),
          child: Text(
            _inisial,
            style: TextStyle(
              color: AppTheme.primary,
              fontWeight: FontWeight.w800,
              fontSize: 15,
            ),
          ),
        ),
      ),
    );
  }

  // =============================================
  // ITEM PENGUMUMAN
  // =============================================
  Widget _buildPengumumanItem(PengumumanModel p) {
    final colors = {
      'penting': const Color(0xFFEF4444),
      'kegiatan': const Color(0xFF6366F1),
      'info': const Color(0xFF22C55E),
    };
    final color = colors[p.kategori] ?? AppTheme.primary;

    return GestureDetector(
      onTap: () => Navigator.push(
        context,
        _pageRoute(PengumumanScreen(
          pengumuman: _pengumuman,
          selectedId: p.id,
        )),
      ),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppTheme.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: AppTheme.cardShadowList,
        ),
        child: Row(
          children: [
            Container(
              width: 4,
              height: 50,
              decoration: BoxDecoration(
                color: color,
                borderRadius: BorderRadius.circular(4),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      if (!p.isRead)
                        Container(
                          width: 7,
                          height: 7,
                          margin: const EdgeInsets.only(right: 6),
                          decoration: BoxDecoration(
                            color: color,
                            shape: BoxShape.circle,
                          ),
                        ),
                      Expanded(
                        child: Text(
                          p.judul,
                          style: TextStyle(
                            color: AppTheme.textDark,
                            fontSize: 13,
                            fontWeight: p.isRead
                                ? FontWeight.w600
                                : FontWeight.w800,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Text(
                    p.isi,
                    style: const TextStyle(
                      color: AppTheme.textMedium,
                      fontSize: 11,
                      fontWeight: FontWeight.w500,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 6),
                  Text(
                    p.tanggal,
                    style: TextStyle(
                      color: color,
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 8),
            const Icon(Icons.chevron_right_rounded,
                color: AppTheme.textLight, size: 20),
          ],
        ),
      ),
    );
  }

  // =============================================
  // HELPERS
  // =============================================
  String _getInitials(String name) {
    final parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }
    return name.isNotEmpty ? name[0].toUpperCase() : '?';
  }

  PageRouteBuilder _pageRoute(Widget page) {
    return PageRouteBuilder(
      pageBuilder: (_, __, ___) => page,
      transitionsBuilder: (_, anim, __, child) {
        return SlideTransition(
          position: Tween<Offset>(
            begin: const Offset(1, 0),
            end: Offset.zero,
          ).animate(CurvedAnimation(parent: anim, curve: Curves.easeOutCubic)),
          child: child,
        );
      },
      transitionDuration: const Duration(milliseconds: 350),
    );
  }
}