import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/api_client.dart';
import 'order_models.dart';

final orderRepositoryProvider = Provider<OrderRepository>((ref) {
  return OrderRepository(ref.watch(dioProvider));
});

final ordersProvider = FutureProvider<List<NovaOrder>>((ref) {
  return ref.watch(orderRepositoryProvider).orders();
});

final orderProvider = FutureProvider.family<NovaOrder, int>((ref, id) {
  return ref.watch(orderRepositoryProvider).order(id);
});

class OrderRepository {
  OrderRepository(this._dio);

  final Dio _dio;

  Future<List<NovaOrder>> orders() async {
    final response = await _dio.get<dynamic>(
      '/orders',
      queryParameters: {
        'per_page': 50,
        '_ts': DateTime.now().millisecondsSinceEpoch,
      },
      options: Options(headers: {'Cache-Control': 'no-cache'}),
    );
    final data = apiData<List<dynamic>>(response);
    return data.whereType<Map<String, dynamic>>().map(NovaOrder.fromJson).toList();
  }

  Future<NovaOrder> order(int id) async {
    final response = await _dio.get<dynamic>(
      '/orders/$id',
      queryParameters: {'_ts': DateTime.now().millisecondsSinceEpoch},
      options: Options(headers: {'Cache-Control': 'no-cache'}),
    );
    return NovaOrder.fromJson(apiData<Map<String, dynamic>>(response));
  }

  Future<NovaOrder> create({
    required String name,
    required String email,
    required String phone,
    required String city,
    String? district,
    String? street,
  }) async {
    final response = await _dio.post<dynamic>(
      '/orders',
      data: {
        'customer_name': name,
        'customer_email': email,
        'customer_phone': phone,
        'shipping_address': {
          'city': city,
          'district': district,
          'street': street,
        },
      },
    );
    return NovaOrder.fromJson(apiData<Map<String, dynamic>>(response));
  }
}
