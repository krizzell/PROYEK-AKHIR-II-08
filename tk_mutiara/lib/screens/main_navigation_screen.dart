import 'package:flutter/material.dart';
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
  int _currentIndex = 0;

  // Fungsi untuk back ke tab sebelumnya atau Home
  void _goBack() {
    setState(() {
      if (_currentIndex != 0) {
        _currentIndex = 0;
      }
    });
  }

  // Screen untuk setiap tab
  late final List<Widget> _screens = [
    const DashboardScreen(),
    PerkembanganScreen(onBackPressed: _goBack),
    PembayaranScreen(onBackPressed: _goBack),
    HistoryScreen(onBackPressed: _goBack),
    PengumumanScreen(onBackPressed: _goBack),
  ];

  // Nav items config (index 2 is the center floating button)
  static const _navItems = [
    _NavItem(icon: Icons.home_outlined, activeIcon: Icons.home_rounded, label: 'Beranda'),
    _NavItem(icon: Icons.show_chart_rounded, activeIcon: Icons.show_chart_rounded, label: 'Perkembangan'),
    _NavItem(icon: Icons.account_balance_wallet_outlined, activeIcon: Icons.account_balance_wallet_rounded, label: 'SPP'),
    _NavItem(icon: Icons.receipt_long_outlined, activeIcon: Icons.receipt_long_rounded, label: 'Riwayat'),
    _NavItem(icon: Icons.campaign_outlined, activeIcon: Icons.campaign_rounded, label: 'Pengumuman'),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: IndexedStack(index: _currentIndex, children: _screens),
      extendBody: true,
      bottomNavigationBar: _buildBottomNav(),
    );
  }

  Widget _buildBottomNav() {
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
            if (index == 2) return _buildCenterButton();
            return _buildNavItem(index);
          }),
        ),
      ),
    );
  }

  // ── Regular nav item ──
  Widget _buildNavItem(int index) {
    final isActive = _currentIndex == index;
    final item = _navItems[index];

    return GestureDetector(
      onTap: () => setState(() => _currentIndex = index),
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
                style: TextStyle(
                  fontSize: 9,
                  fontWeight: isActive ? FontWeight.w700 : FontWeight.w500,
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

  // ── Floating center button (SPP) ──
  Widget _buildCenterButton() {
    final isActive = _currentIndex == 2;
    return GestureDetector(
      onTap: () => setState(() => _currentIndex = 2),
      child: Container(
        margin: const EdgeInsets.only(bottom: 16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            Container(
              width: 52,
              height: 52,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: isActive
                      ? [const Color(0xFFFF6B1A), const Color(0xFFFF8C42)]
                      : [AppTheme.primary.withOpacity(0.8), AppTheme.primaryLight],
                ),
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(color: AppTheme.primary.withOpacity(isActive ? 0.45 : 0.3), blurRadius: isActive ? 16 : 12, offset: const Offset(0, 4)),
                  BoxShadow(color: AppTheme.primary.withOpacity(0.15), blurRadius: 24, offset: const Offset(0, 8)),
                ],
                border: Border.all(color: Colors.white, width: 3),
              ),
              child: Icon(
                isActive ? Icons.account_balance_wallet_rounded : Icons.account_balance_wallet_outlined,
                color: Colors.white,
                size: 26,
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
