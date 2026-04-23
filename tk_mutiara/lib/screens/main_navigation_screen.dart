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
  int _previousIndex = 0; // Track previous tab for back button

  // Fungsi untuk back ke tab sebelumnya atau Home
  void _goBack() {
    setState(() {
      if (_currentIndex != 0) {
        _previousIndex = _currentIndex;
        _currentIndex = 0; // Go back to Home
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 20,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (index) {
            setState(() {
              _previousIndex = _currentIndex;
              _currentIndex = index;
            });
          },
          type: BottomNavigationBarType.fixed,
          backgroundColor: Colors.white,
          elevation: 0,
          selectedItemColor: AppTheme.primary,
          unselectedItemColor: const Color(0xFFB0BEC5),
          selectedLabelStyle: const TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w600,
          ),
          unselectedLabelStyle: const TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w500,
          ),
          items: [
            // Home
            BottomNavigationBarItem(
              icon: _buildIcon(
                Icons.home_rounded,
                isActive: _currentIndex == 0,
              ),
              activeIcon: _buildIcon(
                Icons.home_rounded,
                isActive: true,
              ),
              label: 'Home',
            ),
            // Perkembangan
            BottomNavigationBarItem(
              icon: _buildIcon(
                Icons.trending_up_rounded,
                isActive: _currentIndex == 1,
              ),
              activeIcon: _buildIcon(
                Icons.trending_up_rounded,
                isActive: true,
              ),
              label: 'Perkembangan',
            ),
            // SPP / Pembayaran
            BottomNavigationBarItem(
              icon: _buildIcon(
                Icons.payment_rounded,
                isActive: _currentIndex == 2,
              ),
              activeIcon: _buildIcon(
                Icons.payment_rounded,
                isActive: true,
              ),
              label: 'SPP',
            ),
            // Histori
            BottomNavigationBarItem(
              icon: _buildIcon(
                Icons.receipt_long_rounded,
                isActive: _currentIndex == 3,
              ),
              activeIcon: _buildIcon(
                Icons.receipt_long_rounded,
                isActive: true,
              ),
              label: 'Histori',
            ),
            // Pengumuman
            BottomNavigationBarItem(
              icon: _buildIcon(
                Icons.notifications_rounded,
                isActive: _currentIndex == 4,
              ),
              activeIcon: _buildIcon(
                Icons.notifications_rounded,
                isActive: true,
              ),
              label: 'Pengumuman',
            ),
          ],
        ),
      ),
    );
  }

  // Helper widget untuk icon dengan styling
  Widget _buildIcon(IconData icon, {required bool isActive}) {
    return Container(
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: isActive ? AppTheme.primary.withOpacity(0.1) : Colors.transparent,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Icon(icon, size: 24),
    );
  }
}
