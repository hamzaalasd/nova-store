import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/storage/token_store.dart';
import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/back_to_home_guard.dart';
import '../../../core/widgets/nova_brand_mark.dart';

class OnboardingScreen extends ConsumerStatefulWidget {
  const OnboardingScreen({super.key});

  @override
  ConsumerState<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends ConsumerState<OnboardingScreen> {
  final _controller = PageController();
  int _index = 0;

  static const _slides = [
    _OnboardingSlideData(
      kind: _ArtKind.products,
      title: 'اكتشف أفضل المنتجات بضغطة واحدة',
      body: 'منتجات تقنية منتقاة بعناية داخل متجر واحد أنيق وسريع.',
    ),
    _OnboardingSlideData(
      kind: _ArtKind.luxury,
      title: 'تجربة تسوق فاخرة لا مثيل لها',
      body: 'واجهة راقية، أسعار واضحة، وعروض تظهر لك بدون ازدحام.',
    ),
    _OnboardingSlideData(
      kind: _ArtKind.delivery,
      title: 'تتبع طلبك لحظة بلحظة',
      body: 'من تأكيد الطلب إلى التسليم، كل خطوة واضحة داخل حسابك.',
    ),
  ];

  Future<void> _finish(String path) async {
    await ref.read(tokenStoreProvider).completeOnboarding();
    if (!mounted) return;
    context.go(path);
  }

  void _next() {
    if (_index < _slides.length - 1) {
      _controller.nextPage(duration: const Duration(milliseconds: 360), curve: Curves.easeOutCubic);
      return;
    }
    _finish('/login');
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isLast = _index == _slides.length - 1;

    return BackToHomeGuard(
      child: Scaffold(
        backgroundColor: NovaColors.deepNight,
        body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(18, 12, 18, 0),
              child: Row(
                children: [
                  const _MiniLogo(),
                  const Spacer(),
                  TextButton(
                    onPressed: () => _finish('/'),
                    child: const Text('تخطي', style: TextStyle(color: NovaColors.muted)),
                  ),
                ],
              ),
            ),
            Expanded(
              child: PageView.builder(
                controller: _controller,
                itemCount: _slides.length,
                onPageChanged: (value) => setState(() => _index = value),
                itemBuilder: (context, index) => _Slide(data: _slides[index]),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(22, 0, 22, 24),
              child: Column(
                children: [
                  _Dots(count: _slides.length, active: _index),
                  const SizedBox(height: 18),
                  SizedBox(
                    width: double.infinity,
                    height: 54,
                    child: FilledButton.icon(
                      onPressed: _next,
                      style: FilledButton.styleFrom(
                        backgroundColor: isLast ? NovaColors.gold : NovaColors.violet,
                        foregroundColor: isLast ? NovaColors.deepNight : NovaColors.cream,
                        shape: const StadiumBorder(),
                      ),
                      icon: Icon(isLast ? Icons.auto_awesome : Icons.arrow_back_rounded),
                      label: Text(isLast ? 'ابدأ الآن' : 'التالي', style: const TextStyle(fontWeight: FontWeight.w900)),
                    ),
                  ),
                  const SizedBox(height: 10),
                  TextButton.icon(
                    onPressed: () => _finish('/'),
                    icon: const Icon(Icons.storefront_outlined, size: 18),
                    label: const Text('المتابعة كزائر'),
                    style: TextButton.styleFrom(foregroundColor: NovaColors.darkMuted),
                  ),
                ],
              ),
            ),
          ],
        ),
        ),
      ),
    );
  }
}

enum _ArtKind { products, luxury, delivery }

class _OnboardingSlideData {
  const _OnboardingSlideData({
    required this.kind,
    required this.title,
    required this.body,
  });

  final _ArtKind kind;
  final String title;
  final String body;
}

class _Slide extends StatelessWidget {
  const _Slide({required this.data});

  final _OnboardingSlideData data;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 26, 24, 10),
      child: Column(
        children: [
          Expanded(
            child: Center(
              child: AnimatedSwitcher(
                duration: const Duration(milliseconds: 280),
                child: _OnboardingArt(key: ValueKey(data.kind), kind: data.kind),
              ),
            ),
          ),
          Text(
            data.title,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: NovaColors.cream,
              fontSize: 27,
              fontWeight: FontWeight.w900,
              height: 1.28,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            data.body,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: NovaColors.muted,
              fontSize: 15,
              height: 1.75,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }
}

class _OnboardingArt extends StatelessWidget {
  const _OnboardingArt({super.key, required this.kind});

  final _ArtKind kind;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 240,
      height: 230,
      child: Stack(
        alignment: Alignment.center,
        children: [
          Container(
            width: 190,
            height: 190,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: NovaColors.violet.withAlpha(26),
              border: Border.all(color: NovaColors.violet.withAlpha(88)),
            ),
          ),
          if (kind == _ArtKind.products) const _ProductsArt(),
          if (kind == _ArtKind.luxury) const _LuxuryArt(),
          if (kind == _ArtKind.delivery) const _DeliveryArt(),
        ],
      ),
    );
  }
}

class _ProductsArt extends StatelessWidget {
  const _ProductsArt();

  @override
  Widget build(BuildContext context) {
    return Stack(
      alignment: Alignment.center,
      children: [
        Container(
          width: 92,
          height: 92,
          decoration: BoxDecoration(color: NovaColors.violet, borderRadius: BorderRadius.circular(24)),
          child: const Icon(Icons.shopping_bag_outlined, color: NovaColors.cream, size: 46),
        ),
        Positioned(
          bottom: 38,
          right: 34,
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 7),
            decoration: BoxDecoration(color: NovaColors.gold, borderRadius: BorderRadius.circular(12)),
            child: const Text('500+ منتج', style: TextStyle(color: NovaColors.deepNight, fontWeight: FontWeight.w900, fontSize: 12)),
          ),
        ),
      ],
    );
  }
}

class _LuxuryArt extends StatelessWidget {
  const _LuxuryArt();

  @override
  Widget build(BuildContext context) {
    return Stack(
      alignment: Alignment.center,
      children: [
        Transform.rotate(
          angle: -.12,
          child: Container(width: 118, height: 150, decoration: _cardDecoration(NovaColors.darkPurple)),
        ),
        Transform.translate(
          offset: const Offset(-12, -8),
          child: Container(width: 118, height: 150, decoration: _cardDecoration(NovaColors.violet)),
        ),
        Positioned(
          top: 62,
          child: Container(
            width: 82,
            height: 64,
            decoration: BoxDecoration(color: NovaColors.gold.withAlpha(45), borderRadius: BorderRadius.circular(14)),
            child: const Icon(Icons.watch_outlined, color: NovaColors.gold, size: 34),
          ),
        ),
        const Positioned(bottom: 58, child: Text('منتج مميز', style: TextStyle(color: NovaColors.cream, fontWeight: FontWeight.w900))),
        const Positioned(bottom: 38, child: Text('749 ر.س', style: TextStyle(color: NovaColors.gold, fontWeight: FontWeight.w900))),
        Positioned(
          top: 36,
          right: 42,
          child: Container(
            width: 38,
            height: 38,
            decoration: const BoxDecoration(color: NovaColors.deepNight, shape: BoxShape.circle),
            child: const Icon(Icons.favorite_border, color: NovaColors.gold, size: 20),
          ),
        ),
      ],
    );
  }

  BoxDecoration _cardDecoration(Color color) {
    return BoxDecoration(
      color: color,
      borderRadius: BorderRadius.circular(22),
      border: Border.all(color: NovaColors.violet.withAlpha(96)),
    );
  }
}

class _DeliveryArt extends StatelessWidget {
  const _DeliveryArt();

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 210,
      height: 145,
      child: Stack(
        children: [
          Positioned(
            top: 65,
            left: 26,
            right: 26,
            child: Container(height: 3, color: NovaColors.violet.withAlpha(55)),
          ),
          Positioned(
            top: 65,
            right: 26,
            width: 96,
            child: Container(height: 3, color: NovaColors.violet),
          ),
          const _TrackDot(right: 16, label: 'تأكيد', icon: Icons.check, done: true),
          const _TrackDot(right: 72, label: 'تجهيز', icon: Icons.check, done: true),
          const _TrackDot(right: 128, label: 'توصيل', icon: Icons.local_shipping_outlined, active: true),
          const _TrackDot(right: 178, label: 'وصل', icon: Icons.home_outlined),
          Positioned(
            top: 20,
            left: 22,
            child: Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(color: NovaColors.gold, borderRadius: BorderRadius.circular(15)),
              child: const Icon(Icons.inventory_2_outlined, color: NovaColors.deepNight),
            ),
          ),
        ],
      ),
    );
  }
}

class _TrackDot extends StatelessWidget {
  const _TrackDot({
    required this.right,
    required this.label,
    required this.icon,
    this.done = false,
    this.active = false,
  });

  final double right;
  final String label;
  final IconData icon;
  final bool done;
  final bool active;

  @override
  Widget build(BuildContext context) {
    final color = active ? NovaColors.gold : (done ? NovaColors.violet : NovaColors.darkPurple);
    return Positioned(
      top: 54,
      right: right,
      child: Column(
        children: [
          Container(
            width: 26,
            height: 26,
            decoration: BoxDecoration(color: color, shape: BoxShape.circle),
            child: Icon(icon, color: active ? NovaColors.deepNight : NovaColors.cream, size: 15),
          ),
          const SizedBox(height: 8),
          Text(label, style: TextStyle(color: active ? NovaColors.gold : NovaColors.muted, fontSize: 11, fontWeight: FontWeight.w700)),
        ],
      ),
    );
  }
}

class _MiniLogo extends StatelessWidget {
  const _MiniLogo();

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        const NovaBrandMark(size: 26),
        const SizedBox(width: 10),
        const Text.rich(
          TextSpan(text: 'Nova ', children: [TextSpan(text: 'Store', style: TextStyle(color: NovaColors.gold))]),
          style: TextStyle(color: NovaColors.cream, fontSize: 18, fontWeight: FontWeight.w900),
        ),
      ],
    );
  }
}

class _Dots extends StatelessWidget {
  const _Dots({required this.count, required this.active});

  final int count;
  final int active;

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(
        count,
        (index) => AnimatedContainer(
          duration: const Duration(milliseconds: 240),
          margin: const EdgeInsets.symmetric(horizontal: 4),
          width: active == index ? 24 : 8,
          height: 8,
          decoration: BoxDecoration(
            color: active == index ? NovaColors.violet : NovaColors.violet.withAlpha(70),
            borderRadius: BorderRadius.circular(999),
          ),
        ),
      ),
    );
  }
}
