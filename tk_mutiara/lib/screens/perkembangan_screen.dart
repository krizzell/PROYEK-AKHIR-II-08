import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/perkembangan_model.dart';
import '../services/api_services.dart';

class PerkembanganScreen extends StatefulWidget {
  const PerkembanganScreen({super.key});

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

  void _loadPerkembangan() async {
    try {
      print('Loading perkembangan...');
      final data = await ApiService.getPerkembangan();
      setState(() {
        _data = data;
        _groupDataByMonth();
        if (_monthKeys.isNotEmpty) {
          _selectedMonthKey = _monthKeys.last; // Pilih bulan terakhir sebagai default
        }
        _isLoading = false;
        print('Loaded ${_data.length} perkembangan records');
        for (var item in _data) {
          print('Record: ${item.namaAnak}, Guru: ${item.namaGuru}, Kategori: ${item.kategoriDetails.length}');
        }
      });
    } catch (e) {
      print('Error loading perkembangan: $e');
      setState(() {
        _isLoading = false;
        _errorMsg = '$e';
      });
    }
  }

  void _groupDataByMonth() {
    _groupedData.clear();
    _monthKeys.clear();

    for (var item in _data) {
      final key = '${item.tahun}-${item.bulan.toString().padLeft(2, '0')}';
      if (!_groupedData.containsKey(key)) {
        _groupedData[key] = [];
      }
      _groupedData[key]!.add(item);
    }

    // Sort bulan dari tertua ke terbaru
    _monthKeys = _groupedData.keys.toList();
    _monthKeys.sort((a, b) => a.compareTo(b));
  }

  List<PerkembanganModel> get _filteredData {
    if (_selectedMonthKey == null || !_groupedData.containsKey(_selectedMonthKey)) {
      return [];
    }
    return _groupedData[_selectedMonthKey]!;
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
                  child: CircularProgressIndicator(
                    color: AppTheme.primary,
                  ),
                ),
              ),
            ],
          ),
        ),
      );
    }

    if (_errorMsg != null && _data.isEmpty) {
      return Scaffold(
        backgroundColor: AppTheme.background,
        body: SafeArea(
          child: Column(
            children: [
              _buildHeader(context),
              Expanded(
                child: Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.error_outline, size: 64, color: AppTheme.textLight),
                      const SizedBox(height: 16),
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 24),
                        child: Text(
                          _errorMsg!,
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            color: AppTheme.textMedium,
                            fontSize: 14,
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),
                      ElevatedButton.icon(
                        onPressed: () {
                          setState(() {
                            _isLoading = true;
                            _errorMsg = null;
                          });
                          _loadPerkembangan();
                        },
                        icon: const Icon(Icons.refresh),
                        label: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      );
    }

    if (_data.isEmpty) {
      return Scaffold(
        backgroundColor: AppTheme.background,
        body: SafeArea(
          child: Column(
            children: [
              _buildHeader(context),
              const Expanded(
                child: Center(
                  child: Text('Tidak ada data perkembangan'),
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
            _buildFilterSection(),
            Expanded(
              child: ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: _filteredData.length,
                itemBuilder: (context, index) => _buildPerkembanganCard(_filteredData[index]),
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _getMonthYearDisplay(String monthKey) {
    // Format: "2026-04" -> "April 2026"
    final parts = monthKey.split('-');
    final tahun = int.parse(parts[0]);
    final bulan = int.parse(parts[1]);
    return '${_getMonthName(bulan)} $tahun';
  }

  Widget _buildFilterSection() {
    if (_monthKeys.isEmpty) {
      return const SizedBox.shrink();
    }

    final currentMonth = _selectedMonthKey != null ? _getMonthYearDisplay(_selectedMonthKey!) : '';
    final parts = _selectedMonthKey!.split('-');
    final selectedBulan = int.parse(parts[1]);
    final selectedTahun = int.parse(parts[0]);

    return Container(
      padding: const EdgeInsets.all(16),
      color: AppTheme.white,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Periode Laporan',
            style: TextStyle(
              color: AppTheme.textMedium,
              fontSize: 12,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  decoration: BoxDecoration(
                    border: Border.all(color: const Color(0xFFE5E5E5)),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: DropdownButton<int>(
                    value: selectedBulan,
                    isExpanded: true,
                    underline: const SizedBox.shrink(),
                    items: List.generate(12, (index) {
                      final bulan = index + 1;
                      final key = '$selectedTahun-${bulan.toString().padLeft(2, '0')}';
                      final hasData = _monthKeys.contains(key);
                      return DropdownMenuItem(
                        value: bulan,
                        enabled: hasData,
                        child: Text(
                          _getMonthName(bulan),
                          style: TextStyle(
                            color: hasData ? AppTheme.textDark : AppTheme.textLight,
                            fontSize: 13,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      );
                    }),
                    onChanged: (bulan) {
                      if (bulan != null) {
                        final key = '$selectedTahun-${bulan.toString().padLeft(2, '0')}';
                        if (_monthKeys.contains(key)) {
                          setState(() {
                            _selectedMonthKey = key;
                          });
                        }
                      }
                    },
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  decoration: BoxDecoration(
                    border: Border.all(color: const Color(0xFFE5E5E5)),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: DropdownButton<int>(
                    value: selectedTahun,
                    isExpanded: true,
                    underline: const SizedBox.shrink(),
                    items: _getAvailableYears().map((tahun) {
                      return DropdownMenuItem(
                        value: tahun,
                        child: Text(
                          '$tahun',
                          style: const TextStyle(
                            color: AppTheme.textDark,
                            fontSize: 13,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      );
                    }).toList(),
                    onChanged: (tahun) {
                      if (tahun != null) {
                        final key = '$tahun-${selectedBulan.toString().padLeft(2, '0')}';
                        if (_monthKeys.contains(key)) {
                          setState(() {
                            _selectedMonthKey = key;
                          });
                        } else {
                          // Jika kombinasi tidak ada, ambil bulan pertama dari tahun itu
                          final firstMonthInYear = _monthKeys.firstWhere(
                            (k) => k.startsWith('$tahun-'),
                            orElse: () => '',
                          );
                          if (firstMonthInYear.isNotEmpty) {
                            setState(() {
                              _selectedMonthKey = firstMonthInYear;
                            });
                          }
                        }
                      }
                    },
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 20, 16),
      decoration: BoxDecoration(
        color: AppTheme.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          IconButton(
            onPressed: () => Navigator.pop(context),
            icon: const Icon(
              Icons.arrow_back_ios_new_rounded,
              color: AppTheme.textDark,
              size: 20,
            ),
          ),
          const SizedBox(width: 4),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Detail Perkembangan Anak',
                style: TextStyle(
                  color: AppTheme.textDark,
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                ),
              ),
              Text(
                '${_data.isNotEmpty ? _data[0].namaAnak : "Siswa"} · ${_data.isNotEmpty ? _data[0].kelas : ""}',
                style: TextStyle(
                  color: AppTheme.primary,
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
          const Spacer(),
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: const Color(0xFFFFEDE0),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(
              Icons.child_care_rounded,
              color: AppTheme.primary,
              size: 22,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPerkembanganCard(PerkembanganModel data) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Row 1: Nama Anak & Kelas
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Nama Anak',
                      style: TextStyle(
                        color: AppTheme.textMedium,
                        fontSize: 11,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      data.namaAnak,
                      style: const TextStyle(
                        color: AppTheme.textDark,
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Kelas',
                      style: TextStyle(
                        color: AppTheme.textMedium,
                        fontSize: 11,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      data.kelas,
                      style: const TextStyle(
                        color: AppTheme.textDark,
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // Row 2: Guru
          const Text(
            'Guru',
            style: TextStyle(
              color: AppTheme.textMedium,
              fontSize: 11,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            data.namaGuru.isNotEmpty ? data.namaGuru : 'N/A',
            style: const TextStyle(
              color: AppTheme.textDark,
              fontSize: 14,
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: 16),

          // Row 3: Periode Laporan
          const Text(
            'Periode Laporan',
            style: TextStyle(
              color: AppTheme.textMedium,
              fontSize: 11,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            '${_getMonthName(data.bulan)} ${data.tahun}',
            style: const TextStyle(
              color: AppTheme.textDark,
              fontSize: 14,
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: 20),

          // Divider
          Container(
            height: 1,
            color: const Color(0xFFE5E5E5),
          ),
          const SizedBox(height: 20),

          // Kategori Perkembangan
          const Text(
            'Kategori Perkembangan',
            style: TextStyle(
              color: AppTheme.textDark,
              fontSize: 14,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 16),

          // List kategori details
          ...data.kategoriDetails.map((kategori) => _buildKategoriItem(kategori)),

          const SizedBox(height: 20),
          Container(
            height: 1,
            color: const Color(0xFFE5E5E5),
          ),
          const SizedBox(height: 20),

          // Deskripsi Template Indikator
          if (data.templateDeskripsi.isNotEmpty)
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Deskripsi Template Indikator',
                  style: TextStyle(
                    color: AppTheme.textDark,
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 12),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: _getStatusBgColor(data.statusUtama).withOpacity(0.3),
                    border: Border.all(
                      color: _getStatusColor(data.statusUtama),
                      width: 1.5,
                    ),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    data.templateDeskripsi,
                    style: TextStyle(
                      color: _getStatusColor(data.statusUtama),
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                      height: 1.6,
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                Container(
                  height: 1,
                  color: const Color(0xFFE5E5E5),
                ),
                const SizedBox(height: 20),
              ],
            ),

          // Catatan Tambahan
          if (data.deskripsi.isNotEmpty)
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Catatan Tambahan',
                  style: TextStyle(
                    color: AppTheme.textDark,
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 12),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFAFAFA),
                    border: Border.all(color: const Color(0xFFE5E5E5)),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    data.deskripsi,
                    style: const TextStyle(
                      color: AppTheme.textDark,
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                      height: 1.6,
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                Container(
                  height: 1,
                  color: const Color(0xFFE5E5E5),
                ),
                const SizedBox(height: 20),
              ],
            ),

          // Status Pencapaian
          const Text(
            'Status Pencapaian',
            style: TextStyle(
              color: AppTheme.textMedium,
              fontSize: 11,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 10),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
            decoration: BoxDecoration(
              color: _getStatusBgColor(data.statusUtama),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              _getStatusLabel(data.statusUtama),
              textAlign: TextAlign.center,
              style: TextStyle(
                color: _getStatusColor(data.statusUtama),
                fontSize: 14,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildKategoriItem(PerkembanganKategoriModel kategori) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFFAFAFA),
        border: Border.all(color: const Color(0xFFE5E5E5)),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                kategori.namaKategori,
                style: const TextStyle(
                  color: AppTheme.textDark,
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: AppTheme.primary,
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  'Nilai ${kategori.nilai}/10',
                  style: const TextStyle(
                    color: AppTheme.white,
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            kategori.deskripsi.isNotEmpty ? kategori.deskripsi : 'Tidak ada deskripsi',
            style: const TextStyle(
              color: AppTheme.textDark,
              fontSize: 12,
              fontWeight: FontWeight.w500,
              height: 1.5,
            ),
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toUpperCase()) {
      case 'BB':
        return const Color(0xFFEF4444);
      case 'MB':
        return const Color(0xFFF59E0B);
      case 'BSH':
        return const Color(0xFF22C55E);
      case 'BSB':
        return const Color(0xFF3B82F6);
      default:
        return AppTheme.textMedium;
    }
  }

  Color _getStatusBgColor(String status) {
    switch (status.toUpperCase()) {
      case 'BB':
        return const Color(0xFFFEE2E2);
      case 'MB':
        return const Color(0xFFFEF3C7);
      case 'BSH':
        return const Color(0xFFDCFCE7);
      case 'BSB':
        return const Color(0xFFDEF7FF);
      default:
        return const Color(0xFFF3F4F6);
    }
  }

  String _getStatusLabel(String status) {
    switch (status.toUpperCase()) {
      case 'BB':
        return 'BB - Belum Berkembang';
      case 'MB':
        return 'MB - Mulai Berkembang';
      case 'BSH':
        return 'BSH - Berkembang Sesuai Harapan';
      case 'BSB':
        return 'BSB - Berkembang Sangat Baik';
      default:
        return status;
    }
  }

  String _getMonthName(int month) {
    const months = [
      '',
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember',
    ];
    if (month >= 1 && month <= 12) {
      return months[month];
    }
    return '';
  }

  List<int> _getAvailableYears() {
    final years = _monthKeys.map((key) {
      return int.parse(key.split('-')[0]);
    }).toSet().toList();
    years.sort((a, b) => a.compareTo(b));
    return years;
  }
}
