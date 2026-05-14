import 'package:flutter/material.dart';
import 'dart:ui';
import '../theme/app_theme.dart';
import '../models/perkembangan_model.dart';
import '../services/api_services.dart';

class PerkembanganScreen extends StatefulWidget {
  final VoidCallback? onBackPressed;

  const PerkembanganScreen({super.key, this.onBackPressed});

  @override
  State<PerkembanganScreen> createState() => _PerkembanganScreenState();
}

class _PerkembanganScreenState extends State<PerkembanganScreen> with TickerProviderStateMixin {
  // --- MODERN COLOR SYSTEM (Orange Aesthetic) ---
  static const Color kPrimary = AppTheme.primary; 
  static const Color kSecondary = AppTheme.primaryLight;
  static const Color kBackground = Color(0xFFF8FAFC);
  static const Color kCardBackground = Colors.white;
  static const Color kTextMain = Color(0xFF1E293B);
  static const Color kTextMuted = Color(0xFF64748B);
  static const Color kSuccess = Color(0xFF10B981);

  List<PerkembanganModel> _data = [];
  bool _isLoading = true;
  String? _errorMsg;
  Map<String, List<PerkembanganModel>> _groupedData = {};
  List<String> _monthKeys = [];
  String? _selectedMonthKey;

  late AnimationController _progressController;

  @override
  void initState() {
    super.initState();
    _progressController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    );
    _loadPerkembangan();
  }

  @override
  void dispose() {
    _progressController.dispose();
    super.dispose();
  }

  Future<void> _loadPerkembangan() async {
    try {
      final data = await ApiService.getPerkembangan();
      if (mounted) {
        setState(() {
          _data = data;
          _groupDataByMonth();
          if (_monthKeys.isNotEmpty) {
            _selectedMonthKey = _monthKeys.last;
          }
          _isLoading = false;
        });
        _progressController.forward(from: 0.0);
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMsg = '$e';
        });
      }
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
    if (_selectedMonthKey == null || !_groupedData.containsKey(_selectedMonthKey)) {
      return [];
    }
    return _groupedData[_selectedMonthKey]!;
  }

  String _getMonthName(int month) {
    const names = [
      '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return month > 0 && month <= 12 ? names[month] : '';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: kBackground,
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(context),
            if (_isLoading)
              const Expanded(
                child: Center(
                  child: CircularProgressIndicator(
                    color: kPrimary,
                    strokeWidth: 3,
                  ),
                ),
              )
            else if (_errorMsg != null && _data.isEmpty)
              _buildErrorState()
            else if (_data.isEmpty)
              _buildEmptyState()
            else
              Expanded(
                child: RefreshIndicator(
                  color: kPrimary,
                  onRefresh: _loadPerkembangan,
                  child: ListView(
                    physics: const AlwaysScrollableScrollPhysics(
                      parent: BouncingScrollPhysics(),
                    ),
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                    children: [
                      _buildDateSelector(),
                      const SizedBox(height: 24),
                      if (_filteredData.isNotEmpty) ...[
                        _buildHeroSummaryCard(_filteredData[0]),
                        const SizedBox(height: 24),
                        _buildGuruAssessor(_filteredData[0]),
                        const SizedBox(height: 32),
                        _buildSectionTitle("Aspek Penilaian"),
                        const SizedBox(height: 16),
                        ..._filteredData[0].kategoriDetails.map((k) => _buildCategoryCard(k)),
                        const SizedBox(height: 24),
                        if (_filteredData[0].templateDeskripsi.isNotEmpty)
                          _buildIndicatorCard(_filteredData[0].templateDeskripsi),
                        const SizedBox(height: 16),
                        if (_filteredData[0].deskripsi.isNotEmpty)
                          _buildTeacherNoteCard(_filteredData[0].deskripsi),
                        const SizedBox(height: 32),
                      ],
                    ],
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  // --- 1. HEADER (CONSISTENT WITH OTHER SCREENS) ---
  Widget _buildHeader(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 20, 16),
      color: kBackground,
      child: Row(
        children: [
          IconButton(
            onPressed: widget.onBackPressed ?? () => Navigator.pop(context),
            icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 18),
            color: kPrimary,
          ),
          const SizedBox(width: 4),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Detail Perkembangan',
                  style: TextStyle(
                    color: kTextMain,
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                    letterSpacing: -0.3,
                  ),
                ),
                if (_data.isNotEmpty)
                  Text(
                    _data[0].namaAnak,
                    style: const TextStyle(
                      color: kTextMuted,
                      fontSize: 12,
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

  // --- 2. DATE SELECTOR ---
  Widget _buildDateSelector() {
    if (_monthKeys.isEmpty) return const SizedBox.shrink();

    final parts = _selectedMonthKey!.split('-');
    final month = int.parse(parts[1]);
    final year = parts[0];
    final label = "${_getMonthName(month)} $year";

    return Center(
      child: PopupMenuButton<String>(
        onSelected: (String value) {
          setState(() {
            _selectedMonthKey = value;
          });
          _progressController.forward(from: 0.0);
        },
        offset: const Offset(0, 45),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        itemBuilder: (BuildContext context) {
          return _monthKeys.map((String key) {
            final p = key.split('-');
            final m = int.parse(p[1]);
            final y = p[0];
            return PopupMenuItem<String>(
              value: key,
              child: Text("${_getMonthName(m)} $y"),
            );
          }).toList();
        },
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(30),
            border: Border.all(color: const Color(0xFFE2E8F0)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.03),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                label,
                style: const TextStyle(
                  color: kTextMain,
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                ),
              ),
              const SizedBox(width: 8),
              const Icon(Icons.keyboard_arrow_down_rounded, size: 18, color: kTextMuted),
            ],
          ),
        ),
      ),
    );
  }

  // --- 3. HERO SUMMARY CARD ---
  Widget _buildHeroSummaryCard(PerkembanganModel data) {
    double avg = 0;
    if (data.kategoriDetails.isNotEmpty) {
      avg = data.kategoriDetails.map((e) => e.nilai).reduce((a, b) => a + b) / data.kategoriDetails.length;
    } else {
      avg = data.nilaiChart.toDouble() * 2.5;
    }

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(32),
      decoration: BoxDecoration(
        color: kCardBackground,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: kPrimary.withOpacity(0.08),
            blurRadius: 30,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Column(
        children: [
          Stack(
            alignment: Alignment.center,
            children: [
              // Perfect Circle Background
              Container(
                width: 140,
                height: 140,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: const Color(0xFFF1F5F9), width: 12),
                ),
              ),
              // Perfect Circular Progress Ring
              SizedBox(
                width: 140,
                height: 140,
                child: AnimatedBuilder(
                  animation: _progressController,
                  builder: (context, child) {
                    return CustomPaint(
                      painter: _GradientCircularPainter(
                        progress: (avg / 10.0) * _progressController.value,
                        primaryColor: kPrimary,
                        secondaryColor: kSecondary,
                        strokeWidth: 12,
                      ),
                    );
                  },
                ),
              ),
              Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    avg.toStringAsFixed(1),
                    style: const TextStyle(
                      color: kTextMain,
                      fontSize: 40,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const Text(
                    "Score",
                    style: TextStyle(
                      color: kTextMuted,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ],
          ),
          const SizedBox(height: 24),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
            decoration: BoxDecoration(
              color: kPrimary.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Text(
              _getStatusLabel(data.statusUtama),
              style: const TextStyle(
                color: kPrimary,
                fontSize: 14,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }

  // --- 4. TEACHER INFO ---
  Widget _buildGuruAssessor(PerkembanganModel data) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFF1F5F9)),
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              gradient: AppTheme.primaryGradient,
              borderRadius: BorderRadius.circular(14),
            ),
            child: const Icon(Icons.person_outline_rounded, color: Colors.white),
          ),
          const SizedBox(width: 16),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                "Dinilai oleh",
                style: TextStyle(color: kTextMuted, fontSize: 12, fontWeight: FontWeight.w500),
              ),
              Text(
                data.namaGuru.isNotEmpty ? data.namaGuru : "Guru Sekolah",
                style: const TextStyle(color: kTextMain, fontSize: 16, fontWeight: FontWeight.w700),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // --- 5. CATEGORY CARDS ---
  Widget _buildCategoryCard(PerkembanganKategoriModel k) {
    final progress = k.nilai / 10.0;
    
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: kCardBackground,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFF1F5F9)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Text(
                k.namaKategori,
                style: const TextStyle(
                  color: kTextMain,
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                ),
              ),
              Text(
                k.nilai.toStringAsFixed(1),
                style: const TextStyle(
                  color: kTextMain,
                  fontSize: 18, // Matches header size
                  fontWeight: FontWeight.w900,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Stack(
            children: [
              Container(
                height: 8,
                width: double.infinity,
                decoration: BoxDecoration(
                  color: const Color(0xFFF1F5F9),
                  borderRadius: BorderRadius.circular(4),
                ),
              ),
              AnimatedBuilder(
                animation: _progressController,
                builder: (context, child) {
                  return FractionallySizedBox(
                    widthFactor: (progress * _progressController.value).clamp(0.0, 1.0),
                    child: Container(
                      height: 8,
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [kPrimary, kSecondary],
                        ),
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                  );
                },
              ),
            ],
          ),
          if (k.deskripsi.isNotEmpty) ...[
            const SizedBox(height: 16),
            Text(
              "${k.nilai}. ${_getShortDescription(k.namaKategori, k.nilai)}",
              style: const TextStyle(
                color: kTextMuted,
                fontSize: 13,
                height: 1.5,
                fontWeight: FontWeight.w400,
              ),
            ),
          ],
        ],
      ),
    );
  }

  String _getShortDescription(String kategori, int nilai) {
    int n = nilai;
    String k = kategori.toLowerCase();

    final map = {
      'akademik': {
        1: 'Belum mengenali materi dasar',
        2: 'Mulai mengenali materi dengan bantuan',
        3: 'Mulai mencoba memahami materi',
        4: 'Cukup memahami materi sederhana',
        5: 'Mulai berkembang dalam pembelajaran',
        6: 'Memahami materi cukup baik',
        7: 'Mampu belajar mandiri',
        8: 'Memahami materi dengan baik dan konsisten',
        9: 'Sangat aktif dalam pembelajaran',
        10: 'Sangat optimal dan melampaui target belajar'
      },
      'sosial': {
        1: 'Kesulitan berinteraksi',
        2: 'Mulai mengenali interaksi sosial',
        3: 'Mulai berinteraksi sederhana',
        4: 'Cukup mampu berinteraksi',
        5: 'Mulai berkembang dalam kerja sama',
        6: 'Berinteraksi cukup baik',
        7: 'Mampu bekerja sama mandiri',
        8: 'Sangat baik dan konsisten bersosialisasi',
        9: 'Sangat aktif dan percaya diri',
        10: 'Menjadi contoh positif bagi teman'
      },
      'emosional': {
        1: 'Belum mampu mengontrol emosi',
        2: 'Mulai mengenali emosi',
        3: 'Mulai mencoba mengendalikan emosi',
        4: 'Cukup mampu mengontrol emosi',
        5: 'Mulai berkembang secara emosional',
        6: 'Emosi cukup stabil',
        7: 'Mampu mengendalikan emosi mandiri',
        8: 'Stabil dan konsisten dalam pengendalian diri',
        9: 'Sangat baik dalam regulasi emosi',
        10: 'Sangat matang dan positif secara emosional'
      }
    };

    if (k.contains('akademik')) return map['akademik']?[n] ?? 'Perkembangan Akademik';
    if (k.contains('sosial')) return map['sosial']?[n] ?? 'Perkembangan Sosial';
    if (k.contains('emosional')) return map['emosional']?[n] ?? 'Perkembangan Emosional';

    return map[k]?[n] ?? "Perkembangan $kategori";
  }

  // --- 6. ADDITIONAL DESCRIPTION CARDS ---
  Widget _buildIndicatorCard(String text) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFFFFF7ED),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFFFEDD5)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              // Icon(Icons.auto_awesome_rounded, size: 18, color: kPrimary),
              // SizedBox(width: 8),
              Text(
                "Indikator Pencapaian",
                style: TextStyle(color: kPrimary, fontSize: 13, fontWeight: FontWeight.w700),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            text,
            style: const TextStyle(color: Color(0xFF92400E), fontSize: 13, height: 1.6),
          ),
        ],
      ),
    );
  }

  Widget _buildTeacherNoteCard(String text) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.chat_bubble_outline_rounded, size: 18, color: kTextMuted),
              SizedBox(width: 8),
              Text(
                "Catatan Tambahan Guru",
                style: TextStyle(color: kTextMuted, fontSize: 13, fontWeight: FontWeight.w700),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            text,
            style: const TextStyle(
              color: kTextMain,
              fontSize: 14,
              height: 1.6,
              fontStyle: FontStyle.italic,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: const TextStyle(
        color: kTextMain,
        fontSize: 18,
        fontWeight: FontWeight.w800,
      ),
    );
  }

  String _getStatusLabel(String status) {
    switch (status.toUpperCase()) {
      case "BSB": return "Berkembang Sangat Baik";
      case "BSH": return "Berkembang Sesuai Harapan";
      case "MB": return "Mulai Berkembang";
      case "BB": return "Belum Berkembang";
      default: return "Berkembang Sangat Baik";
    }
  }

  // --- HELPERS & STATES ---

  Widget _buildEmptyState() {
    return Expanded(
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.analytics_outlined, size: 80, color: kTextMuted.withOpacity(0.2)),
            const SizedBox(height: 16),
            const Text(
              "Belum ada data perkembangan",
              style: TextStyle(color: kTextMuted, fontSize: 16, fontWeight: FontWeight.w600),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildErrorState() {
    return Expanded(
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline_rounded, size: 60, color: Colors.redAccent),
            const SizedBox(height: 16),
            const Text(
              "Gagal memuat data",
              style: TextStyle(color: kTextMain, fontSize: 16, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 8),
            Text(_errorMsg ?? "Terjadi kesalahan", style: const TextStyle(color: kTextMuted)),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _loadPerkembangan,
              style: ElevatedButton.styleFrom(
                backgroundColor: kPrimary,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text("Coba Lagi"),
            ),
          ],
        ),
      ),
    );
  }
}

class _GradientCircularPainter extends CustomPainter {
  final double progress;
  final Color primaryColor;
  final Color secondaryColor;
  final double strokeWidth;

  _GradientCircularPainter({
    required this.progress,
    required this.primaryColor,
    required this.secondaryColor,
    required this.strokeWidth,
  });

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = (size.width - strokeWidth) / 2;
    final rect = Rect.fromCircle(center: center, radius: radius);

    final paint = Paint()
      ..shader = LinearGradient(
        colors: [primaryColor, secondaryColor],
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
      ).createShader(rect)
      ..strokeWidth = strokeWidth
      ..strokeCap = StrokeCap.round
      ..style = PaintingStyle.stroke;

    canvas.drawArc(
      rect,
      -3.141592653589793 / 2, // Start at top
      2 * 3.141592653589793 * progress.clamp(0.0, 1.0),
      false,
      paint,
    );
  }

  @override
  bool shouldRepaint(covariant _GradientCircularPainter oldDelegate) {
    return oldDelegate.progress != progress;
  }
}
