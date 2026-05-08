import 'package:flutter/material.dart';

import 'nova_colors.dart';

class NovaTheme {
  static ThemeData light() {
    final base = ThemeData(
      useMaterial3: true,
      fontFamily: 'Arial',
      colorScheme: ColorScheme.fromSeed(
        seedColor: NovaColors.purple,
        brightness: Brightness.light,
        primary: NovaColors.purple,
        secondary: NovaColors.gold,
        surface: NovaColors.cream,
      ),
      scaffoldBackgroundColor: NovaColors.cream,
    );

    return base.copyWith(
      appBarTheme: const AppBarTheme(
        backgroundColor: NovaColors.cream,
        foregroundColor: NovaColors.text,
        centerTitle: false,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
      ),
      cardTheme: CardThemeData(
        color: Colors.white,
        elevation: 0,
        margin: EdgeInsets.zero,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: NovaColors.border),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: NovaColors.border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: NovaColors.border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: NovaColors.gold, width: 1.4),
        ),
      ),
      textTheme: base.textTheme.apply(
        bodyColor: NovaColors.text,
        displayColor: NovaColors.text,
      ),
    );
  }

  static ThemeData dark() {
    final base = ThemeData(
      useMaterial3: true,
      fontFamily: 'Arial',
      colorScheme: const ColorScheme.dark(
        primary: NovaColors.violet,
        secondary: NovaColors.gold,
        surface: NovaColors.darkSurface,
        onSurface: NovaColors.cream,
      ),
      scaffoldBackgroundColor: NovaColors.deepNight,
    );

    return base.copyWith(
      appBarTheme: const AppBarTheme(
        backgroundColor: NovaColors.deepNight,
        foregroundColor: NovaColors.cream,
        centerTitle: false,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
      ),
      cardTheme: CardThemeData(
        color: NovaColors.darkSurface,
        elevation: 0,
        margin: EdgeInsets.zero,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: NovaColors.darkBorder),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: NovaColors.darkPurple,
        labelStyle: const TextStyle(color: NovaColors.darkMuted),
        hintStyle: const TextStyle(color: NovaColors.muted),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: NovaColors.darkBorder),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: NovaColors.darkBorder),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: NovaColors.gold, width: 1.4),
        ),
      ),
      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: NovaColors.darkPurple,
        indicatorColor: NovaColors.violet.withAlpha(80),
        labelTextStyle: WidgetStateProperty.resolveWith(
          (states) => TextStyle(
            color: states.contains(WidgetState.selected) ? NovaColors.goldLight : NovaColors.darkMuted,
            fontWeight: FontWeight.w800,
            fontSize: 12,
          ),
        ),
        iconTheme: WidgetStateProperty.resolveWith(
          (states) => IconThemeData(
            color: states.contains(WidgetState.selected) ? NovaColors.goldLight : NovaColors.darkMuted,
          ),
        ),
      ),
      textTheme: base.textTheme.apply(
        bodyColor: NovaColors.cream,
        displayColor: NovaColors.cream,
      ),
    );
  }
}
