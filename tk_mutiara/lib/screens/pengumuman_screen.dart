import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/pengumuman_model.dart';
import '../services/api_services.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'dart:convert';

class PengumumanScreen extends StatefulWidget {
  final int? idPengumuman;
  final VoidCallback? onBackPressed;

  const PengumumanScreen({
    super.key,
    this.idPengumuman,
    this.onBackPressed,
  });

  @override
  State<PengumumanScreen> createState() => _PengumumanScreenState();
}

class _PengumumanScreenState extends State<PengumumanScreen> with TickerProviderStateMixin {
  static const String imageBaseUrl = 'https://admin.tkmutiara.my.id/storage/';

  List<PengumumanModel> _data = [];
  PengumumanModel? _selectedData;
  bool _isLoading = true;
  String? _errorMsg;
  late AnimationController _fadeController;
  final PageController _imagePageController = PageController();
  int _currentImageIndex = 0;

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    );
    _loadPengumuman();
  }

  @override
  void dispose() {
    _fadeController.dispose();
    _imagePageController.dispose();
    super.dispose();
  }

  void _showDetail(PengumumanModel pengumuman) {
    setState(() {
      _selectedData = pengumuman;
      _currentImageIndex = 0;
    });
  }

  void _backToList() {
    setState(() => _selectedData = null);
  }

  Future<void> _loadPengumuman() async {
    try {
      final data = await ApiService.getPengumuman();
      setState(() {
        _data = data;
        _isLoading = false;
        if (widget.idPengumuman != null) {
          try {
            _selectedData = _data.firstWhere((p) => p.idPengumuman == widget.idPengumuman);
          } catch (_) {}
        }
      });
      _fadeController.forward();
    } catch (e) {
      setState(() {
        _isLoading = false;
        _errorMsg = '$e';
      });
    }
  }

  String _formatDate(String dateString) {
    try {
      final date = DateTime.parse(dateString);
      final now = DateTime.now();
      final diff = now.difference(date);
      if (diff.inSeconds < 60) return 'Baru saja';
      if (diff.inMinutes < 60) return '${diff.inMinutes}m lalu';
      if (diff.inHours < 24) return '${diff.inHours}j lalu';
      if (diff.inDays < 7) return '${diff.inDays}h lalu';
      final months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
      return '${date.day} ${months[date.month - 1]} ${date.year}';
    } catch (_) {
      return dateString;
    }
  }

  String _getImageUrl(String media) {
    if (media.isEmpty) return '';

    try {
      final decoded = jsonDecode(media);
      if (decoded is List && decoded.isNotEmpty) {
        return _getImageUrlFromPath(decoded.first.toString());
      }
    } catch (_) {
      return _getImageUrlFromPath(media);
    }

    return _getImageUrlFromPath(media);
  }

  // Build image URL from a single path (no JSON parsing needed)
  String _getImageUrlFromPath(String path) {
    if (path.isEmpty) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path;
    }

    String cleanPath = path.startsWith('/') ? path.substring(1) : path;
    if (cleanPath.startsWith('storage/')) {
      return Uri.encodeFull('https://admin.tkmutiara.my.id/$cleanPath');
    }

    return Uri.encodeFull('$imageBaseUrl$cleanPath');
  }

  @override
  Widget build(BuildContext context) {
    if (_selectedData != null) return _buildDetailView(context);

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
            else
              Expanded(
                child: RefreshIndicator(
                  color: AppTheme.primary,
                  edgeOffset: 20,
                  onRefresh: _loadPengumuman,
                  child: ListView.builder(
                    physics: const AlwaysScrollableScrollPhysics(parent: ClampingScrollPhysics()),
                    padding: const EdgeInsets.fromLTRB(20, 8, 20, 24),
                    itemCount: _data.length,
                    itemBuilder: (context, index) {
                      return TweenAnimationBuilder<double>(
                        tween: Tween(begin: 0.0, end: 1.0),
                        duration: Duration(milliseconds: 400 + (index * 100)),
                        curve: Curves.easeOutCubic,
                        builder: (context, value, child) {
                          return Transform.translate(
                            offset: Offset(0, 20 * (1 - value)),
                            child: Opacity(opacity: value, child: child),
                          );
                        },
                        child: Padding(
                          padding: const EdgeInsets.only(bottom: 16),
                          child: _buildPengumumanCard(_data[index]),
                        ),
                      );
                    },
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildErrorState() {
    return Expanded(
      child: Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: AppTheme.danger.withOpacity(0.08),
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.wifi_off_rounded, size: 48, color: AppTheme.danger.withOpacity(0.6)),
              ),
              const SizedBox(height: 20),
              const Text('Gagal Memuat', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppTheme.textDark)),
              const SizedBox(height: 8),
              Text(_errorMsg!, textAlign: TextAlign.center, style: const TextStyle(color: AppTheme.textMedium, fontSize: 13)),
              const SizedBox(height: 24),
              FilledButton.icon(
                onPressed: () { setState(() { _isLoading = true; _errorMsg = null; }); _loadPengumuman(); },
                icon: const Icon(Icons.refresh_rounded, size: 18),
                label: const Text('Coba Lagi'),
                style: FilledButton.styleFrom(backgroundColor: AppTheme.primary, padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 14), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
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
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(color: AppTheme.primary.withOpacity(0.06), shape: BoxShape.circle),
              child: Icon(Icons.campaign_outlined, size: 56, color: AppTheme.primary.withOpacity(0.4)),
            ),
            const SizedBox(height: 20),
            const Text('Belum Ada Pengumuman', style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800, color: AppTheme.textDark)),
            const SizedBox(height: 8),
            const Text('Pengumuman baru akan muncul di sini', style: TextStyle(color: AppTheme.textMedium, fontSize: 13)),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    final isDetail = _selectedData != null;
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 20, 16),
      color: AppTheme.white,
      child: Row(
        children: [
          IconButton(
            onPressed: (isDetail && widget.idPengumuman == null)
                ? () => _backToList()
                : (widget.onBackPressed ?? () => Navigator.pop(context)),
            icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 18),
            color: AppTheme.primary,
          ),
          const SizedBox(width: 4),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                isDetail ? 'Detail Pengumuman' : 'Pengumuman',
                style: const TextStyle(color: AppTheme.textDark, fontSize: 18, fontWeight: FontWeight.w800),
              ),
              if (!isDetail && _data.isNotEmpty)
                Text(
                  '${_data.length} pengumuman tersedia',
                  style: TextStyle(color: AppTheme.textMedium, fontSize: 12, fontWeight: FontWeight.w500),
                ),
            ],
          ),
        ],
      ),
    );
  }

  // ── DETAIL VIEW ──
  Widget _buildDetailView(BuildContext context) {
    final data = _selectedData!;
    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FC),
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(context),
            Expanded(
              child: SingleChildScrollView(
                physics: const ClampingScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Hero image(s) with swipe support
                    if (data.mediaPaths.isNotEmpty)
                      Container(
                        width: double.infinity,
                        height: 280,
                        margin: const EdgeInsets.fromLTRB(20, 16, 20, 0),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEEEFF5),
                          borderRadius: BorderRadius.circular(20),
                          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 20, offset: const Offset(0, 8))],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(20),
                          child: Stack(
                            fit: StackFit.expand,
                            children: [
                              // PageView for multiple images
                              PageView.builder(
                                controller: _imagePageController,
                                itemCount: data.mediaPaths.length,
                                onPageChanged: (index) => setState(() => _currentImageIndex = index),
                                itemBuilder: (context, index) {
                                  final imageUrl = _getImageUrlFromPath(data.mediaPaths[index]);
                                  return CachedNetworkImage(
                                    imageUrl: imageUrl,
                                    fit: BoxFit.cover,
                                    memCacheHeight: 800, // Optimasi memori untuk gambar besar
                                    placeholder: (context, url) => Center(
                                      child: CircularProgressIndicator(
                                        strokeWidth: 2.5,
                                        color: AppTheme.primary,
                                      ),
                                    ),
                                    errorWidget: (context, url, error) => const Center(
                                      child: Icon(Icons.image_not_supported_rounded, size: 48, color: AppTheme.textLight),
                                    ),
                                  );
                                },
                              ),
                              // Bottom gradient overlay
                              Positioned(
                                bottom: 0, left: 0, right: 0,
                                child: Container(
                                  height: 80,
                                  decoration: BoxDecoration(
                                    gradient: LinearGradient(
                                      begin: Alignment.topCenter, end: Alignment.bottomCenter,
                                      colors: [Colors.transparent, Colors.black.withOpacity(0.4)],
                                    ),
                                  ),
                                ),
                              ),
                              // Dot indicators (only if more than 1 image)
                              if (data.mediaPaths.length > 1)
                                Positioned(
                                  bottom: 12, left: 0, right: 0,
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: List.generate(data.mediaPaths.length, (index) {
                                      final isActive = index == _currentImageIndex;
                                      return AnimatedContainer(
                                        duration: const Duration(milliseconds: 250),
                                        margin: const EdgeInsets.symmetric(horizontal: 3),
                                        width: isActive ? 20 : 7,
                                        height: 7,
                                        decoration: BoxDecoration(
                                          color: isActive ? Colors.white : Colors.white.withOpacity(0.5),
                                          borderRadius: BorderRadius.circular(4),
                                          boxShadow: isActive ? [BoxShadow(color: Colors.white.withOpacity(0.3), blurRadius: 4)] : [],
                                        ),
                                      );
                                    }),
                                  ),
                                ),
                              // Image counter badge
                              if (data.mediaPaths.length > 1)
                                Positioned(
                                  top: 12, left: 12,
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                    decoration: BoxDecoration(
                                      color: Colors.black.withOpacity(0.5),
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        const Icon(Icons.photo_library_rounded, size: 13, color: Colors.white),
                                        const SizedBox(width: 4),
                                        Text('${_currentImageIndex + 1}/${data.mediaPaths.length}',
                                          style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700)),
                                      ],
                                    ),
                                  ),
                                ),
                              // Time badge
                              Positioned(
                                top: 12, right: 12,
                                child: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                  decoration: BoxDecoration(
                                    color: Colors.white.withOpacity(0.92),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      const Icon(Icons.access_time_rounded, size: 13, color: AppTheme.primary),
                                      const SizedBox(width: 4),
                                      Text(_formatDate(data.waktuUnggah), style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.textDark)),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),

                    // Title & meta
                    Padding(
                      padding: const EdgeInsets.fromLTRB(20, 20, 20, 0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(data.judul, style: const TextStyle(color: AppTheme.textDark, fontSize: 24, fontWeight: FontWeight.w900, letterSpacing: -0.5, height: 1.2)),
                          const SizedBox(height: 14),
                          // Author chip
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                            decoration: BoxDecoration(
                              gradient: LinearGradient(colors: [AppTheme.primary.withOpacity(0.08), AppTheme.primary.withOpacity(0.02)]),
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(color: AppTheme.primary.withOpacity(0.1)),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                CircleAvatar(
                                  radius: 16,
                                  backgroundColor: AppTheme.primary.withOpacity(0.15),
                                  child: Text(
                                    (data.namaGuru.isNotEmpty ? data.namaGuru : 'A')[0].toUpperCase(),
                                    style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w800, fontSize: 14),
                                  ),
                                ),
                                const SizedBox(width: 10),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    const Text('Diposting oleh', style: TextStyle(color: AppTheme.textMedium, fontSize: 10, fontWeight: FontWeight.w600)),
                                    Text(data.namaGuru.isNotEmpty ? data.namaGuru : 'Admin', style: const TextStyle(color: AppTheme.textDark, fontSize: 13, fontWeight: FontWeight.w800)),
                                  ],
                                ),
                                if (data.mediaPaths.isEmpty) ...[
                                  const Spacer(),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                    decoration: BoxDecoration(color: AppTheme.primary.withOpacity(0.08), borderRadius: BorderRadius.circular(20)),
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        const Icon(Icons.access_time_rounded, size: 12, color: AppTheme.primary),
                                        const SizedBox(width: 4),
                                        Text(_formatDate(data.waktuUnggah), style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppTheme.textDark)),
                                      ],
                                    ),
                                  ),
                                ],
                              ],
                            ),
                          ),
                          const SizedBox(height: 20),

                          // Deskripsi card
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.all(20),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(18),
                              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 12, offset: const Offset(0, 4))],
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Container(width: 4, height: 18, decoration: BoxDecoration(color: AppTheme.primary, borderRadius: BorderRadius.circular(2))),
                                    const SizedBox(width: 10),
                                    const Text('Isi Pengumuman', style: TextStyle(color: AppTheme.textDark, fontSize: 15, fontWeight: FontWeight.w800)),
                                  ],
                                ),
                                const SizedBox(height: 14),
                                Text(
                                  data.deskripsi,
                                  textAlign: TextAlign.justify,
                                  style: const TextStyle(color: AppTheme.textDark, fontSize: 14, fontWeight: FontWeight.w500, height: 1.8, letterSpacing: 0.1),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 32),
                        ],
                      ),
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

  // ── LIST CARD ──
  Widget _buildPengumumanCard(PengumumanModel data) {
    final hasImage = data.media.isNotEmpty;
    return GestureDetector(
      onTap: () => _showDetail(data),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 16, offset: const Offset(0, 4)),
            BoxShadow(color: AppTheme.primary.withOpacity(0.03), blurRadius: 8, offset: const Offset(0, 2)),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(18),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Image section (top) — only if has media
              if (hasImage)
                SizedBox(
                  height: 160,
                  width: double.infinity,
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      CachedNetworkImage(
                        imageUrl: _getImageUrl(data.media),
                        fit: BoxFit.cover,
                        memCacheHeight: 400, // Optimasi: jangan decode gambar 1MB full untuk list kecil
                        placeholder: (context, url) => Container(
                          color: const Color(0xFFEEEFF5),
                          child: Center(
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              color: AppTheme.primary,
                            ),
                          ),
                        ),
                        errorWidget: (context, url, error) => Container(
                          color: const Color(0xFFEEEFF5),
                          child: const Center(
                            child: Icon(Icons.image_outlined, size: 36, color: AppTheme.textLight),
                          ),
                        ),
                      ),
                      // Gradient overlay
                      Positioned(
                        bottom: 0, left: 0, right: 0,
                        child: Container(
                          height: 60,
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              begin: Alignment.topCenter, end: Alignment.bottomCenter,
                              colors: [Colors.transparent, Colors.black.withOpacity(0.35)],
                            ),
                          ),
                        ),
                      ),
                      // Time badge on image
                      Positioned(
                        top: 10, right: 10,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                          decoration: BoxDecoration(color: Colors.white.withOpacity(0.92), borderRadius: BorderRadius.circular(20)),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const Icon(Icons.access_time_rounded, size: 12, color: AppTheme.primary),
                              const SizedBox(width: 4),
                              Text(_formatDate(data.waktuUnggah), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: AppTheme.textDark)),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),

              // Content section
              Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Author row + time (if no image)
                    Row(
                      children: [
                        CircleAvatar(
                          radius: 14,
                          backgroundColor: AppTheme.primary.withOpacity(0.1),
                          child: Text(
                            (data.namaGuru.isNotEmpty ? data.namaGuru : 'A')[0].toUpperCase(),
                            style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w800, fontSize: 11),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            data.namaGuru.isNotEmpty ? data.namaGuru : 'Admin',
                            style: const TextStyle(color: AppTheme.textMedium, fontSize: 12, fontWeight: FontWeight.w700),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        if (!hasImage) ...[
                          const Icon(Icons.access_time_rounded, size: 13, color: AppTheme.textLight),
                          const SizedBox(width: 4),
                          Text(_formatDate(data.waktuUnggah), style: const TextStyle(color: AppTheme.textLight, fontSize: 11, fontWeight: FontWeight.w600)),
                        ],
                      ],
                    ),
                    const SizedBox(height: 10),
                    // Title
                    Text(
                      data.judul,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: AppTheme.textDark, fontSize: 16, fontWeight: FontWeight.w800, letterSpacing: -0.3, height: 1.3),
                    ),
                    const SizedBox(height: 6),
                    // Description preview
                    Text(
                      data.deskripsi,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(color: AppTheme.textMedium.withOpacity(0.8), fontSize: 13, height: 1.5),
                    ),
                    const SizedBox(height: 12),
                    // Read more link
                    Row(
                      children: [
                        const Spacer(),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
                          decoration: BoxDecoration(
                            gradient: LinearGradient(colors: [AppTheme.primary, AppTheme.primaryLight]),
                            borderRadius: BorderRadius.circular(20),
                            boxShadow: [BoxShadow(color: AppTheme.primary.withOpacity(0.25), blurRadius: 8, offset: const Offset(0, 3))],
                          ),
                          child: const Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Text('Baca', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w700)),
                              SizedBox(width: 4),
                              Icon(Icons.arrow_forward_rounded, size: 14, color: Colors.white),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
