import '../../catalog/data/catalog_models.dart';

class CartItem {
  const CartItem({
    required this.id,
    required this.quantity,
    required this.unitPrice,
    required this.lineTotal,
    required this.product,
  });

  final int id;
  final int quantity;
  final double unitPrice;
  final double lineTotal;
  final Product product;

  factory CartItem.fromJson(Map<String, dynamic> json) {
    return CartItem(
      id: (json['id'] as num).toInt(),
      quantity: (json['quantity'] as num).toInt(),
      unitPrice: double.tryParse('${json['unit_price'] ?? 0}') ?? 0,
      lineTotal: double.tryParse('${json['line_total'] ?? 0}') ?? 0,
      product: Product.fromJson(json['product'] as Map<String, dynamic>),
    );
  }
}

class Cart {
  const Cart({
    required this.items,
    required this.itemsCount,
    required this.subtotal,
  });

  final List<CartItem> items;
  final int itemsCount;
  final double subtotal;

  factory Cart.empty() => const Cart(items: [], itemsCount: 0, subtotal: 0);

  factory Cart.fromJson(Map<String, dynamic> json) {
    return Cart(
      items: (json['items'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(CartItem.fromJson)
          .toList(),
      itemsCount: (json['items_count'] as num?)?.toInt() ?? 0,
      subtotal: double.tryParse('${json['subtotal'] ?? 0}') ?? 0,
    );
  }
}
