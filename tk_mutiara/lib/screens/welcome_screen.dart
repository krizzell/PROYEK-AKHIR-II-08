import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../theme/app_theme.dart';
import 'login_screen.dart';

class WelcomeScreen extends StatefulWidget {
  const WelcomeScreen({super.key});

  @override
  State<WelcomeScreen> createState() => _WelcomeScreenState();
}

class _WelcomeScreenState extends State<WelcomeScreen> with TickerProviderStateMixin {
  late AnimationController _logoController;
  late AnimationController _contentController;
  late Animation<double> _logoScale;
  late Animation<double> _logoFade;
  late Animation<Offset> _bottomSlide;
  late Animation<double> _bottomFade;

  @override
  void initState() {
    super.initState();
    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
    ));

    _logoController = AnimationController(vsync: this, duration: const Duration(milliseconds: 900));
    _logoScale = Tween<double>(begin: 0.7, end: 1.0).animate(
      CurvedAnimation(parent: _logoController, curve: Curves.easeOutBack),
    );
    _logoFade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _logoController, curve: const Interval(0.0, 0.6, curve: Curves.easeOut)),
    );

    _contentController = AnimationController(vsync: this, duration: const Duration(milliseconds: 800));
    _bottomSlide = Tween<Offset>(begin: const Offset(0, 1), end: Offset.zero).animate(
      CurvedAnimation(parent: _contentController, curve: Curves.easeOutCubic),
    );
    _bottomFade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _contentController, curve: const Interval(0.2, 1.0, curve: Curves.easeOut)),
    );

    _logoController.forward().then((_) {
      Future.delayed(const Duration(milliseconds: 300), () {
        if (mounted) _contentController.forward();
      });
    });
  }

  @override
  void dispose() {
    _logoController.dispose();
    _contentController.dispose();
    super.dispose();
  }

  void _goToLogin() {
    Navigator.of(context).pushReplacement(
      PageRouteBuilder(
        pageBuilder: (context, animation, secondaryAnimation) => const LoginScreen(),
        transitionsBuilder: (context, animation, secondaryAnimation, child) {
          final curvedAnim = CurvedAnimation(parent: animation, curve: Curves.easeInOutCubic);
          return FadeTransition(
            opacity: Tween<double>(begin: 0.0, end: 1.0).animate(curvedAnim),
            child: SlideTransition(
              position: Tween<Offset>(begin: const Offset(0.08, 0), end: Offset.zero).animate(curvedAnim),
              child: child,
            ),
          );
        },
        transitionDuration: const Duration(milliseconds: 450),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    return Scaffold(
      backgroundColor: const Color(0xFFFFFBF8),
      body: Stack(
        children: [
          // Soft decorative shapes
          Positioned(top: -80, right: -80,
            child: Container(width: 220, height: 220,
              decoration: BoxDecoration(shape: BoxShape.circle,
                color: AppTheme.primary.withOpacity(0.06)))),
          Positioned(top: size.height * 0.12, left: -50,
            child: Container(width: 130, height: 130,
              decoration: BoxDecoration(shape: BoxShape.circle,
                color: AppTheme.primary.withOpacity(0.04)))),

          // Main content
          Column(
            children: [
              // Logo area — takes upper portion
              Expanded(
                flex: 55,
                child: Center(
                  child: FadeTransition(
                    opacity: _logoFade,
                    child: ScaleTransition(
                      scale: _logoScale,
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Image.asset('assets/images/logosekolah.png', width: 160, height: 160),
                          const SizedBox(height: 24),
                          const Text('TK Mutiara', style: TextStyle(
                            fontSize: 32, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: -0.5, height: 1,
                          )),
                          const SizedBox(height: 8),
                          Text('YAYASAN TK SWASTA MUTIARA BALIGE', style: TextStyle(
                            fontSize: 10, fontWeight: FontWeight.w800, color: AppTheme.primary.withOpacity(0.7), letterSpacing: 3,
                          )),
                        ],
                      ),
                    ),
                  ),
                ),
              ),

              // Bottom card
              FadeTransition(
                opacity: _bottomFade,
                child: SlideTransition(
                  position: _bottomSlide,
                  child: Container(
                    width: double.infinity,
                    padding: EdgeInsets.fromLTRB(28, 32, 28, MediaQuery.of(context).padding.bottom + 32),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        begin: Alignment.topLeft, end: Alignment.bottomRight,
                        colors: [Color(0xFFFF6B1A), Color(0xFFFF8840), Color(0xFFFFA05C)],
                        stops: [0.0, 0.6, 1.0],
                      ),
                      borderRadius: const BorderRadius.only(
                        topLeft: Radius.circular(32), topRight: Radius.circular(32),
                      ),
                      boxShadow: [BoxShadow(color: AppTheme.primary.withOpacity(0.2), blurRadius: 30, offset: const Offset(0, -8))],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Text('Selamat Datang!', style: TextStyle(
                          color: Colors.white, fontSize: 28, fontWeight: FontWeight.w900, letterSpacing: -0.5,
                        )),
                        const SizedBox(height: 8),
                        Text(
                          'Pantau perkembangan anak, informasi sekolah, dan pembayaran SPP dalam satu aplikasi.',
                          style: TextStyle(color: Colors.white.withOpacity(0.85), fontSize: 14, height: 1.6),
                        ),
                        const SizedBox(height: 24),
                        Row(
                          children: [
                            Expanded(
                              child: Text('Klik tombol panah untuk melanjutkan', style: TextStyle(
                                color: Colors.white.withOpacity(0.6), fontSize: 12, fontWeight: FontWeight.w500,
                              )),
                            ),
                            GestureDetector(
                              onTap: _goToLogin,
                              child: Container(
                                width: 56, height: 56,
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(18),
                                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 16, offset: const Offset(0, 4))],
                                ),
                                child: const Icon(Icons.arrow_forward_rounded, color: AppTheme.primary, size: 28),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
