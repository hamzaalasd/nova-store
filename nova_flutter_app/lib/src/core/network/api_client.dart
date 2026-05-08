import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../config/app_config.dart';
import '../storage/token_store.dart';

final dioProvider = Provider<Dio>((ref) {
  final tokenStore = ref.watch(tokenStoreProvider);
  final dio = Dio(
    BaseOptions(
      baseUrl: AppConfig.apiBaseUrl,
      connectTimeout: const Duration(seconds: 12),
      receiveTimeout: const Duration(seconds: 20),
      headers: {'Accept': 'application/json'},
    ),
  );

  dio.interceptors.add(
    InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await tokenStore.readToken();
        final cartSession = await tokenStore.cartSession();
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        options.headers['X-Cart-Session'] = cartSession;
        handler.next(options);
      },
    ),
  );

  return dio;
});

class ApiException implements Exception {
  ApiException(this.message);

  final String message;

  @override
  String toString() => message;
}

T apiData<T>(Response<dynamic> response) {
  final body = response.data;
  if (body is Map<String, dynamic>) {
    if (body['success'] == false) {
      throw ApiException('${body['message'] ?? 'حدث خطأ غير متوقع'}');
    }
    return body['data'] as T;
  }
  return body as T;
}

String readableApiError(Object error) {
  if (error is DioException) {
    final data = error.response?.data;
    if (data is Map<String, dynamic>) {
      if (data['message'] != null) return '${data['message']}';
      final errors = data['errors'];
      if (errors is Map && errors.isNotEmpty) {
        final first = errors.values.first;
        if (first is List && first.isNotEmpty) return '${first.first}';
      }
    }
    return 'تعذر الاتصال بالخادم. تحقق من تشغيل Laravel API.';
  }
  if (error is ApiException) return error.message;
  return 'حدث خطأ غير متوقع.';
}
