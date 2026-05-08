import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

final appPreferencesProvider = AsyncNotifierProvider<AppPreferencesController, AppPreferences>(
  AppPreferencesController.new,
);

class AppPreferences {
  const AppPreferences({
    required this.themeMode,
    required this.currencyCode,
  });

  final ThemeMode themeMode;
  final String currencyCode;

  AppPreferences copyWith({
    ThemeMode? themeMode,
    String? currencyCode,
  }) {
    return AppPreferences(
      themeMode: themeMode ?? this.themeMode,
      currencyCode: currencyCode ?? this.currencyCode,
    );
  }
}

class AppPreferencesController extends AsyncNotifier<AppPreferences> {
  static const _themeModeKey = 'nova_theme_mode';
  static const _currencyKey = 'nova_currency';

  @override
  Future<AppPreferences> build() async {
    final prefs = await SharedPreferences.getInstance();
    return AppPreferences(
      themeMode: _themeModeFromName(prefs.getString(_themeModeKey)),
      currencyCode: prefs.getString(_currencyKey) ?? 'SAR',
    );
  }

  Future<void> setThemeMode(ThemeMode mode) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_themeModeKey, mode.name);
    final current = state.asData?.value ?? const AppPreferences(themeMode: ThemeMode.system, currencyCode: 'SAR');
    state = AsyncData(current.copyWith(themeMode: mode));
  }

  Future<void> setCurrencyCode(String code) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_currencyKey, code);
    final current = state.asData?.value ?? const AppPreferences(themeMode: ThemeMode.system, currencyCode: 'SAR');
    state = AsyncData(current.copyWith(currencyCode: code));
  }

  ThemeMode _themeModeFromName(String? name) {
    return ThemeMode.values.firstWhere(
      (mode) => mode.name == name,
      orElse: () => ThemeMode.system,
    );
  }
}
