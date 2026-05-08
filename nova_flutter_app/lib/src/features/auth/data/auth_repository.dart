import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/api_client.dart';
import '../../../core/storage/token_store.dart';
import 'auth_models.dart';

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepository(ref.watch(dioProvider), ref.watch(tokenStoreProvider));
});

final authStateProvider = AsyncNotifierProvider<AuthController, NovaUser?>(AuthController.new);

class AuthRepository {
  AuthRepository(this._dio, this._tokenStore);

  final Dio _dio;
  final TokenStore _tokenStore;

  Future<NovaUser?> profile() async {
    final token = await _tokenStore.readToken();
    if (token == null) return null;
    final response = await _dio.get<dynamic>('/profile');
    return NovaUser.fromJson(apiData<Map<String, dynamic>>(response));
  }

  Future<NovaUser> login({required String email, required String password}) async {
    final cartSession = await _tokenStore.cartSession();
    final response = await _dio.post<dynamic>(
      '/auth/login',
      data: {
        'email': email,
        'password': password,
        'cart_session': cartSession,
      },
    );
    final data = apiData<Map<String, dynamic>>(response);
    await _tokenStore.saveToken('${data['token']}');
    await _tokenStore.completeOnboarding();
    return NovaUser.fromJson(data['user'] as Map<String, dynamic>);
  }

  Future<NovaUser> register({
    required String name,
    required String email,
    required String password,
    String? phone,
  }) async {
    final cartSession = await _tokenStore.cartSession();
    final response = await _dio.post<dynamic>(
      '/auth/register',
      data: {
        'name': name,
        'email': email,
        'phone': phone,
        'password': password,
        'password_confirmation': password,
        'cart_session': cartSession,
      },
    );
    final data = apiData<Map<String, dynamic>>(response);
    await _tokenStore.saveToken('${data['token']}');
    await _tokenStore.completeOnboarding();
    return NovaUser.fromJson(data['user'] as Map<String, dynamic>);
  }

  Future<void> logout() async {
    try {
      await _dio.post<dynamic>('/auth/logout');
    } finally {
      await _tokenStore.clearToken();
    }
  }
}

class AuthController extends AsyncNotifier<NovaUser?> {
  @override
  Future<NovaUser?> build() => ref.watch(authRepositoryProvider).profile();

  Future<void> login(String email, String password) async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(() => ref.read(authRepositoryProvider).login(email: email, password: password));
  }

  Future<void> register(String name, String email, String password, String? phone) async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(
      () => ref.read(authRepositoryProvider).register(
            name: name,
            email: email,
            password: password,
            phone: phone,
          ),
    );
  }

  Future<void> logout() async {
    await ref.read(authRepositoryProvider).logout();
    state = const AsyncData(null);
  }
}
