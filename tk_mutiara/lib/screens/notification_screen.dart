import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/pembayaran_model.dart';
import '../services/api_services.dart';
import '../theme/app_theme.dart';

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  bool _isLoading = true;
  String? _error;
  List<PembayaranModel> _notifications = [];

  @override
  void initState() {
    super.initState();
    _loadNotifications();
    ApiService.paymentRefreshNotifier.addListener(_onPaymentUpdated);
  }

  @override
  void dispose() {
    ApiService.paymentRefreshNotifier.removeListener(_onPaymentUpdated);
    super.dispose();
  }

  void _onPaymentUpdated() {
    if (mounted) {
      _loadNotifications();
    }
  }

  Future<void> _loadNotifications() async {
    try {
      final payments = await ApiService.getPembayaran();
      if (!mounted) return;

      setState(() {
        _notifications = payments.where((p) => p.isLunas).toList()
          ..sort((a, b) => b.tanggalBayar.compareTo(a.tanggalBayar));
        _isLoading = false;
        _error = null;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.white,
      appBar: AppBar(
        backgroundColor: AppTheme.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          onPressed: () => Navigator.pop(context),
          icon: const Icon(
            Icons.arrow_back_ios_new_rounded,
            color: AppTheme.primary,
            size: 20,
          ),
        ),
        title: Text(
          'Notifikasi Pembayaran',
          style: GoogleFonts.montserrat(
            color: AppTheme.textDark,
            fontSize: 18,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(
        child: CircularProgressIndicator(color: AppTheme.primary),
      );
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Text(
            'Gagal memuat notifikasi\n$_error',
            textAlign: TextAlign.center,
            style: GoogleFonts.montserrat(
              color: AppTheme.danger,
              fontSize: 13,
              height: 1.5,
            ),
          ),
        ),
      );
    }

    if (_notifications.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 86,
              height: 86,
              decoration: BoxDecoration(
                color: const Color(0xFFF9FAFB),
                borderRadius: BorderRadius.circular(24),
              ),
              child: Icon(
                Icons.notifications_off_rounded,
                size: 44,
                color: AppTheme.textLight.withOpacity(0.5),
              ),
            ),
            const SizedBox(height: 16),
            Text(
              'Belum ada notifikasi',
              style: GoogleFonts.montserrat(
                color: AppTheme.textDark,
                fontSize: 16,
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              'Konfirmasi pembayaran akan muncul di sini.',
              style: GoogleFonts.montserrat(
                color: AppTheme.textMedium,
                fontSize: 12,
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      color: AppTheme.primary,
      onRefresh: _loadNotifications,
      child: ListView.builder(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        itemCount: _notifications.length + 1,
        itemBuilder: (context, index) {
          if (index == 0) {
            return _buildInfoHeader();
          }

          return _buildNotificationCard(_notifications[index - 1]);
        },
      ),
    );
  }

  Widget _buildInfoHeader() {
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFFF9FAFB),
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE5E7EB)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              color: AppTheme.primary.withOpacity(0.08),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(
              Icons.notifications_active_rounded,
              color: AppTheme.primary,
              size: 20,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '${_notifications.length} pembayaran lunas',
                  style: GoogleFonts.montserrat(
                    color: AppTheme.textDark,
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Daftar ini berisi konfirmasi pembayaran SPP yang sudah berhasil diproses.',
                  style: GoogleFonts.montserrat(
                    color: AppTheme.textMedium,
                    fontSize: 12,
                    height: 1.5,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNotificationCard(PembayaranModel p) {
    final paidAt = _formatTime(p.tanggalBayar);

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE5E7EB)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: AppTheme.success.withOpacity(0.1),
              borderRadius: BorderRadius.circular(14),
            ),
            child: const Icon(
              Icons.notifications_active_rounded,
              color: AppTheme.success,
              size: 22,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Text(
                        '${p.periodeLabel} lunas',
                        style: GoogleFonts.montserrat(
                          color: AppTheme.textDark,
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          height: 1.25,
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    _statusBadge(),
                  ],
                ),
                const SizedBox(height: 6),
                Text(
                  'Pembayaran ${p.nominalFormatted} berhasil dikonfirmasi.',
                  style: GoogleFonts.montserrat(
                    color: AppTheme.textMedium,
                    fontSize: 12,
                    height: 1.45,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(
                      Icons.access_time_rounded,
                      size: 13,
                      color: AppTheme.textLight,
                    ),
                    const SizedBox(width: 5),
                    Expanded(
                      child: Text(
                        paidAt,
                        style: GoogleFonts.montserrat(
                          color: AppTheme.textLight,
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _statusBadge() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 5),
      decoration: BoxDecoration(
        color: const Color(0xFFE8F8EE),
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        'Lunas',
        style: GoogleFonts.montserrat(
          color: AppTheme.success,
          fontSize: 11,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }

  String _formatTime(String dateTimeString) {
    if (dateTimeString.isEmpty) return 'Tanggal belum tersedia';

    try {
      final DateTime dt = DateTime.parse(dateTimeString);
      final Map<int, String> bulanNama = {
        1: 'Jan',
        2: 'Feb',
        3: 'Mar',
        4: 'Apr',
        5: 'Mei',
        6: 'Jun',
        7: 'Jul',
        8: 'Agu',
        9: 'Sep',
        10: 'Okt',
        11: 'Nov',
        12: 'Des',
      };

      final tanggal = dt.day.toString().padLeft(2, '0');
      final bulan = bulanNama[dt.month] ?? '';
      final tahun = dt.year;
      final jam = dt.hour.toString().padLeft(2, '0');
      final menit = dt.minute.toString().padLeft(2, '0');

      return '$tanggal $bulan $tahun, $jam:$menit';
    } catch (_) {
      return dateTimeString;
    }
  }
}
