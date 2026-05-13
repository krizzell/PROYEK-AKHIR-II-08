import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/perkembangan_model.dart';
import '../services/api_services.dart';

class PerkembanganScreen extends StatefulWidget {
  final VoidCallback? onBackPressed;

  const PerkembanganScreen({super.key, this.onBackPressed});

  @override
  State<PerkembanganScreen> createState() => _PerkembanganScreenState();
}

class _PerkembanganScreenState extends State<PerkembanganScreen> {
  List<PerkembanganModel> _data = [];
  bool _isLoading = true;
  String? _errorMsg;
  Map<String, List<PerkembanganModel>> _groupedData = {};
  List<String> _monthKeys = [];
  String? _selectedMonthKey;

  @override
  void initState() {
    super.initState();
    _loadPerkembangan();
  }

  Future<void> _loadPerkembangan() async {
    try {
      final data = await ApiService.getPerkembangan();
      setState(() {
        _data = data;
        _groupDataByMonth();
        if (_monthKeys.isNotEmpty) _selectedMonthKey = _monthKeys.last;
        _isLoading = false;
      });
    } catch (e) {
      setState(() { _isLoading = false; _errorMsg = '$e'; });
    }
  }

  void _groupDataByMonth() {
    _groupedData.clear();
    _monthKeys.clear();
    for (var item in _data) {
      final key = '${item.tahun}-${item.bulan.toString().padLeft(2, '0')}';
      _groupedData.putIfAbsent(key, () => []).add(item);
    }
    _monthKeys = _groupedData.keys.toList()..sort();
  }

  List<PerkembanganModel> get _filteredData {
    if (_selectedMonthKey == null || !_groupedData.containsKey(_selectedMonthKey)) return [];
    return _groupedData[_selectedMonthKey]!;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FC),
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(context),
            if (_isLoading)
              const Expanded(child: Center(child: CircularProgressIndicator(color: AppTheme.primary)))
            else if (_errorMsg != null && _data.isEmpty)
              _buildErrorState()
            else if (_data.isEmpty)
              _buildEmptyState()
            else ...[
              _buildFilterSection(),
              Expanded(
                child: RefreshIndicator(
                  color: AppTheme.primary,
                  onRefresh: _loadPerkembangan,
                  child: ListView.builder(
                    physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
                    padding: const EdgeInsets.fromLTRB(20, 12, 20, 24),
                    itemCount: _filteredData.length,
                    itemBuilder: (context, index) {
                      return TweenAnimationBuilder<double>(
                        tween: Tween(begin: 0.0, end: 1.0),
                        duration: Duration(milliseconds: 400 + (index * 80)),
                        curve: Curves.easeOutCubic,
                        builder: (context, value, child) {
                          return Transform.translate(
                            offset: Offset(0, 16 * (1 - value)),
                            child: Opacity(opacity: value, child: child),
                          );
                        },
                        child: Padding(
                          padding: const EdgeInsets.only(bottom: 16),
                          child: _buildPerkembanganCard(_filteredData[index]),
                        ),
                      );
                    },
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  // ── HEADER ──
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
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Perkembangan Anak',
                style: TextStyle(color: AppTheme.textDark, fontSize: 18, fontWeight: FontWeight.w800),
              ),
              if (_data.isNotEmpty)
                Text(
                  '${_data[0].namaAnak} · ${_data[0].kelas}',
                  style: const TextStyle(color: AppTheme.textMedium, fontSize: 12, fontWeight: FontWeight.w500),
                ),
            ],
          ),
        ],
      ),
    );
  }

  // ── FILTER SECTION ──
  Widget _buildFilterSection() {
    if (_monthKeys.isEmpty) return const SizedBox.shrink();
    final parts = _selectedMonthKey!.split('-');
    final selectedBulan = int.parse(parts[1]);
    final selectedTahun = int.parse(parts[0]);

    return Container(
      margin: const EdgeInsets.fromLTRB(20, 12, 20, 4),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 2))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(width: 4, height: 16, decoration: BoxDecoration(color: AppTheme.primary, borderRadius: BorderRadius.circular(2))),
              const SizedBox(width: 8),
              const Text('Periode Laporan', style: TextStyle(color: AppTheme.textDark, fontSize: 14, fontWeight: FontWeight.w800)),
            ],
          ),
          const SizedBox(height: 14),
          Row(
            children: [
              Expanded(child: _buildDropdown<int>(
                value: selectedBulan,
                items: List.generate(12, (i) {
                  final bulan = i + 1;
                  final key = '$selectedTahun-${bulan.toString().padLeft(2, '0')}';
                  final hasData = _monthKeys.contains(key);
                  return DropdownMenuItem(value: bulan, enabled: hasData,
                    child: Text(_getMonthName(bulan), style: TextStyle(color: hasData ? AppTheme.textDark : AppTheme.textLight, fontSize: 13, fontWeight: FontWeight.w600)));
                }),
                onChanged: (bulan) {
                  if (bulan != null) {
                    final key = '$selectedTahun-${bulan.toString().padLeft(2, '0')}';
                    if (_monthKeys.contains(key)) setState(() => _selectedMonthKey = key);
                  }
                },
                icon: Icons.calendar_month_rounded,
              )),
              const SizedBox(width: 12),
              Expanded(child: _buildDropdown<int>(
                value: selectedTahun,
                items: _getAvailableYears().map((t) => DropdownMenuItem(value: t,
                  child: Text('$t', style: const TextStyle(color: AppTheme.textDark, fontSize: 13, fontWeight: FontWeight.w600)))).toList(),
                onChanged: (tahun) {
                  if (tahun != null) {
                    final key = '$tahun-${selectedBulan.toString().padLeft(2, '0')}';
                    if (_monthKeys.contains(key)) {
                      setState(() => _selectedMonthKey = key);
                    } else {
                      final first = _monthKeys.firstWhere((k) => k.startsWith('$tahun-'), orElse: () => '');
                      if (first.isNotEmpty) setState(() => _selectedMonthKey = first);
                    }
                  }
                },
                icon: Icons.date_range_rounded,
              )),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildDropdown<T>({required T value, required List<DropdownMenuItem<T>> items, required ValueChanged<T?> onChanged, required IconData icon}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14),
      decoration: BoxDecoration(
        color: const Color(0xFFF7F8FC),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE8E8EE)),
      ),
      child: Row(
        children: [
          Icon(icon, size: 16, color: AppTheme.primary.withOpacity(0.5)),
          const SizedBox(width: 8),
          Expanded(
            child: DropdownButton<T>(value: value, isExpanded: true, underline: const SizedBox.shrink(),
              icon: Icon(Icons.keyboard_arrow_down_rounded, color: AppTheme.textLight, size: 20), items: items, onChanged: onChanged),
          ),
        ],
      ),
    );
  }

  // ── PERKEMBANGAN CARD (PREMIUM) ──
  Widget _buildPerkembanganCard(PerkembanganModel data) {
    final statusColor = _getStatusColor(data.statusUtama);
    final statusBg = _getStatusBgColor(data.statusUtama);

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 16, offset: const Offset(0, 4)),
          BoxShadow(color: statusColor.withOpacity(0.06), blurRadius: 8, offset: const Offset(0, 2)),
        ],
      ),
      child: Column(
        children: [
          // Top accent bar
          Container(
            height: 4,
            decoration: BoxDecoration(
              gradient: LinearGradient(colors: [statusColor.withOpacity(0.8), statusColor.withOpacity(0.3)]),
              borderRadius: const BorderRadius.only(topLeft: Radius.circular(20), topRight: Radius.circular(20)),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Status badge + period
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(color: statusBg, borderRadius: BorderRadius.circular(20)),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container(width: 8, height: 8, decoration: BoxDecoration(color: statusColor, shape: BoxShape.circle)),
                          const SizedBox(width: 6),
                          Text(data.statusUtama, style: TextStyle(color: statusColor, fontSize: 12, fontWeight: FontWeight.w800)),
                        ],
                      ),
                    ),
                    const Spacer(),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(color: const Color(0xFFF0F1F5), borderRadius: BorderRadius.circular(20)),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(Icons.calendar_today_rounded, size: 12, color: AppTheme.textMedium),
                          const SizedBox(width: 5),
                          Text('${_getMonthName(data.bulan)} ${data.tahun}', style: const TextStyle(color: AppTheme.textMedium, fontSize: 11, fontWeight: FontWeight.w600)),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Info row — guru
                Row(
                  children: [
                    CircleAvatar(
                      radius: 16, backgroundColor: AppTheme.primary.withOpacity(0.1),
                      child: Text((data.namaGuru.isNotEmpty ? data.namaGuru : 'G')[0].toUpperCase(),
                        style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w800, fontSize: 13)),
                    ),
                    const SizedBox(width: 10),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Dinilai oleh', style: TextStyle(color: AppTheme.textLight, fontSize: 10, fontWeight: FontWeight.w600)),
                        Text(data.namaGuru.isNotEmpty ? data.namaGuru : 'N/A',
                          style: const TextStyle(color: AppTheme.textDark, fontSize: 13, fontWeight: FontWeight.w700)),
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Kategori list
                if (data.kategoriDetails.isNotEmpty) ...[
                  Row(
                    children: [
                      Container(width: 4, height: 16, decoration: BoxDecoration(color: AppTheme.primary, borderRadius: BorderRadius.circular(2))),
                      const SizedBox(width: 8),
                      const Text('Aspek Penilaian', style: TextStyle(color: AppTheme.textDark, fontSize: 14, fontWeight: FontWeight.w800)),
                    ],
                  ),
                  const SizedBox(height: 12),
                  ...data.kategoriDetails.map((k) => _buildKategoriItem(k)),
                ] else if (data.kategori.trim().isNotEmpty) ...[
                  Row(
                    children: [
                      Container(width: 4, height: 16, decoration: BoxDecoration(color: AppTheme.primary, borderRadius: BorderRadius.circular(2))),
                      const SizedBox(width: 8),
                      const Text('Kategori', style: TextStyle(color: AppTheme.textDark, fontSize: 14, fontWeight: FontWeight.w800)),
                    ],
                  ),
                  const SizedBox(height: 12),
                  ...data.kategori.split(',').map((k) => k.trim()).where((k) => k.isNotEmpty).map((k) => _buildKategoriFallbackItem(k)),
                ],

                // Template deskripsi
                if (data.templateDeskripsi.isNotEmpty) ...[
                  const SizedBox(height: 16),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: statusBg.withOpacity(0.5),
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: statusColor.withOpacity(0.2)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(Icons.format_quote_rounded, size: 16, color: statusColor),
                            const SizedBox(width: 6),
                            Text('Indikator Pencapaian', style: TextStyle(color: statusColor, fontSize: 12, fontWeight: FontWeight.w700)),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(data.templateDeskripsi, style: TextStyle(color: statusColor.withOpacity(0.9), fontSize: 12, fontWeight: FontWeight.w500, height: 1.6)),
                      ],
                    ),
                  ),
                ],

                // Catatan
                if (data.deskripsi.isNotEmpty) ...[
                  const SizedBox(height: 14),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(color: const Color(0xFFF7F8FC), borderRadius: BorderRadius.circular(14)),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Row(
                          children: [
                            Icon(Icons.edit_note_rounded, size: 16, color: AppTheme.textMedium),
                            SizedBox(width: 6),
                            Text('Catatan Guru', style: TextStyle(color: AppTheme.textDark, fontSize: 12, fontWeight: FontWeight.w700)),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(data.deskripsi, style: const TextStyle(color: AppTheme.textDark, fontSize: 12, fontWeight: FontWeight.w500, height: 1.6)),
                      ],
                    ),
                  ),
                ],

                // Status footer
                const SizedBox(height: 16),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(colors: [statusColor.withOpacity(0.1), statusColor.withOpacity(0.05)]),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: statusColor.withOpacity(0.15)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        _getStatusLabel(data.statusUtama),
                        style: TextStyle(color: statusColor, fontSize: 13, fontWeight: FontWeight.w700),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // ── KATEGORI ITEM ──
  Widget _buildKategoriItem(PerkembanganKategoriModel kategori) {
    final progress = kategori.nilai / 10.0;
    final kColor = _getStatusColor(kategori.statusUtama);
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFF9FAFB),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFEEEFF5)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(child: Text(kategori.namaKategori, style: const TextStyle(color: AppTheme.textDark, fontSize: 13, fontWeight: FontWeight.w700))),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  gradient: LinearGradient(colors: [AppTheme.primary, AppTheme.primaryLight]),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text('${kategori.nilai}/10', style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700)),
              ),
            ],
          ),
          const SizedBox(height: 10),
          // Progress bar
          ClipRRect(
            borderRadius: BorderRadius.circular(6),
            child: LinearProgressIndicator(
              value: progress,
              backgroundColor: const Color(0xFFE8E8EE),
              color: kColor,
              minHeight: 6,
            ),
          ),
          if (kategori.deskripsi.isNotEmpty) ...[
            const SizedBox(height: 8),
            Text(kategori.deskripsi, style: const TextStyle(color: AppTheme.textMedium, fontSize: 11, height: 1.5)),
          ],
        ],
      ),
    );
  }

  Widget _buildKategoriFallbackItem(String namaKategori) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(color: const Color(0xFFF9FAFB), borderRadius: BorderRadius.circular(12), border: Border.all(color: const Color(0xFFEEEFF5))),
      child: Row(
        children: [
          Container(width: 6, height: 6, decoration: const BoxDecoration(color: AppTheme.primary, shape: BoxShape.circle)),
          const SizedBox(width: 10),
          Text(namaKategori, style: const TextStyle(color: AppTheme.textDark, fontSize: 13, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }

  // ── STATES ──
  Widget _buildErrorState() {
    return Expanded(
      child: Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(padding: const EdgeInsets.all(20), decoration: BoxDecoration(color: AppTheme.danger.withOpacity(0.08), shape: BoxShape.circle),
                child: Icon(Icons.wifi_off_rounded, size: 48, color: AppTheme.danger.withOpacity(0.6))),
              const SizedBox(height: 20),
              const Text('Gagal Memuat', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppTheme.textDark)),
              const SizedBox(height: 8),
              Text(_errorMsg!, textAlign: TextAlign.center, style: const TextStyle(color: AppTheme.textMedium, fontSize: 13)),
              const SizedBox(height: 24),
              FilledButton.icon(
                onPressed: () { setState(() { _isLoading = true; _errorMsg = null; }); _loadPerkembangan(); },
                icon: const Icon(Icons.refresh_rounded, size: 18), label: const Text('Coba Lagi'),
                style: FilledButton.styleFrom(backgroundColor: AppTheme.primary, padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Expanded(
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(padding: const EdgeInsets.all(24), decoration: BoxDecoration(color: AppTheme.primary.withOpacity(0.06), shape: BoxShape.circle),
              child: Icon(Icons.child_care_rounded, size: 56, color: AppTheme.primary.withOpacity(0.4))),
            const SizedBox(height: 20),
            const Text('Belum Ada Data', style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800, color: AppTheme.textDark)),
            const SizedBox(height: 8),
            const Text('Data perkembangan akan muncul di sini', style: TextStyle(color: AppTheme.textMedium, fontSize: 13)),
          ],
        ),
      ),
    );
  }

  // ── HELPERS ──
  Color _getStatusColor(String status) {
    switch (status.toUpperCase()) {
      case 'BB': return const Color(0xFFEF4444);
      case 'MB': return const Color(0xFFF59E0B);
      case 'BSH': return const Color(0xFF22C55E);
      case 'BSB': return const Color(0xFF3B82F6);
      default: return AppTheme.textMedium;
    }
  }

  Color _getStatusBgColor(String status) {
    switch (status.toUpperCase()) {
      case 'BB': return const Color(0xFFFEE2E2);
      case 'MB': return const Color(0xFFFEF3C7);
      case 'BSH': return const Color(0xFFDCFCE7);
      case 'BSB': return const Color(0xFFDEF7FF);
      default: return const Color(0xFFF3F4F6);
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status.toUpperCase()) {
      case 'BB': return Icons.trending_down_rounded;
      case 'MB': return Icons.trending_flat_rounded;
      case 'BSH': return Icons.trending_up_rounded;
      case 'BSB': return Icons.star_rounded;
      default: return Icons.info_outline_rounded;
    }
  }

  String _getStatusLabel(String status) {
    switch (status.toUpperCase()) {
      case 'BB': return 'Belum Berkembang';
      case 'MB': return 'Mulai Berkembang';
      case 'BSH': return 'Berkembang Sesuai Harapan';
      case 'BSB': return 'Berkembang Sangat Baik';
      default: return status;
    }
  }

  String _getMonthName(int month) {
    const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return (month >= 1 && month <= 12) ? months[month] : '';
  }

  List<int> _getAvailableYears() {
    final years = _monthKeys.map((k) => int.parse(k.split('-')[0])).toSet().toList()..sort();
    return years;
  }
}
