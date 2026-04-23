import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/pembayaran_model.dart';
import '../services/api_services.dart';

class HistoryScreen extends StatefulWidget {
  final List<PembayaranModel>? payments;
  final VoidCallback? onBackPressed;
  
  const HistoryScreen({
    super.key,
    this.payments,
    this.onBackPressed,
  });

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  String _filter = 'semua';
  bool _isLoading = true;
  String? _error;
  List<PembayaranModel> _payments = [];

  @override
  void initState() {
    super.initState();
    _loadPayments();
  }

  Future<void> _loadPayments() async {
    try {
      if (widget.payments != null) {
        _payments = widget.payments!;
      } else {
        _payments = await ApiService.getPembayaran();
      }
    } catch (e) {
      _error = '$e';
    }

    if (!mounted) return;
    setState(() {
      _isLoading = false;
    });
  }

  List<PembayaranModel> get _filtered {
    if (_filter == 'semua') return _payments;
    return _payments.where((p) => p.status == _filter).toList();
  }

  int get _totalLunas => _payments
      .where((p) => p.isLunas)
      .fold(0, (sum, p) => sum + p.nominal);

  int get _jumlahLunas => _payments.where((p) => p.isLunas).length;

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: AppTheme.background,
        body: SafeArea(
          child: Column(
            children: [
              _buildHeader(context),
              const Expanded(
                child: Center(
                  child: CircularProgressIndicator(color: AppTheme.primary),
                ),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(context),
            if (_error != null)
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 8, 20, 0),
                child: Text(
                  'Gagal memuat histori: $_error',
                  style: const TextStyle(
                    color: AppTheme.danger,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            Expanded(
              child: SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.all(20),
                child: Column(
                  children: [
                    _buildSummaryRow(),
                    const SizedBox(height: 24),
                    _buildFilterRow(),
                    const SizedBox(height: 16),
                    ..._filtered.map((p) => _buildHistoryCard(p)),
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 20, 16),
      color: AppTheme.white,
      child: Row(
        children: [
          IconButton(
            onPressed: widget.onBackPressed ?? () => Navigator.pop(context),
            icon: const Icon(
              Icons.arrow_back_ios_new_rounded,
              size: 18,
            ),
            color: AppTheme.primary,
          ),
          const SizedBox(width: 4),
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Riwayat Pembayaran',
                style: TextStyle(
                  color: AppTheme.textDark,
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                ),
              ),
              Text(
                'SPP Tahun Ajaran 2024/2025',
                style: TextStyle(
                  color: AppTheme.textMedium,
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryRow() {
    final totalFormatted = _formatRupiah(_totalLunas);
    return Row(
      children: [
        Expanded(
          child: _summaryCard(
            'Total Dibayar',
            totalFormatted,
            Icons.payments_rounded,
            AppTheme.primary,
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _summaryCard(
            'Bulan Lunas',
            '$_jumlahLunas Bulan',
            Icons.check_circle_rounded,
            AppTheme.success,
          ),
        ),
      ],
    );
  }

  Widget _summaryCard(String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: AppTheme.cardShadowList,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: color.withOpacity(0.12),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color, size: 18),
          ),
          const SizedBox(height: 10),
          Text(
            value,
            style: TextStyle(
              color: color,
              fontSize: 17,
              fontWeight: FontWeight.w900,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: const TextStyle(
              color: AppTheme.textMedium,
              fontSize: 11,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterRow() {
    final filters = [
      {'key': 'semua', 'label': 'Semua'},
      {'key': 'lunas', 'label': 'Lunas'},
      {'key': 'belum', 'label': 'Belum Bayar'},
    ];
    return Row(
      children: [
        ...filters.map(
          (f) => Padding(
            padding: const EdgeInsets.only(right: 8),
            child: GestureDetector(
              onTap: () => setState(() => _filter = f['key']!),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding: const EdgeInsets.symmetric(
                  horizontal: 14,
                  vertical: 8,
                ),
                decoration: BoxDecoration(
                  color: _filter == f['key']
                      ? AppTheme.primary
                      : AppTheme.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: _filter == f['key']
                        ? AppTheme.primary
                        : AppTheme.primary.withOpacity(0.2),
                  ),
                  boxShadow: _filter == f['key'] ? AppTheme.softShadow : [],
                ),
                child: Text(
                  f['label']!,
                  style: TextStyle(
                    color: _filter == f['key']
                        ? Colors.white
                        : AppTheme.textDark,
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildHistoryCard(PembayaranModel p) {
    Color statusColor;
    IconData statusIcon;
    String statusLabel;
    Color bgColor;

    if (p.isLunas) {
      statusColor = AppTheme.success;
      statusIcon = Icons.check_circle_rounded;
      statusLabel = 'Lunas';
      bgColor = const Color(0xFFDCFCE7);
    } else if (p.isPending) {
      statusColor = AppTheme.warning;
      statusIcon = Icons.access_time_rounded;
      statusLabel = 'Pending';
      bgColor = const Color(0xFFFEF3C7);
    } else {
      statusColor = AppTheme.danger;
      statusIcon = Icons.cancel_rounded;
      statusLabel = 'Belum Bayar';
      bgColor = const Color(0xFFFEE2E2);
    }

    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0, end: 1),
      duration: const Duration(milliseconds: 400),
      builder: (_, v, child) => Opacity(opacity: v, child: child),
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        decoration: BoxDecoration(
          color: AppTheme.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: AppTheme.cardShadowList,
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            children: [
              Row(
                children: [
                  Container(
                    width: 46,
                    height: 46,
                    decoration: BoxDecoration(
                      gradient: AppTheme.primaryGradient,
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Center(
                      child: Text(
                        p.bulan.substring(0, 3).toUpperCase(),
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'SPP ${p.bulan} ${p.tahun}',
                          style: const TextStyle(
                            color: AppTheme.textDark,
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          p.isLunas
                              ? 'Dibayar: ${p.tanggalBayar}'
                              : 'Jatuh tempo: 10 ${p.bulan} ${p.tahun}',
                          style: const TextStyle(
                            color: AppTheme.textMedium,
                            fontSize: 12,
                            fontWeight: FontWeight.w400,
                          ),
                        ),
                      ],
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        p.nominalFormatted,
                        style: const TextStyle(
                          color: AppTheme.textDark,
                          fontSize: 14,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 3,
                        ),
                        decoration: BoxDecoration(
                          color: bgColor,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(statusIcon, size: 12, color: statusColor),
                            const SizedBox(width: 4),
                            Text(
                              statusLabel,
                              style: TextStyle(
                                color: statusColor,
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              if (p.isLunas && p.kodeTransaksi.isNotEmpty) ...[
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppTheme.background,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        Icons.receipt_rounded,
                        size: 14,
                        color: AppTheme.textLight,
                      ),
                      const SizedBox(width: 8),
                      Text(
                        'Kode: ${p.kodeTransaksi}',
                        style: const TextStyle(
                          color: AppTheme.textMedium,
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          letterSpacing: 0.3,
                        ),
                      ),
                      const Spacer(),
                      Text(
                        p.metodePembayaran,
                        style: TextStyle(
                          color: AppTheme.primary,
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  String _formatRupiah(int value) {
    final n = value.toString();
    final buffer = StringBuffer();
    int count = 0;
    for (int i = n.length - 1; i >= 0; i--) {
      if (count > 0 && count % 3 == 0) buffer.write('.');
      buffer.write(n[i]);
      count++;
    }
    return 'Rp ${buffer.toString().split('').reversed.join('')}';
  }
}
