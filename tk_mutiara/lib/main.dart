import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'theme/app_theme.dart';
import 'screens/login_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
    ),
  );
  runApp(const MutiaraApp());
}

class MutiaraApp extends StatelessWidget {
  const MutiaraApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'TK Mutiara',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: const LoginScreen(), // ini kalo mau langsung tampilan login
      // home: const MainNavigationScreen(), // uncomment ini kalau mau langsung ke main app
    );
  }
}
