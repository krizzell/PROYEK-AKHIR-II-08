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
  bool _isLoading = true;
  bool _isProcessing = false;
  bool _isDone = false;
  String? _error;
  String _lastOrderId = '-';
  String _lastTagihanId = '-';
  String _lastRedirectUrl = '';
  String _paymentStateLabel = 'Belum Dibayar';
  List<PembayaranModel> _allTagihan = [];
  PembayaranModel? _selectedTagihan;

  late AnimationController _doneController;
  late Animation<double> _doneScale;

  PembayaranModel? get _activeTagihan {
    if (widget.tagihan != null) return widget.tagihan;
    if (_selectedTagihan != null) {
      final selectedId = _selectedTagihan!.idTagihan;
      for (final t in _allTagihan) {
        if (t.idTagihan == selectedId) return t;
      }
    }
    final unpaid = _sortedTagihan(_allTagihan.where((t) => t.isBelum));
    if (unpaid.isNotEmpty) return unpaid.first;
    final all = _sortedTagihan(_allTagihan, newestFirst: true);
    return all.isNotEmpty ? all.first : null;
  }

  List<PembayaranModel> get _unpaidTagihan {
    return _sortedTagihan(_allTagihan.where((t) => t.isBelum));
  }

  List<PembayaranModel> _sortedTagihan(
    Iterable<PembayaranModel> source, {
    bool newestFirst = false,
  }) {
    final list = source.toList();
    list.sort((a, b) {
      final aKey = _periodeSortKey(a);
      final bKey = _periodeSortKey(b);
      if (aKey != bKey) {
        return newestFirst ? bKey.compareTo(aKey) : aKey.compareTo(bKey);
      }
      return newestFirst
          ? b.createdAt.compareTo(a.createdAt)
          : a.createdAt.compareTo(b.createdAt);
    });
    return list;
  }

  int _periodeSortKey(PembayaranModel tagihan) {
    final text = tagihan.periodeBersih.toLowerCase();
    final monthMap = {
      'januari': 1,
      'jan': 1,
      'februari': 2,
      'feb': 2,
      'maret': 3,
      'mar': 3,
      'april': 4,
      'apr': 4,
      'mei': 5,
      'may': 5,
      'juni': 6,
      'jun': 6,
      'juli': 7,
      'jul': 7,
      'agustus': 8,
      'agu': 8,
      'aug': 8,
      'september': 9,
      'sep': 9,
      'oktober': 10,
      'okt': 10,
      'oct': 10,
      'november': 11,
      'nov': 11,
      'desember': 12,
      'des': 12,
      'dec': 12,
    };

    var month = 0;
    for (final entry in monthMap.entries) {
      if (text.contains(entry.key)) {
        month = entry.value;
        break;
      }
    }

    final yearMatch = RegExp(r'(20\d{2})').firstMatch(text);
    final year = int.tryParse(yearMatch?.group(1) ?? '') ?? 0;

    if (year > 0 && month > 0) return year * 100 + month;

    final created = DateTime.tryParse(tagihan.createdAt);
    if (created != null) return created.year * 100 + created.month;

    return 0;
  }

  void _syncSelectedTagihan(List<PembayaranModel> data) {
    if (widget.tagihan != null) return;

    final unpaid = _sortedTagihan(data.where((t) => t.isBelum));
    if (unpaid.isEmpty) {
      _selectedTagihan = null;
      return;
    }

    final selectedId = _selectedTagihan?.idTagihan;
    final stillAvailable = unpaid.any((t) => t.idTagihan == selectedId);
    if (!stillAvailable) {
      _selectedTagihan = unpaid.first;
    }
  }

  String _monthBadge(PembayaranModel tagihan) {
    final bulan = tagihan.bulan.trim();
    if (bulan.isEmpty) return 'SPP';
    return bulan.length <= 3
        ? bulan.toUpperCase()
        : bulan.substring(0, 3).toUpperCase();
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
    ApiService.paymentRefreshNotifier.addListener(_onPaymentUpdated);
  }

  @override
  void dispose() {
    ApiService.paymentRefreshNotifier.removeListener(_onPaymentUpdated);
    _doneController.dispose();
    super.dispose();
  }

  void _onPaymentUpdated() {
    if (mounted) {
      _loadTagihan();
    }
  }

  Future<void> _loadTagihan() async {
    try {
      final data = await ApiService.getPembayaran();
      if (!mounted) return;
      setState(() {
        _allTagihan = data;
        _syncSelectedTagihan(data);
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

    final result = await ApiService.bayarSPP(tagihan.id, 'bank_transfer');

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

      setState(() {
        _paymentStateLabel = 'Menunggu Pembayaran';
      });

      if (_lastRedirectUrl.isNotEmpty) {
        ScaffoldMessenger.of(context).hideCurrentSnackBar();
        final webViewResult = await Navigator.push<String?>(
          context,
          MaterialPageRoute(
            builder: (_) => PaymentWebViewScreen(initialUrl: _lastRedirectUrl),
          ),
        );

        if (!mounted) return;

        final didFinishPaymentPage = webViewResult != null;
        if (!didFinishPaymentPage) {
          setState(() {
            _isProcessing = false;
            _paymentStateLabel = 'Belum Dibayar';
            _lastTagihanId = '-';
            _lastOrderId = '-';
            _lastRedirectUrl = '';
          });
          return;
        }
      }

      final isPaid = await _pollStatusInBackground();
      if (!mounted) return;
      if (!isPaid) {
        setState(() {
          _paymentStateLabel = 'Menunggu Pembayaran';
          _isProcessing = false;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text(
              'Pembayaran belum terkonfirmasi. Tekan cek status beberapa saat lagi.',
            ),
          ),
        );
        return;
      }
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

  Future<bool> _pollStatusInBackground() async {
    // Cek beberapa kali karena webhook/status Midtrans kadang butuh jeda.
    for (int i = 0; i < 8; i++) {
      await Future.delayed(const Duration(seconds: 2));
      if (!mounted) return false;
      if (_lastTagihanId == '-' || _lastTagihanId.isEmpty) return false;

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
          await _loadTagihan();
          return true;
        }
      }
    }

    return false;
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
              'Status masih belum bayar. Selesaikan pembayaran terlebih dulu.',
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
        backgroundColor: AppTheme.white,
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
      backgroundColor: AppTheme.white,
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
          child: RefreshIndicator(
            color: AppTheme.primary,
            edgeOffset: 20,
            onRefresh: _loadTagihan,
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(
                parent: ClampingScrollPhysics(),
              ),
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  if (widget.tagihan == null && _unpaidTagihan.isNotEmpty) ...[
                    _buildTagihanSelector(),
                    const SizedBox(height: 16),
                  ],
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

  Widget _buildTagihanSelector() {
    final unpaid = _unpaidTagihan;
    final selectedId = _activeTagihan?.idTagihan;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: const Color(0xFFE5E7EB)),
        boxShadow: AppTheme.cardShadowList,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: AppTheme.primary.withValues(alpha: 0.08),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(
                  Icons.calendar_month_rounded,
                  color: AppTheme.primary,
                  size: 19,
                ),
              ),
              const SizedBox(width: 10),
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Pilih Bulan Tagihan',
                      style: TextStyle(
                        color: AppTheme.textDark,
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    SizedBox(height: 2),
                    Text(
                      'Pilih SPP bulan mana yang ingin dibayar.',
                      style: TextStyle(
                        color: AppTheme.textMedium,
                        fontSize: 11,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          Column(
            children: unpaid.map((tagihan) {
              final isSelected = tagihan.idTagihan == selectedId;
              return Padding(
                padding: const EdgeInsets.only(bottom: 10),
                child: InkWell(
                  borderRadius: BorderRadius.circular(16),
                  onTap: _isProcessing
                      ? null
                      : () => setState(() {
                          _selectedTagihan = tagihan;
                          _lastTagihanId = '-';
                          _lastOrderId = '-';
                          _lastRedirectUrl = '';
                          _paymentStateLabel = 'Belum Dibayar';
                        }),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 180),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: isSelected
                          ? const Color(0xFFFFF4ED)
                          : const Color(0xFFF9FAFB),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: isSelected
                            ? AppTheme.primary
                            : const Color(0xFFE5E7EB),
                        width: isSelected ? 1.5 : 1,
                      ),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 46,
                          height: 46,
                          alignment: Alignment.center,
                          decoration: BoxDecoration(
                            color: AppTheme.white,
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(
                              color: isSelected
                                  ? AppTheme.primary.withValues(alpha: 0.35)
                                  : const Color(0xFFE5E7EB),
                            ),
                          ),
                          child: Text(
                            _monthBadge(tagihan),
                            style: TextStyle(
                              color: isSelected
                                  ? AppTheme.primary
                                  : AppTheme.textMedium,
                              fontSize: 12,
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                tagihan.periodeLabel,
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(
                                  color: AppTheme.textDark,
                                  fontSize: 13,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                tagihan.nominalFormatted,
                                style: const TextStyle(
                                  color: AppTheme.textMedium,
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 8),
                        Icon(
                          isSelected
                              ? Icons.radio_button_checked_rounded
                              : Icons.radio_button_unchecked_rounded,
                          color: isSelected
                              ? AppTheme.primary
                              : AppTheme.textLight,
                          size: 22,
                        ),
                      ],
                    ),
                  ),
                ),
              );
            }).toList(),
          ),
        ],
      ),
    );
  }

  Widget _buildTagihanCard(PembayaranModel tagihan) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFE5E7EB)),
        boxShadow: AppTheme.cardShadowList,
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Detail Tagihan',
                style: TextStyle(
                  color: AppTheme.textMedium,
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
                  color: tagihan.isLunas
                      ? const Color(0xFFE8F8EE)
                      : const Color(0xFFFFEDE0),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  tagihan.isLunas ? 'Lunas' : 'Belum Lunas',
                  style: TextStyle(
                    color: tagihan.isLunas
                        ? const Color(0xFF16A34A)
                        : AppTheme.primary,
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
          _tagihanRow('Periode', tagihan.periodeLabel),
          _tagihanRow('SPP Pokok', tagihan.jumlahTagihanFormatted),
          if (tagihan.dendaKeterlambatan > 0)
            _tagihanRow(
              'Denda Keterlambatan',
              tagihan.dendaFormatted,
              valueColor: AppTheme.danger,
            ),
          const Divider(color: Color(0xFFE5E7EB), height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Total Pembayaran',
                style: TextStyle(
                  color: AppTheme.textMedium,
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                ),
              ),
              Text(
                tagihan.nominalFormatted,
                style: const TextStyle(
                  color: AppTheme.primary,
                  fontSize: 20,
                  fontWeight: FontWeight.w900,
                ),
              ),
            ],
          ),
          if (tagihan.dendaKeterlambatan > 0) ...[
            const SizedBox(height: 12),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFFFF1F2),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: const Color(0xFFFECACA)),
              ),
              child: const Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(
                    Icons.info_outline_rounded,
                    color: AppTheme.danger,
                    size: 18,
                  ),
                  SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'Denda keterlambatan Rp 20.000 berlaku karena pembayaran melewati tanggal 10.',
                      style: TextStyle(
                        color: AppTheme.danger,
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        height: 1.4,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _tagihanRow(String label, String value, {Color? valueColor}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(color: AppTheme.textMedium, fontSize: 12),
          ),
          Text(
            value,
            style: TextStyle(
              color: valueColor ?? AppTheme.textDark,
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
        SizedBox(
          width: double.infinity,
          height: 56,
          child: ElevatedButton(
            onPressed: (_isProcessing || tagihan == null || tagihan.isLunas)
                ? null
                : _processBayar,
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primary,
              disabledBackgroundColor: AppTheme.primary.withValues(alpha: 0.6),
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
        if (_lastTagihanId != '-' &&
            _paymentStateLabel.toLowerCase() == 'menunggu pembayaran') ...[
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
        border: Border.all(
          color: const Color(0xFF16A34A).withValues(alpha: 0.35),
        ),
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
        border: Border.all(
          color: const Color(0xFFF59E0B).withValues(alpha: 0.3),
        ),
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
              'Status pembayaran akan otomatis berubah menjadi lunas setelah pembayaran berhasil dilakukan.',
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
                  color: AppTheme.white,
                  shape: BoxShape.circle,
                  border: Border.all(color: const Color(0xFFE5E7EB)),
                  boxShadow: AppTheme.cardShadowList,
                ),
                child: const Icon(
                  Icons.check_circle_outline_rounded,
                  color: AppTheme.success,
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
