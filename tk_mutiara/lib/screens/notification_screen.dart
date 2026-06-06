import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../services/api_services.dart';
import '../theme/app_theme.dart';
import 'pengumuman_screen.dart';

class NotificationScreen extends StatefulWidget {
  final String initialType;

  const NotificationScreen({super.key, this.initialType = 'pengumuman'});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  static const String _typePengumuman = 'pengumuman';
  static const String _typePembayaran = 'pembayaran';

  bool _isLoading = true;
  String? _error;
  late String _selectedType;
  List<PengumumanModel> _pengumumanNotifications = [];
  List<PembayaranModel> _paymentNotifications = [];

  @override
  void initState() {
    super.initState();
    _selectedType = widget.initialType == _typePembayaran
        ? _typePembayaran
        : _typePengumuman;
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
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final pengumumanFuture = ApiService.getPengumuman();
      final pembayaranFuture = ApiService.getPembayaran();
      final pengumuman = await pengumumanFuture;
      final payments = await pembayaranFuture;

      if (!mounted) return;

      setState(() {
        _pengumumanNotifications = pengumuman
          ..sort(
            (a, b) =>
                _dateValue(b.waktuUnggah).compareTo(_dateValue(a.waktuUnggah)),
          );
        _paymentNotifications = payments.where((p) => p.isLunas).toList()
          ..sort(
            (a, b) => _dateValue(
              b.tanggalBayar,
            ).compareTo(_dateValue(a.tanggalBayar)),
          );
        _isLoading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _isLoading = false;
      });
    }
  }

  int _dateValue(String value) {
    if (value.isEmpty) return 0;
    return DateTime.tryParse(value)?.millisecondsSinceEpoch ?? 0;
  }

  bool get _isPengumumanSelected => _selectedType == _typePengumuman;

  int get _selectedCount => _isPengumumanSelected
      ? _pengumumanNotifications.length
      : _paymentNotifications.length;

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
          'Notifikasi',
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

    return RefreshIndicator(
      color: AppTheme.primary,
      onRefresh: _loadNotifications,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        children: [
          _buildTypeSelector(),
          const SizedBox(height: 14),
          if (_selectedCount == 0)
            _buildEmptyState()
          else if (_isPengumumanSelected)
            ..._pengumumanNotifications.map(_buildPengumumanCard)
          else
            ..._paymentNotifications.map(_buildPaymentCard),
        ],
      ),
    );
  }

  Widget _buildTypeSelector() {
    return Container(
      padding: const EdgeInsets.all(5),
      decoration: BoxDecoration(
        color: const Color(0xFFF6F7F9),
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE5E7EB)),
      ),
      child: Row(
        children: [
          Expanded(
            child: _buildTypeButton(
              label: 'Pengumuman',
              count: _pengumumanNotifications.length,
              type: _typePengumuman,
              icon: Icons.campaign_rounded,
            ),
          ),
          const SizedBox(width: 6),
          Expanded(
            child: _buildTypeButton(
              label: 'Pembayaran',
              count: _paymentNotifications.length,
              type: _typePembayaran,
              icon: Icons.receipt_long_rounded,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTypeButton({
    required String label,
    required int count,
    required String type,
    required IconData icon,
  }) {
    final isActive = _selectedType == type;

    return GestureDetector(
      onTap: () => setState(() => _selectedType = type),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 11),
        decoration: BoxDecoration(
          color: isActive ? AppTheme.primary : Colors.transparent,
          borderRadius: BorderRadius.circular(14),
          boxShadow: isActive
              ? [
                  BoxShadow(
                    color: AppTheme.primary.withValues(alpha: 0.18),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ]
              : null,
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              icon,
              size: 17,
              color: isActive ? AppTheme.white : AppTheme.textMedium,
            ),
            const SizedBox(width: 6),
            Flexible(
              child: Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.montserrat(
                  color: isActive ? AppTheme.white : AppTheme.textMedium,
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
            const SizedBox(width: 6),
            Text(
              count.toString(),
              style: GoogleFonts.montserrat(
                color: isActive ? AppTheme.white : AppTheme.primary,
                fontSize: 11,
                fontWeight: FontWeight.w800,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    final title = _isPengumumanSelected
        ? 'Belum ada pengumuman'
        : 'Belum ada notifikasi pembayaran';
    final subtitle = _isPengumumanSelected
        ? 'Pengumuman dari sekolah akan muncul di sini.'
        : 'Konfirmasi pembayaran akan muncul di sini.';

    return Padding(
      padding: const EdgeInsets.only(top: 56),
      child: Column(
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
              color: AppTheme.textLight.withValues(alpha: 0.5),
            ),
          ),
          const SizedBox(height: 16),
          Text(
            title,
            style: GoogleFonts.montserrat(
              color: AppTheme.textDark,
              fontSize: 16,
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            subtitle,
            textAlign: TextAlign.center,
            style: GoogleFonts.montserrat(
              color: AppTheme.textMedium,
              fontSize: 12,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPengumumanCard(PengumumanModel p) {
    final date = _formatTime(p.waktuUnggah);
    final description = _cleanText(p.deskripsi);

    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => PengumumanScreen(idPengumuman: p.idPengumuman),
          ),
        );
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        padding: const EdgeInsets.all(14),
        decoration: _cardDecoration(),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 42,
              height: 42,
              decoration: BoxDecoration(
                color: AppTheme.primary.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(14),
              ),
              child: const Icon(
                Icons.campaign_rounded,
                color: AppTheme.primary,
                size: 22,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    p.judul,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: GoogleFonts.montserrat(
                      color: AppTheme.textDark,
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      height: 1.25,
                    ),
                  ),
                  if (description.isNotEmpty) ...[
                    const SizedBox(height: 6),
                    Text(
                      description,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: GoogleFonts.montserrat(
                        color: AppTheme.textMedium,
                        fontSize: 12,
                        height: 1.45,
                      ),
                    ),
                  ],
                  const SizedBox(height: 8),
                  _buildTimeRow('$date - ${p.namaGuru}'),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPaymentCard(PembayaranModel p) {
    final paidAt = _formatTime(p.tanggalBayar);

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(14),
      decoration: _cardDecoration(),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: AppTheme.success.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(14),
            ),
            child: const Icon(
              Icons.check_circle_rounded,
              color: AppTheme.success,
              size: 23,
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
                _buildTimeRow(paidAt),
              ],
            ),
          ),
        ],
      ),
    );
  }

  BoxDecoration _cardDecoration() {
    return BoxDecoration(
      color: AppTheme.white,
      borderRadius: BorderRadius.circular(16),
      border: Border.all(color: const Color(0xFFE5E7EB)),
      boxShadow: [
        BoxShadow(
          color: Colors.black.withValues(alpha: 0.04),
          blurRadius: 12,
          offset: const Offset(0, 4),
        ),
      ],
    );
  }

  Widget _buildTimeRow(String text) {
    return Row(
      children: [
        const Icon(
          Icons.access_time_rounded,
          size: 13,
          color: AppTheme.textLight,
        ),
        const SizedBox(width: 5),
        Expanded(
          child: Text(
            text,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: GoogleFonts.montserrat(
              color: AppTheme.textLight,
              fontSize: 11,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ],
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

  String _cleanText(String value) {
    return value
        .replaceAll(RegExp(r'<[^>]*>'), ' ')
        .replaceAll(RegExp(r'\s+'), ' ')
        .trim();
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
