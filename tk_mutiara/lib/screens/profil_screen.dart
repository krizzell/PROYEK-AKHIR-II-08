import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../theme/app_theme.dart';
import '../services/api_services.dart';

class ProfilScreen extends StatefulWidget {
  final Function(String nama, String email)? onProfilUpdated;
  final String namaAwal;
  final String emailAwal;

  const ProfilScreen({
    super.key,
    this.onProfilUpdated,
  this.namaAwal = 'Bintang Mutiara',
  this.emailAwal = 'anak@tkmutiara.com', 
  });

  @override
  State<ProfilScreen> createState() => _ProfilScreenState();
}

class _ProfilScreenState extends State<ProfilScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late Animation<double> _fadeAnim;
  late Animation<Offset> _slideAnim;

  late final TextEditingController _namaController;
  late final TextEditingController _emailController;
  late final TextEditingController _hpController;

  // Password controllers
  final _passwordLamaController = TextEditingController();
  final _passwordBaruController = TextEditingController();
  final _passwordKonfirmasiController = TextEditingController();

  bool _obscureLama = true;
  bool _obscureBaru = true;
  bool _obscureKonfirmasi = true;

  bool _isEditingProfil = false;
  bool _isLoadingSave = false;
  bool _isLoadingPassword = false;

  String? _errorPassword;
  String? _successPassword;

  @override
  void initState() {
    super.initState();

    _namaController = TextEditingController(text: widget.namaAwal);
    _emailController = TextEditingController(text: widget.emailAwal);
    _hpController = TextEditingController(text: '08123456789');

    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 700),
    );
    _fadeAnim = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _slideAnim = Tween<Offset>(begin: const Offset(0, 0.15), end: Offset.zero)
        .animate(
          CurvedAnimation(parent: _animController, curve: Curves.easeOutCubic),
        );
    _animController.forward();
  }

  @override
  void dispose() {
    _animController.dispose();
    _namaController.dispose();
    _emailController.dispose();
    _hpController.dispose();
    _passwordLamaController.dispose();
    _passwordBaruController.dispose();
    _passwordKonfirmasiController.dispose();
    super.dispose();
  }

  void _simpanProfil() async {
    if (_namaController.text.isEmpty || _hpController.text.isEmpty) {
      _showSnackbar('Nama dan No. HP tidak boleh kosong', isError: true);
      return;
    }

    setState(() => _isLoadingSave = true);
    
    // Prepare data berdasarkan role
    Map<String, dynamic> updateData = {};
    final userInfo = ApiService.userInfo;
    
    if (userInfo?['role'] == 'orangtua') {
      updateData = {
        'nama_ortu': _namaController.text.trim(),
        'no_hp': _hpController.text.trim(),
        'alamat': _emailController.text.trim(), // Untuk orangtua, email controller digunakan untuk alamat
      };
    } else if (userInfo?['role'] == 'guru') {
      updateData = {
        'nama_guru': _namaController.text.trim(),
        'no_hp': _hpController.text.trim(),
        'email': _emailController.text.trim(),
      };
    }

    // Call API untuk update profile
    final result = await ApiService.updateProfile(updateData);

    if (result['success']) {
      setState(() {
        _isLoadingSave = false;
        _isEditingProfil = false;
      });
      
      // Update callback
      widget.onProfilUpdated?.call(
        _namaController.text.trim(),
        _emailController.text.trim(),
      );
      
      _showSnackbar(result['message'] ?? 'Profil berhasil diperbarui!');
    } else {
      setState(() => _isLoadingSave = false);
      _showSnackbar(
        result['message'] ?? 'Gagal mengupdate profil',
        isError: true,
      );
    }
  }

  void _simpanPassword() async {
    setState(() {
      _errorPassword = null;
      _successPassword = null;
    });

    if (_passwordLamaController.text.isEmpty ||
        _passwordBaruController.text.isEmpty ||
        _passwordKonfirmasiController.text.isEmpty) {
      setState(() => _errorPassword = 'Semua field password harus diisi');
      return;
    }

    if (_passwordBaruController.text != _passwordKonfirmasiController.text) {
      setState(
        () => _errorPassword = 'Password baru dan konfirmasi tidak cocok',
      );
      return;
    }

    if (_passwordBaruController.text.length < 6) {
      setState(() => _errorPassword = 'Password minimal 6 karakter');
      return;
    }

    setState(() => _isLoadingPassword = true);
    HapticFeedback.mediumImpact();

    // Call API untuk update password
    final result = await ApiService.updatePassword(
      _passwordLamaController.text,
      _passwordBaruController.text,
    );

    if (result['success']) {
      setState(() {
        _isLoadingPassword = false;
        _successPassword = result['message'] ?? 'Password berhasil diubah!';
        _errorPassword = null;
        _passwordLamaController.clear();
        _passwordBaruController.clear();
        _passwordKonfirmasiController.clear();
      });
      _showSnackbar(_successPassword ?? 'Password berhasil diubah!');
    } else {
      setState(() {
        _isLoadingPassword = false;
        _errorPassword = result['message'] ?? 'Gagal mengubah password';
        _successPassword = null;
      });
      _showSnackbar(
        _errorPassword ?? 'Gagal mengubah password',
        isError: true,
      );
    }
  }

  void _showSnackbar(String msg, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(
              isError
                  ? Icons.error_outline_rounded
                  : Icons.check_circle_rounded,
              color: Colors.white,
              size: 18,
            ),
            const SizedBox(width: 8),
            Text(
              msg,
              style: const TextStyle(
                fontWeight: FontWeight.w600,
                fontFamily: 'Nunito',
              ),
            ),
          ],
        ),
        backgroundColor: isError ? AppTheme.danger : AppTheme.success,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        margin: const EdgeInsets.all(16),
        duration: const Duration(seconds: 2),
      ),
    );
  }

  void _showFotoOptions() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (_) => Container(
        padding: const EdgeInsets.all(24),
        decoration: const BoxDecoration(
          color: AppTheme.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: AppTheme.textLight,
                borderRadius: BorderRadius.circular(4),
              ),
            ),
            const SizedBox(height: 20),
            const Text(
              'Ubah Foto Profil',
              style: TextStyle(
                color: AppTheme.textDark,
                fontSize: 16,
                fontWeight: FontWeight.w800,
              ),
            ),
            const SizedBox(height: 20),
            _bottomSheetOption(
              icon: Icons.camera_alt_rounded,
              label: 'Ambil dari Kamera',
              color: AppTheme.primary,
              onTap: () {
                Navigator.pop(context);
                _showSnackbar('Fitur kamera akan segera hadir!');
              },
            ),
            const SizedBox(height: 12),
            _bottomSheetOption(
              icon: Icons.photo_library_rounded,
              label: 'Pilih dari Galeri',
              color: const Color(0xFF6366F1),
              onTap: () {
                Navigator.pop(context);
                _showSnackbar('Fitur galeri akan segera hadir!');
              },
            ),
            const SizedBox(height: 12),
            _bottomSheetOption(
              icon: Icons.delete_outline_rounded,
              label: 'Hapus Foto',
              color: AppTheme.danger,
              onTap: () {
                Navigator.pop(context);
                _showSnackbar('Foto profil dihapus');
              },
            ),
            const SizedBox(height: 8),
          ],
        ),
      ),
    );
  }

  Widget _bottomSheetOption({
    required IconData icon,
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: color.withOpacity(0.08),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withOpacity(0.15)),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: color.withOpacity(0.12),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(width: 14),
            Text(
              label,
              style: TextStyle(
                color: color,
                fontSize: 14,
                fontWeight: FontWeight.w700,
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: SafeArea(
        child: FadeTransition(
          opacity: _fadeAnim,
          child: SlideTransition(
            position: _slideAnim,
            child: CustomScrollView(
              physics: const BouncingScrollPhysics(),
              slivers: [
                SliverToBoxAdapter(child: _buildHeader(context)),
                SliverToBoxAdapter(child: _buildAvatarSection()),
                SliverToBoxAdapter(child: _buildInfoAnak()),
                SliverToBoxAdapter(child: _buildProfilForm()),
                SliverToBoxAdapter(child: _buildPasswordForm()),
                SliverToBoxAdapter(child: _buildLogoutButton(context)),
                const SliverToBoxAdapter(child: SizedBox(height: 40)),
              ],
            ),
          ),
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
            onPressed: () => Navigator.pop(context),
            icon: const Icon(
              Icons.arrow_back_ios_new_rounded,
              color: AppTheme.textDark,
              size: 20,
            ),
          ),
          const SizedBox(width: 4),
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Profil Saya',
                style: TextStyle(
                  color: AppTheme.textDark,
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                ),
              ),
              Text(
                'Kelola informasi akun kamu',
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

  Widget _buildAvatarSection() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 32),
      decoration: BoxDecoration(
        color: AppTheme.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          Stack(
            children: [
              Container(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: AppTheme.primary, width: 3),
                  boxShadow: AppTheme.softShadow,
                ),
                child: CircleAvatar(
                  radius: 52,
                  backgroundColor: const Color(0xFFFFEDE0),
                  child: Text(
                    _getInitials(_namaController.text),
                    style: TextStyle(
                      color: AppTheme.primary,
                      fontSize: 28,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
              ),
              Positioned(
                bottom: 0,
                right: 0,
                child: GestureDetector(
                  onTap: _showFotoOptions,
                  child: Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      gradient: AppTheme.primaryGradient,
                      shape: BoxShape.circle,
                      border: Border.all(color: AppTheme.white, width: 2),
                      boxShadow: AppTheme.softShadow,
                    ),
                    child: const Icon(
                      Icons.camera_alt_rounded,
                      color: Colors.white,
                      size: 16,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Text(
            _namaController.text,
            style: const TextStyle(
              color: AppTheme.textDark,
              fontSize: 20,
              fontWeight: FontWeight.w900,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            _emailController.text,
            style: const TextStyle(
              color: AppTheme.textMedium,
              fontSize: 13,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
            decoration: BoxDecoration(
              color: const Color(0xFFFFEDE0),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              '👨‍� Murid TK Mutiara',
              style: TextStyle(
                color: AppTheme.primary,
                fontSize: 12,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoAnak() {
    return Container(
      margin: const EdgeInsets.fromLTRB(20, 20, 20, 0),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: AppTheme.primaryGradient,
        borderRadius: BorderRadius.circular(20),
        boxShadow: AppTheme.softShadow,
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(14),
            ),
            child: const Icon(
              Icons.child_care_rounded,
              color: Colors.white,
              size: 26,
            ),
          ),
          const SizedBox(width: 14),
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Bintang Mutiara',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                ),
              ),
              Text(
                'Kelas A · TK Mutiara',
                style: TextStyle(
                  color: Colors.white70,
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
          const Spacer(),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Text(
              'Aktif',
              style: TextStyle(
                color: Colors.white,
                fontSize: 11,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildProfilForm() {
    return Container(
      margin: const EdgeInsets.fromLTRB(20, 20, 20, 0),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: AppTheme.cardShadowList,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Informasi Pribadi',
                style: TextStyle(
                  color: AppTheme.textDark,
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                ),
              ),
              GestureDetector(
                onTap: () {
                  if (_isEditingProfil) {
                    _simpanProfil();
                  } else {
                    setState(() => _isEditingProfil = true);
                    HapticFeedback.selectionClick();
                  }
                },
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  padding: const EdgeInsets.symmetric(
                    horizontal: 14,
                    vertical: 7,
                  ),
                  decoration: BoxDecoration(
                    color: _isEditingProfil
                        ? AppTheme.primary
                        : const Color(0xFFFFEDE0),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: _isLoadingSave
                      ? const SizedBox(
                          width: 16,
                          height: 16,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              _isEditingProfil
                                  ? Icons.check_rounded
                                  : Icons.edit_rounded,
                              color: _isEditingProfil
                                  ? Colors.white
                                  : AppTheme.primary,
                              size: 14,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              _isEditingProfil ? 'Simpan' : 'Edit',
                              style: TextStyle(
                                color: _isEditingProfil
                                    ? Colors.white
                                    : AppTheme.primary,
                                fontSize: 12,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          _buildFormField(
            label: 'Nama Lengkap',
            controller: _namaController,
            icon: Icons.person_rounded,
            enabled: _isEditingProfil,
          ),
          const SizedBox(height: 16),
          _buildFormField(
            label: 'Email',
            controller: _emailController,
            icon: Icons.email_rounded,
            enabled: _isEditingProfil,
            keyboardType: TextInputType.emailAddress,
          ),
          const SizedBox(height: 16),
          _buildFormField(
            label: 'No. HP',
            controller: _hpController,
            icon: Icons.phone_rounded,
            enabled: _isEditingProfil,
            keyboardType: TextInputType.phone,
          ),
          if (_isEditingProfil) ...[
            const SizedBox(height: 12),
            GestureDetector(
              onTap: () => setState(() => _isEditingProfil = false),
              child: Center(
                child: Text(
                  'Batal',
                  style: TextStyle(
                    color: AppTheme.textMedium,
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildPasswordForm() {
    return Container(
      margin: const EdgeInsets.fromLTRB(20, 16, 20, 0),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: AppTheme.cardShadowList,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFEDE0),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(
                  Icons.lock_rounded,
                  color: AppTheme.primary,
                  size: 18,
                ),
              ),
              const SizedBox(width: 10),
              const Text(
                'Ubah Password',
                style: TextStyle(
                  color: AppTheme.textDark,
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          _buildPasswordField(
            label: 'Password Lama',
            controller: _passwordLamaController,
            obscure: _obscureLama,
            onToggle: () => setState(() => _obscureLama = !_obscureLama),
          ),
          const SizedBox(height: 14),
          _buildPasswordField(
            label: 'Password Baru',
            controller: _passwordBaruController,
            obscure: _obscureBaru,
            onToggle: () => setState(() => _obscureBaru = !_obscureBaru),
          ),
          const SizedBox(height: 14),
          _buildPasswordField(
            label: 'Konfirmasi Password Baru',
            controller: _passwordKonfirmasiController,
            obscure: _obscureKonfirmasi,
            onToggle: () =>
                setState(() => _obscureKonfirmasi = !_obscureKonfirmasi),
          ),
          const SizedBox(height: 16),

          // Error / Success
          if (_errorPassword != null)
            Container(
              padding: const EdgeInsets.all(12),
              margin: const EdgeInsets.only(bottom: 12),
              decoration: BoxDecoration(
                color: const Color(0xFFFEE2E2),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.error_outline_rounded,
                    color: AppTheme.danger,
                    size: 16,
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      _errorPassword!,
                      style: const TextStyle(
                        color: AppTheme.danger,
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ],
              ),
            ),

          if (_successPassword != null)
            Container(
              padding: const EdgeInsets.all(12),
              margin: const EdgeInsets.only(bottom: 12),
              decoration: BoxDecoration(
                color: const Color(0xFFDCFCE7),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.check_circle_rounded,
                    color: AppTheme.success,
                    size: 16,
                  ),
                  const SizedBox(width: 8),
                  Text(
                    _successPassword!,
                    style: const TextStyle(
                      color: AppTheme.success,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),

          SizedBox(
            width: double.infinity,
            height: 48,
            child: ElevatedButton(
              onPressed: _isLoadingPassword ? null : _simpanPassword,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primary,
                disabledBackgroundColor: AppTheme.primary.withOpacity(0.5),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(14),
                ),
                elevation: 0,
              ),
              child: _isLoadingPassword
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(
                        color: Colors.white,
                        strokeWidth: 2,
                      ),
                    )
                  : const Text(
                      'Ubah Password',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLogoutButton(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(20, 16, 20, 0),
      child: GestureDetector(
        onTap: () => _showLogoutDialog(context),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: const Color(0xFFFEE2E2),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: AppTheme.danger.withOpacity(0.2)),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.logout_rounded, color: AppTheme.danger, size: 20),
              const SizedBox(width: 8),
              Text(
                'Keluar dari Akun',
                style: TextStyle(
                  color: AppTheme.danger,
                  fontSize: 14,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text(
          'Keluar dari Akun',
          style: TextStyle(
            color: AppTheme.textDark,
            fontWeight: FontWeight.w800,
          ),
        ),
        content: const Text(
          'Apakah kamu yakin ingin keluar dari akun ini?',
          style: TextStyle(color: AppTheme.textMedium, fontSize: 14),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text(
              'Batal',
              style: TextStyle(
                color: AppTheme.textMedium,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          ElevatedButton(
            onPressed: () {
              // ApiService.logout();
              Navigator.of(context).pushAndRemoveUntil(
                MaterialPageRoute(
                  builder: (_) =>
                      // Ganti dengan LoginScreen() kalau sudah aktif
                      const Scaffold(
                        body: Center(child: Text('Logout berhasil')),
                      ),
                ),
                (route) => false,
              );
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.danger,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              elevation: 0,
            ),
            child: const Text(
              'Keluar',
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFormField({
    required String label,
    required TextEditingController controller,
    required IconData icon,
    bool enabled = true,
    TextInputType? keyboardType,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            color: AppTheme.textMedium,
            fontSize: 12,
            fontWeight: FontWeight.w600,
          ),
        ),
        const SizedBox(height: 6),
        AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          decoration: BoxDecoration(
            color: enabled ? AppTheme.white : AppTheme.background,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: enabled
                  ? AppTheme.primary.withOpacity(0.4)
                  : Colors.transparent,
              width: 1.5,
            ),
            boxShadow: enabled ? AppTheme.softShadow : [],
          ),
          child: TextField(
            controller: controller,
            enabled: enabled,
            keyboardType: keyboardType,
            style: TextStyle(
              color: enabled ? AppTheme.textDark : AppTheme.textMedium,
              fontSize: 14,
              fontWeight: FontWeight.w600,
            ),
            decoration: InputDecoration(
              prefixIcon: Icon(
                icon,
                color: enabled ? AppTheme.primary : AppTheme.textLight,
                size: 18,
              ),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 14,
                vertical: 14,
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPasswordField({
    required String label,
    required TextEditingController controller,
    required bool obscure,
    required VoidCallback onToggle,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            color: AppTheme.textMedium,
            fontSize: 12,
            fontWeight: FontWeight.w600,
          ),
        ),
        const SizedBox(height: 6),
        Container(
          decoration: BoxDecoration(
            color: AppTheme.background,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: AppTheme.primary.withOpacity(0.2),
              width: 1.5,
            ),
          ),
          child: TextField(
            controller: controller,
            obscureText: obscure,
            style: const TextStyle(
              color: AppTheme.textDark,
              fontSize: 14,
              fontWeight: FontWeight.w600,
            ),
            decoration: InputDecoration(
              prefixIcon: Icon(
                Icons.lock_rounded,
                color: AppTheme.primary,
                size: 18,
              ),
              suffixIcon: IconButton(
                icon: Icon(
                  obscure
                      ? Icons.visibility_off_rounded
                      : Icons.visibility_rounded,
                  color: AppTheme.textLight,
                  size: 18,
                ),
                onPressed: onToggle,
              ),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 14,
                vertical: 14,
              ),
            ),
          ),
        ),
      ],
    );
  }

  String _getInitials(String name) {
    final parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }
    return name.isNotEmpty ? name[0].toUpperCase() : '?';
  }
}
