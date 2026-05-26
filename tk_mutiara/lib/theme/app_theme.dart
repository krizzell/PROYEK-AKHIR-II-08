import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // COLOR PALETTE 
  static const Color primary = Color.fromARGB(255, 255, 107, 26);       // Vibrant orange
  static const Color primaryLight = Color.fromARGB(255, 255, 140, 66);  // Soft orange
  static const Color primaryDark = Color.fromARGB(255, 232, 80, 0);   // Deep orange
  static const Color accent = Color(0xFFFFD166);         // Golden yellow accent
  static const Color white = Color(0xFFFFFFFF);
  static const Color background = Color(0xFFFFF8F4);    // Warm white
  static const Color surface = Color(0xFFFFFFFF);
  static const Color textDark = Color(0xFF1A1A2E);
  static const Color textMedium = Color(0xFF6B7280);
  static const Color textLight = Color(0xFF9CA3AF);
  static const Color success = Color(0xFF22C55E);
  static const Color warning = Color(0xFFF59E0B);
  static const Color danger = Color(0xFFEF4444);
  static const Color cardShadow = Color.fromARGB(26, 255, 107, 26);

  // TYPOGRAPHY 
  static TextStyle get heading1 => GoogleFonts.montserrat(
    fontSize: 20,
    fontWeight: FontWeight.bold,
    color: textDark,
    letterSpacing: -0.5,
  );

  static TextStyle get heading2 => GoogleFonts.montserrat(
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: textDark,
    letterSpacing: -0.3,
  );

  static TextStyle get heading3 => GoogleFonts.montserrat(
    fontSize: 14,
    fontWeight: FontWeight.w500,
    color: textDark,
    letterSpacing: -0.2,
  );

  static TextStyle get heading4 => GoogleFonts.montserrat(
    fontSize: 12,
    fontWeight: FontWeight.w500,
    color: textDark,
  );

  static TextStyle get bodyLarge => GoogleFonts.montserrat(
    fontSize: 16,
    fontWeight: FontWeight.w500,
    color: textDark,
  );

  static TextStyle get bodyMedium => GoogleFonts.montserrat(
    fontSize: 14,
    fontWeight: FontWeight.w500,
    color: textMedium,
  );

  static TextStyle get bodySmall => GoogleFonts.montserrat(
    fontSize: 12,
    fontWeight: FontWeight.w400,
    color: textLight,
  );

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      fontFamily: GoogleFonts.montserrat().fontFamily,
      colorScheme: ColorScheme.light(
        primary: primary,
        secondary: primaryLight,
        surface: surface,
        background: background,
        onPrimary: white,
        onSecondary: white,
        onSurface: textDark,
        onBackground: textDark,
      ),
      scaffoldBackgroundColor: background,
      appBarTheme: AppBarTheme(
        backgroundColor: Colors.transparent,
        elevation: 0,
        scrolledUnderElevation: 0,
        centerTitle: false,
        iconTheme: const IconThemeData(color: textDark),
        titleTextStyle: GoogleFonts.montserrat(
          color: textDark,
          fontSize: 20,
          fontWeight: FontWeight.bold,
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primary,
          foregroundColor: white,
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          textStyle: GoogleFonts.montserrat(
            fontWeight: FontWeight.w700,
            fontSize: 16,
          ),
        ),
      ),
      cardTheme: CardThemeData(
        color: surface,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
      ),
    );
  }

  // GRADIENT HELPERS 
  static LinearGradient get primaryGradient => const LinearGradient(
        colors: [primary, primaryLight],
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
      );

  static LinearGradient get warmGradient => const LinearGradient(
        colors: [Color.fromARGB(255, 255, 107, 26), Color(0xFFFFD166)],
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
      );

  static LinearGradient get cardGradient => const LinearGradient(
        colors: [Color(0xFFFFF8F4), Color(0xFFFFEDE0)],
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
      );

  static List<BoxShadow> get softShadow => [
        BoxShadow(
          color: primary.withOpacity(0.15),
          blurRadius: 20,
          offset: const Offset(0, 8),
          spreadRadius: 0,
        ),
      ];

  static List<BoxShadow> get cardShadowList => [
        BoxShadow(
          color: Colors.black.withOpacity(0.06),
          blurRadius: 16,
          offset: const Offset(0, 4),
          spreadRadius: 0,
        ),
      ];
}