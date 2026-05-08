import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../network/api_client.dart';
import 'app_preferences.dart';

final currencyRepositoryProvider = Provider<CurrencyRepository>((ref) {
  return CurrencyRepository(ref.watch(dioProvider));
});

final currenciesProvider = FutureProvider<List<NovaCurrency>>((ref) {
  return ref.watch(currencyRepositoryProvider).currencies();
});

final selectedCurrencyProvider = Provider<NovaCurrency>((ref) {
  final preferences = ref.watch(appPreferencesProvider).asData?.value;
  final currencies = ref.watch(currenciesProvider).asData?.value ?? const <NovaCurrency>[];
  return NovaCurrency.resolve(currencies, preferences?.currencyCode);
});

class CurrencyRepository {
  CurrencyRepository(this._dio);

  final Dio _dio;

  Future<List<NovaCurrency>> currencies() async {
    final response = await _dio.get<dynamic>(
      '/currencies',
      queryParameters: {'_ts': DateTime.now().millisecondsSinceEpoch},
      options: Options(headers: {'Cache-Control': 'no-cache'}),
    );
    final data = apiData<List<dynamic>>(response);
    final currencies = data.whereType<Map<String, dynamic>>().map(NovaCurrency.fromJson).toList();
    return currencies.isEmpty ? [NovaCurrency.sarFallback] : currencies;
  }
}

class NovaCurrency {
  const NovaCurrency({
    required this.code,
    required this.name,
    required this.symbol,
    required this.exchangeRate,
    required this.isDefault,
    required this.decimalDigits,
    required this.symbolBefore,
  });

  final String code;
  final String name;
  final String symbol;
  final double exchangeRate;
  final bool isDefault;
  final int decimalDigits;
  final bool symbolBefore;

  static const sarFallback = NovaCurrency(
    code: 'SAR',
    name: 'ريال سعودي',
    symbol: 'ر.س',
    exchangeRate: 1,
    isDefault: true,
    decimalDigits: 2,
    symbolBefore: false,
  );

  double convertFromBase(double value, NovaCurrency base) {
    if (exchangeRate <= 0) return value;
    if (base.exchangeRate <= 0) return value;
    return value * (base.exchangeRate / exchangeRate);
  }

  factory NovaCurrency.fromJson(Map<String, dynamic> json) {
    return NovaCurrency(
      code: '${json['code'] ?? 'SAR'}',
      name: '${json['name_ar'] ?? json['name_en'] ?? json['code'] ?? ''}',
      symbol: '${json['symbol_ar'] ?? json['symbol_en'] ?? json['code'] ?? ''}',
      exchangeRate: double.tryParse('${json['exchange_rate'] ?? 1}') ?? 1,
      isDefault: json['is_default'] == true,
      decimalDigits: (json['decimal_places'] as num?)?.toInt() ?? 2,
      symbolBefore: '${json['symbol_position'] ?? 'after'}' == 'before',
    );
  }

  static NovaCurrency resolve(List<NovaCurrency> currencies, String? code) {
    if (currencies.isEmpty) return sarFallback;
    if (code != null) {
      for (final currency in currencies) {
        if (currency.code == code) return currency;
      }
    }
    return currencies.firstWhere((currency) => currency.isDefault, orElse: () => currencies.first);
  }

  static NovaCurrency baseCurrency(List<NovaCurrency> currencies) {
    if (currencies.isEmpty) return sarFallback;
    return currencies.firstWhere((currency) => currency.isDefault, orElse: () => currencies.first);
  }
}
