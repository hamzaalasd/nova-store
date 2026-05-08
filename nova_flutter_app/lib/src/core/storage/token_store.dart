import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:uuid/uuid.dart';

final tokenStoreProvider = Provider<TokenStore>((ref) => TokenStore());

class TokenStore {
  static const _tokenKey = 'nova_auth_token';
  static const _cartSessionKey = 'nova_cart_session';
  static const _onboardingDoneKey = 'nova_onboarding_done';

  Future<String?> readToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
  }

  Future<void> clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
  }

  Future<bool> hasCompletedOnboarding() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(_onboardingDoneKey) ?? false;
  }

  Future<void> completeOnboarding() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_onboardingDoneKey, true);
  }

  Future<String> cartSession() async {
    final prefs = await SharedPreferences.getInstance();
    final existing = prefs.getString(_cartSessionKey);
    if (existing != null && existing.isNotEmpty) return existing;
    final session = const Uuid().v4();
    await prefs.setString(_cartSessionKey, session);
    return session;
  }
}
