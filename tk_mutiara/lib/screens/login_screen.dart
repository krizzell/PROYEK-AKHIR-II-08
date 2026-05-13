import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../services/api_services.dart';
import 'main_navigation_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen>
    with SingleTickerProviderStateMixin {
  final _emailController = TextEditingController(
    text: 'andikapurba',
  );
  final _passwordController = TextEditingController(text: 'password123');
  bool _isLoading = false;
  bool _obscurePassword = true;
  String? _errorMsg;

  late AnimationController _animController;
  late Animation<double> _fadeAnim;
  late Animation<Offset> _slideAnim;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    );
    _fadeAnim = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _slideAnim = Tween<Offset>(begin: const Offset(0, 0.2), end: Offset.zero)
        .animate(
          CurvedAnimation(parent: _animController, curve: Curves.easeOutCubic),
        );
    _animController.forward();
  }

  @override
  void dispose() {
    _animController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  void _login() async {
    // Validasi input
    if (_emailController.text.isEmpty) {
      setState(() => _errorMsg = 'Username tidak boleh kosong');
      return;
    }
    if (_passwordController.text.isEmpty) {
      setState(() => _errorMsg = 'Password tidak boleh kosong');
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMsg = null;
    });

    try {
      final result = await ApiService.login(
        _emailController.text.trim(),
        _passwordController.text.trim(),
      );

      if (!mounted) return;
      
      setState(() => _isLoading = false);

      if (result['success'] == true) {
        // Login berhasil, navigate ke dashboard
        if (!mounted) return;
        Navigator.pushReplacement(
          context,
          PageRouteBuilder(
            pageBuilder: (_, __, ___) => const MainNavigationScreen(),
            transitionsBuilder: (_, anim, __, child) =>
                FadeTransition(opacity: anim, child: child),
            transitionDuration: const Duration(milliseconds: 500),
          ),
        );
      } else {
        // Login gagal, tampilkan error
        setState(() {
          _errorMsg = result['message'] ??
              'Login gagal. Cek username dan password Anda.';
        });
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _errorMsg = 'Terjadi kesalahan: $e';
      });
    }
  }

@override
Widget build(BuildContext context) {
  return Scaffold(
    backgroundColor: const Color.fromARGB(255, 240, 216, 194),
    body: SafeArea(
      child: Center(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 20),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                _buildLogoBaru(),
                const SizedBox(height: 50),
                _buildCardBaru(),
              ],
            ),
          ),
        ),
      ),
    ),
  );
}

 Widget _buildLogoBaru() {
  return Center(
    child: Image.asset(
      'assets/images/logosekolah.png',
      width: 120, // 
    ),
  );
}

Widget _buildCardBaru() {
  return Container(
    width: 300, // 
    padding: const EdgeInsets.all(14), 
    decoration: BoxDecoration(
      color: Colors.white,
      borderRadius: BorderRadius.circular(18),
    ),
    child: Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        const Text(
          'Login',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 4),
        const Text(
          'Masukkan username dan kata sandi',
          style: TextStyle(fontSize: 11, color: Color.fromARGB(255, 72, 72, 72)),
        ),

        const SizedBox(height: 12),

        const Align(
          alignment: Alignment.centerLeft,
          child: Text('Username', style: TextStyle(fontSize: 12)),
        ),
        const SizedBox(height: 4),
        _buildInputBaru(
          controller: _emailController,
          icon: Icons.badge,
          hint: 'username',
        ),

        const SizedBox(height: 8),

        const Align(
          alignment: Alignment.centerLeft,
          child: Text('Kata Sandi', style: TextStyle(fontSize: 12)),
        ),
        const SizedBox(height: 4),
        _buildInputBaru(
          controller: _passwordController,
          icon: Icons.lock,
          hint: 'Password',
          isPassword: true,
        ),

        const SizedBox(height: 15),

        SizedBox(
  width: double.infinity,
  height: 46,
  child: ElevatedButton(
    onPressed: _isLoading ? null : _login,
    style: ButtonStyle(
      backgroundColor: WidgetStateProperty.resolveWith<Color>(
        (states) {
          if (states.contains(WidgetState.disabled)) {
            return Colors.grey.shade400;
          }
          return AppTheme.primary;
        },
      ),
      elevation: WidgetStateProperty.all(2),
      shape: WidgetStateProperty.all(
        RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
        ),
      ),
    ),
    child: _isLoading
        ? const SizedBox(
            width: 18,
            height: 20,
            child: CircularProgressIndicator(
              strokeWidth: 2,
              color: Colors.white,
            ),
          )
        : const Text(
            'Login',
            style: TextStyle(fontSize: 14, color: Colors.white),
          ),
  ),
),

        const SizedBox(height: 15),

        const Text(
          'Ganti Password?',
          style: TextStyle(
            fontSize: 12,
            color:AppTheme.primary,
          ),
        ),

        const SizedBox(height: 10),

        _buildInfoBox(),
      ],
    ),
  );
}

Widget _buildInputBaru({
  required TextEditingController controller,
  required IconData icon,
  required String hint,
  bool isPassword = false,
}) {
  return Container(
    height: 42, // 
    decoration: BoxDecoration(
     color: const Color.fromARGB(255, 242, 242, 242),
      borderRadius: BorderRadius.circular(10),
    ),
    child: TextField(
      controller: controller,
      obscureText: isPassword && _obscurePassword,
      style: const TextStyle(fontSize: 14), 
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: const TextStyle(fontSize: 13),
        border: InputBorder.none,
        prefixIcon: Icon(icon, color: AppTheme.primary, size: 18), 
        suffixIcon: isPassword
            ? IconButton(
                icon: Icon(
                  _obscurePassword
                      ? Icons.visibility_off
                      : Icons.visibility,
                  size: 18,
                ),
                onPressed: () {
                  setState(() {
                    _obscurePassword = !_obscurePassword;
                  });
                },
              )
            : null,
        contentPadding: const EdgeInsets.symmetric(vertical: 8), 
      ),
    ),
  );
}

Widget _buildInfoBox() {
  return Container(
    margin: const EdgeInsets.only(top: 12),
    padding: const EdgeInsets.all(10),
    decoration: BoxDecoration(
      color: Color(0xFFFFE4C2),
      borderRadius: BorderRadius.circular(10),
    ),
    child: const Row(
      children: [
        Icon(Icons.info_outline, color: AppTheme.primary, size: 18),
        SizedBox(width: 6),
        Expanded(
          child: Text(
            'Gunakan username yang diberikan sekolah untuk dapat masuk ke aplikasi.',
            style: TextStyle(fontSize: 11),
          ),
        ),
      ],
    ),
  );
}
    }