import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:google_fonts/google_fonts.dart';
import '../cubit/bottom_nav/bottom_nav_cubit.dart';
import '../theme/app_theme.dart';
import 'dashboard_screen.dart';
import 'perkembangan_screen.dart';
import 'pembayaran_screen.dart';
import 'history_screen.dart';
import 'pengumuman_screen.dart';

class MainNavigationScreen extends StatefulWidget {
  const MainNavigationScreen({super.key});

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  final BottomNavCubit _bottomNavCubit = BottomNavCubit();

  // Kembali ke halaman sebelumnya
  void _goBack() {
    _bottomNavCubit.changeTab(0);
  }

  // Screen untuk setiap tab
  late final List<Widget> _screens = [
    const DashboardScreen(),
    PerkembanganScreen(onBackPressed: _goBack),
    PembayaranScreen(onBackPressed: _goBack),
    HistoryScreen(onBackPressed: _goBack),
    PengumumanScreen(onBackPressed: _goBack),
  ];

  @override
  void dispose() {
    _bottomNavCubit.close();
    super.dispose();
  }

  static const _navItems = [
    _NavItem(icon: Icons.home_outlined, activeIcon: Icons.home_rounded, label: 'Beranda'),
    _NavItem(icon: Icons.show_chart_rounded, activeIcon: Icons.show_chart_rounded, label: 'Perkembangan'),
    _NavItem(icon: Icons.account_balance_wallet_outlined, activeIcon: Icons.account_balance_wallet_rounded, label: 'SPP'),
    _NavItem(icon: Icons.receipt_long_outlined, activeIcon: Icons.receipt_long_rounded, label: 'Riwayat'),
    _NavItem(icon: Icons.campaign_outlined, activeIcon: Icons.campaign_rounded, label: 'Pengumuman'),
  ];

  @override
  Widget build(BuildContext context) {
    return BlocProvider.value(
      value: _bottomNavCubit,
      child: BlocBuilder<BottomNavCubit, int>(
        builder: (context, currentIndex) {
          return Scaffold(
            backgroundColor: AppTheme.background,
            body: IndexedStack(index: currentIndex, children: _screens),
            extendBody: true,
            bottomNavigationBar: _buildBottomNav(currentIndex),
          );
        },
      ),
    );
  }

  Widget _buildBottomNav(int currentIndex) {
    return Container(
      padding: const EdgeInsets.only(bottom: 8),
      child: Container(
        height: 80,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.zero,
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 24, offset: const Offset(0, -4)),
            BoxShadow(color: AppTheme.primary.withOpacity(0.04), blurRadius: 16, offset: const Offset(0, -2)),
          ],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceAround,
          children: List.generate(5, (index) {
            if (index == 2) return _buildCenterButton(currentIndex);
            return _buildNavItem(index, currentIndex);
          }),
        ),
      ),
    );
  }

  Widget _buildNavItem(int index, int currentIndex) {
    final isActive = currentIndex == index;
    final item = _navItems[index];

    return GestureDetector(
      onTap: () => _bottomNavCubit.changeTab(index),
      behavior: HitTestBehavior.opaque,
      child: AnimatedSlide(
        duration: const Duration(milliseconds: 250),
        curve: Curves.easeOutCubic,
        offset: Offset(0, isActive ? -0.05 : 0),
        child: SizedBox(
          width: 72,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Animated icon
              AnimatedContainer(
                duration: const Duration(milliseconds: 250),
                curve: Curves.easeOutCubic,
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: isActive ? AppTheme.primary.withOpacity(0.1) : Colors.transparent,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(
                  isActive ? item.activeIcon : item.icon,
                  size: 22,
                  color: isActive ? AppTheme.primary : const Color(0xFFB8BCC8),
                ),
              ),
              const SizedBox(height: 2),
              // Label
              AnimatedDefaultTextStyle(
                duration: const Duration(milliseconds: 200),
                style: GoogleFonts.montserrat(
                  fontSize: 9,
                  fontWeight: FontWeight.bold,
                  color: isActive ? AppTheme.primary : const Color(0xFFB8BCC8),
                  letterSpacing: -0.2,
                ),
                child: Text(item.label, maxLines: 1, overflow: TextOverflow.ellipsis),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Card SPP
  Widget _buildCenterButton(int currentIndex) {
    final isActive = currentIndex == 2;
    return GestureDetector(
      onTap: () => _bottomNavCubit.changeTab(2),
      behavior: HitTestBehavior.opaque,
      child: SizedBox(
        width: 72,
        height: 80,
        child: Stack(
          clipBehavior: Clip.none,
          alignment: Alignment.topCenter,
          children: [
            Positioned(
              top: -16,
              child: AnimatedScale(
                duration: const Duration(milliseconds: 250),
                curve: Curves.easeOutCubic,
                scale: isActive ? 1.04 : 1.0,
                child: Container(
                  width: 62,
                  height: 62,
                  padding: const EdgeInsets.all(5),
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: Colors.white,
                    boxShadow: [
                      BoxShadow(
                        color: AppTheme.primary.withOpacity(isActive ? 0.22 : 0.14),
                        blurRadius: isActive ? 18 : 14,
                        offset: const Offset(0, 7),
                      ),
                      BoxShadow(
                        color: Colors.black.withOpacity(0.06),
                        blurRadius: 12,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: DecoratedBox(
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      gradient: LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors: isActive
                            ? [const Color(0xFFFF7A1A), const Color(0xFFFF5B11)]
                            : [AppTheme.primaryLight, AppTheme.primary],
                      ),
                    ),
                    child: Icon(
                      isActive ? Icons.account_balance_wallet_rounded : Icons.account_balance_wallet_outlined,
                      color: Colors.white,
                      size: isActive ? 30 : 28,
                    ),
                  ),
                ),
              ),
            ),
            Positioned(
              bottom: 17,
              child: AnimatedDefaultTextStyle(
                duration: const Duration(milliseconds: 200),
                style: GoogleFonts.montserrat(
                  fontSize: 9,
                  fontWeight: FontWeight.bold,
                  color: isActive ? AppTheme.primary : const Color(0xFFB8BCC8),
                  letterSpacing: 0,
                ),
                child: const Text('Bayar SPP', maxLines: 1, overflow: TextOverflow.ellipsis),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _NavItem {
  final IconData icon;
  final IconData activeIcon;
  final String label;
  const _NavItem({required this.icon, required this.activeIcon, required this.label});
}
