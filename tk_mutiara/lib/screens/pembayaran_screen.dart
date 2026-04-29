import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../theme/app_theme.dart';
import '../models/pembayaran_model.dart';
import '../services/api_services.dart';
import 'payment_webview_screen.dart';

class PembayaranScreen extends StatefulWidget {
  final PembayaranModel? tagihan;
  final VoidCallback? onBackPressed;

  const PembayaranScreen({super.key, this.tagihan, this.onBackPressed});

  @override
  State<PembayaranScreen> createState() => _PembayaranScreenState();
}

class _PembayaranScreenState extends State<PembayaranScreen>
    with SingleTickerProviderStateMixin {
  int _selectedMethod = 0;
  bool _isLoading = true;
  bool _isProcessing = false;
  bool _isDone = false;
  String? _error;
  String _lastOrderId = '-';
  String _lastTagihanId = '-';
  String _lastRedirectUrl = '';
  String _paymentStateLabel = 'Belum Dibayar';
  List<PembayaranModel> _allTagihan = [];

  late AnimationController _doneController;
  late Animation<double> _doneScale;

  final List<Map<String, dynamic>> _methods = [
    {
      'label': 'Transfer Bank',
      'sub': 'BCA / Mandiri / BRI / BNI',
      'icon': Icons.account_balance_rounded,
      'color': const Color(0xFF3B82F6),
    },
    {
      'label': 'QRIS',
      'sub': 'Scan QR dari aplikasi manapun',
      'icon': Icons.qr_code_scanner_rounded,
      'color': const Color(0xFF8B5CF6),
    },
    {
      'label': 'GoPay / OVO',
      'sub': 'E-Wallet digital',
      'icon': Icons.account_balance_wallet_rounded,
      'color': const Color(0xFF22C55E),
    },
  ];

  PembayaranModel? get _activeTagihan {
    if (widget.tagihan != null) return widget.tagihan;
    for (final t in _allTagihan) {
      if (t.isBelum) return t;
    }
    return _allTagihan.isNotEmpty ? _allTagihan.first : null;
  }

  @override
  void initState() {
    super.initState();
    _doneController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _doneScale = CurvedAnimation(
      parent: _doneController,
      curve: Curves.elasticOut,
    );
    _loadTagihan();
  }

  @override
  void dispose() {
    _doneController.dispose();
    super.dispose();
  }

  Future<void> _loadTagihan() async {
    try {
      final data = await ApiService.getPembayaran();
      if (!mounted) return;
      setState(() {
        _allTagihan = data;
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

  void _processBayar() async {
    final tagihan = _activeTagihan;
    if (tagihan == null) return;

    setState(() => _isProcessing = true);
    HapticFeedback.mediumImpact();

    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Membuat transaksi pembayaran...')),
      );
    }

    final method = (_methods[_selectedMethod]['label'] as String).toLowerCase();
    final result = await ApiService.bayarSPP(tagihan.id, method);

    if (!mounted) return;

    if (result['success'] == true) {
      _lastOrderId = (result['order_id'] ?? '-').toString();
      _lastTagihanId = (result['id_tagihan'] ?? tagihan.id).toString();
      _lastRedirectUrl = (result['redirect_url'] ?? '').toString();
      final paymentStatus = (result['status_tagihan'] ?? '')
          .toString()
          .toLowerCase();

      if (paymentStatus == 'lunas') {
        setState(() {
          _isDone = true;
          _paymentStateLabel = 'Lunas';
          _isProcessing = false;
        });
        _doneController.forward();
        HapticFeedback.heavyImpact();
        ApiService.notifyPaymentUpdated();
        await _loadTagihan();
        return;
      }

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Transaksi dibuat. Lanjutkan pembayaran di Midtrans.'),
        ),
      );

      setState(() {
        _paymentStateLabel = 'Menunggu Pembayaran';
      });

      if (_lastRedirectUrl.isNotEmpty) {
        await Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => PaymentWebViewScreen(initialUrl: _lastRedirectUrl),
          ),
        );
      }

      await _pollStatusInBackground();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['message'] ?? 'Gagal membuat transaksi pembayaran',
          ),
        ),
      );
    }

    setState(() {
      _isProcessing = false;
    });
  }

  Future<void> _pollStatusInBackground() async {
    // Poll for a short period to capture immediate settlement in sandbox.
    for (int i = 0; i < 20; i++) {
      await Future.delayed(const Duration(seconds: 3));
      if (!mounted) return;
      if (_lastTagihanId == '-' || _lastTagihanId.isEmpty) return;

      final result = await ApiService.cekStatusPembayaran(_lastTagihanId);
      if (result['success'] == true) {
        final data = result['data'] as Map<String, dynamic>? ?? {};
        final paymentStatus = (data['status_tagihan'] ?? '')
            .toString()
            .toLowerCase();
        if (paymentStatus == 'lunas') {
          setState(() {
            _isDone = true;
            _paymentStateLabel = 'Lunas';
          });
          _doneController.forward();
          HapticFeedback.heavyImpact();
          ApiService.notifyPaymentUpdated();
          _loadTagihan();
          return;
        }
      }
    }
  }

  Future<void> _cekStatusPembayaran() async {
    if (_lastTagihanId == '-' || _lastTagihanId.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Belum ada transaksi yang dibuat.')),
      );
      return;
    }

    setState(() => _isProcessing = true);
    final result = await ApiService.cekStatusPembayaran(_lastTagihanId);
    if (!mounted) return;

    if (result['success'] == true) {
      final data = result['data'] as Map<String, dynamic>? ?? {};
      final paymentStatus = (data['status_tagihan'] ?? '')
          .toString()
          .toLowerCase();

      if (paymentStatus == 'lunas') {
        setState(() {
          _isDone = true;
          _paymentStateLabel = 'Lunas';
          _isProcessing = false;
        });
        _doneController.forward();
        HapticFeedback.heavyImpact();
        ApiService.notifyPaymentUpdated();
        _loadTagihan();
      } else {
        setState(() {
          _paymentStateLabel = 'Menunggu Pembayaran';
          _isProcessing = false;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text(
              'Status masih belum bayar. Selesaikan pembayaran di Midtrans dulu.',
            ),
          ),
        );
      }
    } else {
      setState(() => _isProcessing = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? 'Gagal cek status pembayaran'),
        ),
      );
    }
  }

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
        child: _isDone
            ? _buildSuccessView(context)
            : _buildPaymentView(context),
      ),
    );
  }

  Widget _buildPaymentView(BuildContext context) {
    final tagihan = _activeTagihan;

    if (tagihan == null) {
      return Column(
        children: [
          _buildHeader(context),
          Expanded(
            child: Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Text(
                  _error ?? 'Tidak ada tagihan untuk akun ini.',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: AppTheme.textMedium,
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ),
          ),
        ],
      );
    }

    return Column(
      children: [
        _buildHeader(context),
        Expanded(
          child: SingleChildScrollView(
            physics: const BouncingScrollPhysics(),
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                _buildTagihanCard(tagihan),
                const SizedBox(height: 24),
                if (tagihan.isLunas)
                  _buildLunasNotice()
                else
                  _buildMethodSection(),
                const SizedBox(height: 24),
                _buildInfoBox(),
                const SizedBox(height: 100),
              ],
            ),
          ),
        ),
      ],
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
            icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 18),
            color: AppTheme.primary,
          ),
          const SizedBox(width: 4),
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Pembayaran SPP',
                style: TextStyle(
                  color: AppTheme.textDark,
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                ),
              ),
              Text(
                'TK Mutiara',
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

  Widget _buildTagihanCard(PembayaranModel tagihan) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: AppTheme.primaryGradient,
        borderRadius: BorderRadius.circular(24),
        boxShadow: AppTheme.softShadow,
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Detail Tagihan',
                style: TextStyle(
                  color: Colors.white70,
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 4,
                ),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  tagihan.isLunas ? 'Lunas' : 'Belum Lunas',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          _tagihanRow(
            'Nama Siswa',
            tagihan.namaSiswa.isEmpty ? '-' : tagihan.namaSiswa,
          ),
          _tagihanRow('Kelas', tagihan.kelas.isEmpty ? '-' : tagihan.kelas),
          _tagihanRow('Periode', tagihan.periode),
          const Divider(color: Colors.white24, height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Total Pembayaran',
                style: TextStyle(
                  color: Colors.white70,
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                ),
              ),
              Text(
                tagihan.nominalFormatted,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 20,
                  fontWeight: FontWeight.w900,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _tagihanRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(color: Colors.white60, fontSize: 12),
          ),
          Text(
            value,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 13,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMethodSection() {
    final tagihan = _activeTagihan;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Pilih Metode Pembayaran',
          style: TextStyle(
            color: AppTheme.textDark,
            fontSize: 16,
            fontWeight: FontWeight.w800,
          ),
        ),
        const SizedBox(height: 14),
        ...List.generate(
          _methods.length,
          (i) => GestureDetector(
            onTap: () {
              setState(() => _selectedMethod = i);
              HapticFeedback.selectionClick();
            },
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              margin: const EdgeInsets.only(bottom: 12),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppTheme.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(
                  color: _selectedMethod == i
                      ? AppTheme.primary
                      : Colors.transparent,
                  width: 2,
                ),
                boxShadow: _selectedMethod == i
                    ? AppTheme.softShadow
                    : AppTheme.cardShadowList,
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: (_methods[i]['color'] as Color).withOpacity(0.12),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Icon(
                      _methods[i]['icon'] as IconData,
                      color: _methods[i]['color'] as Color,
                      size: 22,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          _methods[i]['label'] as String,
                          style: const TextStyle(
                            color: AppTheme.textDark,
                            fontSize: 14,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        Text(
                          _methods[i]['sub'] as String,
                          style: const TextStyle(
                            color: AppTheme.textMedium,
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),
                  AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    width: 22,
                    height: 22,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: _selectedMethod == i
                            ? AppTheme.primary
                            : AppTheme.textLight,
                        width: 2,
                      ),
                      color: _selectedMethod == i
                          ? AppTheme.primary
                          : Colors.transparent,
                    ),
                    child: _selectedMethod == i
                        ? const Icon(
                            Icons.check_rounded,
                            color: Colors.white,
                            size: 14,
                          )
                        : null,
                  ),
                ],
              ),
            ),
          ),
        ),
        const SizedBox(height: 8),
        SizedBox(
          width: double.infinity,
          height: 56,
          child: ElevatedButton(
            onPressed: (_isProcessing || tagihan == null || tagihan.isLunas)
                ? null
                : _processBayar,
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primary,
              disabledBackgroundColor: AppTheme.primary.withOpacity(0.6),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
            ),
            child: _isProcessing
                ? const SizedBox(
                    width: 24,
                    height: 24,
                    child: CircularProgressIndicator(
                      color: Colors.white,
                      strokeWidth: 2.5,
                    ),
                  )
                : Text(
                    tagihan?.isLunas == true
                        ? 'Pembayaran Sudah Lunas'
                        : 'Bayar ${tagihan?.nominalFormatted ?? 'Rp 0'}',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
          ),
        ),
        if (_lastTagihanId != '-') ...[
          const SizedBox(height: 10),
          SizedBox(
            width: double.infinity,
            height: 52,
            child: OutlinedButton(
              onPressed: _isProcessing ? null : _cekStatusPembayaran,
              style: OutlinedButton.styleFrom(
                side: const BorderSide(color: AppTheme.primary, width: 1.5),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(14),
                ),
              ),
              child: Text(
                _isProcessing ? 'Mengecek...' : 'Cek Status Pembayaran',
                style: const TextStyle(
                  color: AppTheme.primary,
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildLunasNotice() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: const Color(0xFFE8F8EE),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFF16A34A).withOpacity(0.35)),
      ),
      child: const Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(Icons.check_circle_rounded, color: Color(0xFF16A34A), size: 24),
          SizedBox(width: 10),
          Expanded(
            child: Text(
              'Pembayaran sudah lunas. Tidak ada tagihan aktif untuk dibayar saat ini.',
              style: TextStyle(
                color: Color(0xFF166534),
                fontSize: 13,
                fontWeight: FontWeight.w700,
                height: 1.5,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoBox() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFFFFF8E7),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFF59E0B).withOpacity(0.3)),
      ),
      child: Row(
        children: [
          const Icon(
            Icons.info_outline_rounded,
            color: Color(0xFFF59E0B),
            size: 20,
          ),
          const SizedBox(width: 10),
          const Expanded(
            child: Text(
              'Status pembayaran akan otomatis berubah menjadi lunas setelah webhook Midtrans diterima server.',
              style: TextStyle(
                color: AppTheme.textDark,
                fontSize: 12,
                fontWeight: FontWeight.w500,
                height: 1.5,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSuccessView(BuildContext context) {
    final tagihan = _activeTagihan;

    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            ScaleTransition(
              scale: _doneScale,
              child: Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  gradient: AppTheme.primaryGradient,
                  shape: BoxShape.circle,
                  boxShadow: AppTheme.softShadow,
                ),
                child: const Icon(
                  Icons.check_circle_outline_rounded,
                  color: Colors.white,
                  size: 60,
                ),
              ),
            ),
            const SizedBox(height: 32),
            const Text(
              'Pembayaran Berhasil!',
              style: TextStyle(
                color: AppTheme.textDark,
                fontSize: 24,
                fontWeight: FontWeight.w900,
              ),
            ),
            const SizedBox(height: 12),
            Text(
              _paymentStateLabel.toLowerCase() == 'lunas'
                  ? 'Pembayaran tagihan ${tagihan?.periode ?? '-'} sudah lunas.'
                  : 'Transaksi tagihan ${tagihan?.periode ?? '-'} berhasil dibuat. Sistem sedang memproses pembayaran.',
              style: const TextStyle(
                color: AppTheme.textMedium,
                fontSize: 14,
                fontWeight: FontWeight.w500,
                height: 1.6,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 40),
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: AppTheme.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: AppTheme.cardShadowList,
              ),
              child: Column(
                children: [
                  _successRow('Tagihan ID', _lastTagihanId),
                  _successRow('Order ID', _lastOrderId),
                  _successRow(
                    'Metode',
                    _methods[_selectedMethod]['label'] as String,
                  ),
                  _successRow('Jumlah', tagihan?.nominalFormatted ?? 'Rp 0'),
                  _successRow('Status', _paymentStateLabel),
                ],
              ),
            ),
            const SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton(
                onPressed: () {
                  if (widget.onBackPressed != null) {
                    widget.onBackPressed!();
                    return;
                  }
                  Navigator.pop(context);
                },
                child: const Text('Kembali ke Beranda'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _successRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(
              color: AppTheme.textMedium,
              fontSize: 13,
              fontWeight: FontWeight.w500,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              color: label == 'Status' ? AppTheme.success : AppTheme.textDark,
              fontSize: 13,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }
}
