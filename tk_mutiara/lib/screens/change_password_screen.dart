import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../services/api_services.dart';

class ChangePasswordScreen extends StatefulWidget {
  const ChangePasswordScreen({super.key});

  @override
  State<ChangePasswordScreen> createState() => _ChangePasswordScreenState();
}

class _ChangePasswordScreenState extends State<ChangePasswordScreen> {
  final _usernameController = TextEditingController();
  final _oldPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  
  bool _isLoading = false;
  bool _obscureOld = true;
  bool _obscureNew = true;
  bool _obscureConfirm = true;
  String? _errorMsg;

  void _handleSubmit() async {
    if (_usernameController.text.isEmpty || 
        _oldPasswordController.text.isEmpty || 
        _newPasswordController.text.isEmpty || 
        _confirmPasswordController.text.isEmpty) {
      setState(() => _errorMsg = 'Semua field harus diisi');
      return;
    }

    if (_newPasswordController.text != _confirmPasswordController.text) {
      setState(() => _errorMsg = 'Konfirmasi password tidak cocok');
      return;
    }

    setState(() { _isLoading = true; _errorMsg = null; });

    final result = await ApiService.changePassword(
      username: _usernameController.text.trim(),
      oldPassword: _oldPasswordController.text.trim(),
      newPassword: _newPasswordController.text.trim(),
      confirmPassword: _confirmPasswordController.text.trim(),
    );

    if (!mounted) return;
    setState(() => _isLoading = false);

    if (result['success']) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message']),
          backgroundColor: AppTheme.success,
          behavior: SnackBarBehavior.floating,
        ),
      );
      Navigator.pop(context);
    } else {
      setState(() => _errorMsg = result['message']);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFFBF8),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppTheme.textDark),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text('Ubah Password', style: TextStyle(color: AppTheme.textDark, fontWeight: FontWeight.w800, fontSize: 18)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 20),
            const Text(
              'Perbarui keamanan akun Anda dengan mengganti password secara berkala.',
              style: TextStyle(fontSize: 14, color: AppTheme.textMedium, height: 1.5),
            ),
            const SizedBox(height: 32),

            if (_errorMsg != null) ...[
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF2F2),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: const Color(0xFFFECACA)),
                ),
                child: Row(children: [
                  const Icon(Icons.error_outline_rounded, size: 18, color: AppTheme.danger),
                  const SizedBox(width: 10),
                  Expanded(child: Text(_errorMsg!, style: const TextStyle(color: AppTheme.danger, fontSize: 12, fontWeight: FontWeight.w600))),
                ]),
              ),
              const SizedBox(height: 24),
            ],

            _buildLabel('Username'),
            _buildField(
              controller: _usernameController,
              hint: 'Masukkan username Anda',
              icon: Icons.person_outline_rounded,
            ),
            const SizedBox(height: 20),

            _buildLabel('Password Lama'),
            _buildField(
              controller: _oldPasswordController,
              hint: 'Masukkan password saat ini',
              icon: Icons.lock_outline_rounded,
              isPassword: true,
              obscure: _obscureOld,
              onToggle: () => setState(() => _obscureOld = !_obscureOld),
            ),
            const SizedBox(height: 20),

            _buildLabel('Password Baru'),
            _buildField(
              controller: _newPasswordController,
              hint: 'Masukkan password baru',
              icon: Icons.vpn_key_outlined,
              isPassword: true,
              obscure: _obscureNew,
              onToggle: () => setState(() => _obscureNew = !_obscureNew),
            ),
            const SizedBox(height: 20),

            _buildLabel('Konfirmasi Password Baru'),
            _buildField(
              controller: _confirmPasswordController,
              hint: 'Ulangi password baru',
              icon: Icons.check_circle_outline_rounded,
              isPassword: true,
              obscure: _obscureConfirm,
              onToggle: () => setState(() => _obscureConfirm = !_obscureConfirm),
            ),
            const SizedBox(height: 40),

            SizedBox(
              width: double.infinity,
              height: 54,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _handleSubmit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primary,
                  disabledBackgroundColor: AppTheme.primary.withOpacity(0.6),
                  foregroundColor: Colors.white,
                  elevation: 0,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: _isLoading
                    ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white))
                    : const Text('Simpan Perubahan', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
              ),
            ),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _buildLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(text, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppTheme.textDark)),
    );
  }

  Widget _buildField({
    required TextEditingController controller,
    required String hint,
    required IconData icon,
    bool isPassword = false,
    bool obscure = false,
    VoidCallback? onToggle,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFFF4F5F9),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE5E7EB)),
      ),
      child: TextField(
        controller: controller,
        obscureText: obscure,
        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppTheme.textDark),
        decoration: InputDecoration(
          hintText: hint,
          hintStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w400, color: Color(0xFFBFC3CE)),
          border: InputBorder.none,
          prefixIcon: Icon(icon, color: const Color(0xFFBFC3CE), size: 20),
          suffixIcon: isPassword
              ? IconButton(
                  icon: Icon(obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined, size: 20, color: const Color(0xFFBFC3CE)),
                  onPressed: onToggle,
                )
              : null,
          contentPadding: const EdgeInsets.symmetric(vertical: 16, horizontal: 16),
        ),
      ),
    );
  }
}
