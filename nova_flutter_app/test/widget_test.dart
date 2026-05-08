import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:nova_flutter_app/src/core/theme/nova_colors.dart';
import 'package:nova_flutter_app/src/core/widgets/nova_button.dart';

void main() {
  testWidgets('NOVA primary button renders in Arabic', (tester) async {
    await tester.pumpWidget(
      MaterialApp(
        home: Scaffold(
          body: Directionality(
            textDirection: TextDirection.rtl,
            child: Center(
              child: NovaButton(
                label: 'تسوق الآن',
                onPressed: () {},
              ),
            ),
          ),
        ),
      ),
    );

    expect(find.text('تسوق الآن'), findsOneWidget);
    expect(find.byType(NovaButton), findsOneWidget);
  });

  test('Nova signature palette is wired', () {
    expect(NovaColors.purple.toARGB32(), 0xFF3D1F6E);
    expect(NovaColors.gold.toARGB32(), 0xFFC9963A);
  });
}
