class NovaOrder {
  const NovaOrder({
    required this.id,
    required this.number,
    required this.total,
    required this.orderStatus,
    required this.paymentStatus,
    required this.currencyCode,
    this.customerName,
    this.placedAt,
    this.items = const [],
    this.payments = const [],
    this.shippingAddress,
  });

  final int id;
  final String number;
  final double total;
  final String orderStatus;
  final String paymentStatus;
  final String currencyCode;
  final String? customerName;
  final DateTime? placedAt;
  final List<NovaOrderItem> items;
  final List<NovaPayment> payments;
  final Map<String, dynamic>? shippingAddress;

  bool get isCancelled => const {'cancelled', 'returned', 'refunded'}.contains(orderStatus);

  int get trackingStep {
    return switch (orderStatus) {
      'pending_payment' || 'pending_bank_review' || 'confirmed' => 0,
      'processing' || 'ready_to_ship' => 1,
      'shipped' => 2,
      'delivered' => 3,
      _ => 0,
    };
  }

  String get orderStatusLabel {
    return switch (orderStatus) {
      'pending_payment' => 'بانتظار الدفع',
      'pending_bank_review' => 'مراجعة التحويل',
      'confirmed' => 'تم التأكيد',
      'processing' => 'قيد التجهيز',
      'ready_to_ship' => 'جاهز للشحن',
      'shipped' => 'في الطريق',
      'delivered' => 'تم التسليم',
      'cancelled' => 'ملغي',
      'returned' => 'مرتجع',
      'refunded' => 'مسترد',
      _ => orderStatus,
    };
  }

  String get paymentStatusLabel {
    return switch (paymentStatus) {
      'unpaid' => 'غير مدفوع',
      'initiated' => 'بدأ الدفع',
      'paid' => 'مدفوع',
      'failed' => 'فشل الدفع',
      'cancelled' => 'دفع ملغي',
      'refunded' => 'مسترد',
      'partially_refunded' => 'مسترد جزئيا',
      _ => paymentStatus,
    };
  }

  factory NovaOrder.fromJson(Map<String, dynamic> json) {
    return NovaOrder(
      id: (json['id'] as num).toInt(),
      number: '${json['order_number'] ?? ''}',
      total: double.tryParse('${json['total_base'] ?? 0}') ?? 0,
      orderStatus: '${json['order_status'] ?? ''}',
      paymentStatus: '${json['payment_status'] ?? ''}',
      currencyCode: '${json['currency_code'] ?? 'SAR'}',
      customerName: json['customer_name'] as String?,
      placedAt: DateTime.tryParse('${json['placed_at'] ?? ''}'),
      items: (json['items'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(NovaOrderItem.fromJson)
          .toList(),
      payments: (json['payments'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(NovaPayment.fromJson)
          .toList(),
      shippingAddress: json['shipping_address_snapshot'] is Map<String, dynamic>
          ? json['shipping_address_snapshot'] as Map<String, dynamic>
          : null,
    );
  }
}

class NovaOrderItem {
  const NovaOrderItem({
    required this.id,
    required this.name,
    required this.sku,
    required this.quantity,
    required this.total,
  });

  final int id;
  final String name;
  final String sku;
  final int quantity;
  final double total;

  factory NovaOrderItem.fromJson(Map<String, dynamic> json) {
    return NovaOrderItem(
      id: (json['id'] as num).toInt(),
      name: '${json['product_name_ar'] ?? json['product_name_en'] ?? ''}',
      sku: '${json['sku'] ?? ''}',
      quantity: (json['quantity'] as num?)?.toInt() ?? 0,
      total: double.tryParse('${json['total_base'] ?? 0}') ?? 0,
    );
  }
}

class NovaPayment {
  const NovaPayment({
    required this.id,
    required this.status,
    required this.gateway,
    required this.amount,
    this.paidAt,
  });

  final int id;
  final String status;
  final String gateway;
  final double amount;
  final DateTime? paidAt;

  factory NovaPayment.fromJson(Map<String, dynamic> json) {
    return NovaPayment(
      id: (json['id'] as num).toInt(),
      status: '${json['status'] ?? ''}',
      gateway: '${json['gateway'] ?? ''}',
      amount: double.tryParse('${json['amount_base'] ?? 0}') ?? 0,
      paidAt: DateTime.tryParse('${json['paid_at'] ?? ''}'),
    );
  }
}
