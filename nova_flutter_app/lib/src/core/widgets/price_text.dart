import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../settings/currency_repository.dart';
import '../theme/nova_colors.dart';

class PriceText extends ConsumerWidget {
  const PriceText(this.value, {super.key, this.large = false});

  final double value;
  final bool large;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final currencies = ref.watch(currenciesProvider).asData?.value ?? const <NovaCurrency>[];
    final currency = ref.watch(selectedCurrencyProvider);
    final baseCurrency = NovaCurrency.baseCurrency(currencies);
    final converted = currency.convertFromBase(value, baseCurrency);
    final number = NumberFormat.decimalPatternDigits(
      locale: 'ar_SA',
      decimalDigits: currency.decimalDigits,
    ).format(converted);
    final formatted = currency.symbolBefore ? '${currency.symbol}$number' : '$number ${currency.symbol}';
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Text(
      formatted,
      style: TextStyle(
        color: isDark ? NovaColors.goldLight : NovaColors.text,
        fontWeight: FontWeight.w900,
        fontSize: large ? 24 : 15,
      ),
    );
  }
}
