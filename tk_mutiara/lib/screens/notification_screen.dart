import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../models/pembayaran_model.dart';
import '../services/api_services.dart';

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
      // Filter hanya pembayaran yang sudah lunas (berhasil)
      if (mounted) {
        setState(() {
          _notifications = payments.where((p) => p.isLunas).toList();
          // Sort dari yang terbaru ke terlama berdasarkan paymentDate (jika ada) atau biarkan default
          _notifications.sort((a, b) {
             if (a.paymentDate.isNotEmpty && b.paymentDate.isNotEmpty) {
               return b.paymentDate.compareTo(a.paymentDate);
             }
             return 0;
          });
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = '$e';
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          onPressed: () => Navigator.pop(context),
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: AppTheme.primary, size: 20),
        ),
        title: Text(
          'Notifikasi',
          style: GoogleFonts.montserrat(
            color: AppTheme.textDark,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
          : _error != null
              ? Center(
                  child: Text(
                    'Gagal memuat notifikasi\n$_error',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: AppTheme.danger),
                  ),
                )
              : _notifications.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.notifications_off_rounded, size: 80, color: AppTheme.textLight.withOpacity(0.3)),
                          const SizedBox(height: 16),
                          Text(
                            'Belum ada notifikasi',
                            style: GoogleFonts.montserrat(
                              color: AppTheme.textMedium,
                              fontSize: 16,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    )
                  : RefreshIndicator(
                      color: AppTheme.primary,
                      onRefresh: _loadNotifications,
                      child: ListView.builder(
                        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                        itemCount: _notifications.length,
                        itemBuilder: (context, index) {
                          final notif = _notifications[index];
                          return _buildNotificationCard(notif);
                        },
                      ),
                    ),
    );
  }

  Widget _buildNotificationCard(PembayaranModel p) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: AppTheme.success.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.check_circle_rounded, color: AppTheme.success, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Pembayaran Berhasil',
                  style: GoogleFonts.montserrat(
                    color: AppTheme.textDark,
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Pembayaran SPP untuk bulan ${p.bulan} ${p.tahun} sebesar ${p.nominalFormatted} telah berhasil dikonfirmasi.',
                  style: GoogleFonts.montserrat(
                    color: AppTheme.textMedium,
                    fontSize: 12,
                    height: 1.4,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  _formatTime(p.paymentDate),
                  style: GoogleFonts.montserrat(
                    color: AppTheme.textLight,
                    fontSize: 11,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  String _formatTime(String dateTimeString) {
    if (dateTimeString.isEmpty) return 'Baru saja';
    try {
      final DateTime dt = DateTime.parse(dateTimeString);
      final Map<int, String> bulanNama = {
        1: 'Jan', 2: 'Feb', 3: 'Mar', 4: 'Apr', 5: 'Mei', 6: 'Jun',
        7: 'Jul', 8: 'Agu', 9: 'Sep', 10: 'Okt', 11: 'Nov', 12: 'Des',
      };
      
      final tanggal = dt.day.toString().padLeft(2, '0');
      final bulan = bulanNama[dt.month] ?? '';
      final tahun = dt.year;
      final jam = dt.hour.toString().padLeft(2, '0');
      final menit = dt.minute.toString().padLeft(2, '0');
      
      return '$tanggal $bulan $tahun, $jam:$menit';
    } catch (e) {
      return dateTimeString;
    }
  }
}
