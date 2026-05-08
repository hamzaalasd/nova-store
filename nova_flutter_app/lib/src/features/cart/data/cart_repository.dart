import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/api_client.dart';
import 'cart_models.dart';

final cartRepositoryProvider = Provider<CartRepository>((ref) {
  return CartRepository(ref.watch(dioProvider));
});

final cartProvider = AsyncNotifierProvider<CartController, Cart>(CartController.new);

class CartRepository {
  CartRepository(this._dio);

  final Dio _dio;

  Future<Cart> getCart() async {
    final response = await _dio.get<dynamic>('/cart');
    return Cart.fromJson(apiData<Map<String, dynamic>>(response));
  }

  Future<Cart> add(int productId, {int quantity = 1, int? variantId}) async {
    final payload = <String, Object?>{
      'product_id': productId,
      'quantity': quantity,
    };
    if (variantId != null) {
      payload['product_variant_id'] = variantId;
    }

    final response = await _dio.post<dynamic>(
      '/cart/add',
      data: payload,
    );
    return Cart.fromJson(apiData<Map<String, dynamic>>(response));
  }

  Future<Cart> update(int itemId, int quantity) async {
    final response = await _dio.put<dynamic>('/cart/$itemId', data: {'quantity': quantity});
    return Cart.fromJson(apiData<Map<String, dynamic>>(response));
  }

  Future<Cart> remove(int itemId) async {
    final response = await _dio.delete<dynamic>('/cart/$itemId');
    return Cart.fromJson(apiData<Map<String, dynamic>>(response));
  }
}

class CartController extends AsyncNotifier<Cart> {
  @override
  Future<Cart> build() => ref.watch(cartRepositoryProvider).getCart();

  Future<void> add(int productId, {int quantity = 1, int? variantId}) async {
    final previous = state.asData?.value ?? Cart.empty();
    state = AsyncData(previous);
    state = await AsyncValue.guard(
      () => ref.read(cartRepositoryProvider).add(productId, quantity: quantity, variantId: variantId),
    );
  }

  Future<void> updateQuantity(int itemId, int quantity) async {
    state = await AsyncValue.guard(() => ref.read(cartRepositoryProvider).update(itemId, quantity));
  }

  Future<void> remove(int itemId) async {
    state = await AsyncValue.guard(() => ref.read(cartRepositoryProvider).remove(itemId));
  }
}
