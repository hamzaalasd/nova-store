import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/storage/token_store.dart';
import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/nova_brand_mark.dart';

class SplashScreen extends ConsumerStatefulWidget {
  const SplashScreen({super.key});

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen> with TickerProviderStateMixin {
  late final AnimationController _logoController;
  late final AnimationController _ringController;
  late final AnimationController _reverseRingController;
  late final AnimationController _progressController;
  late final Animation<double> _logoScale;
  late final Animation<double> _logoOpacity;

  @override
  void initState() {
    super.initState();
    _logoController = AnimationController(vsync: this, duration: const Duration(milliseconds: 900));
    _ringController = AnimationController(vsync: this, duration: const Duration(seconds: 12))..repeat();
    _reverseRingController = AnimationController(vsync: this, duration: const Duration(seconds: 8))..repeat();
    _progressController = AnimationController(vsync: this, duration: const Duration(milliseconds: 2300));
    _logoScale = CurvedAnimation(parent: _logoController, curve: Curves.elasticOut);
    _logoOpacity = CurvedAnimation(parent: _logoController, curve: const Interval(0, .45));
    Future<void>.microtask(_boot);
  }

  Future<void> _boot() async {
    await Future<void>.delayed(const Duration(milliseconds: 160));
    _logoController.forward();
    _progressController.forward();
    await Future<void>.delayed(const Duration(milliseconds: 2700));
    final completed = await ref.read(tokenStoreProvider).hasCompletedOnboarding();
    if (!mounted) return;
    context.go(completed ? '/' : '/onboarding');
  }

  @override
  void dispose() {
    _logoController.dispose();
    _ringController.dispose();
    _reverseRingController.dispose();
    _progressController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: NovaColors.deepNight,
      body: Stack(
        alignment: Alignment.center,
        children: [
          Positioned.fill(child: CustomPaint(painter: _SplashGlowPainter())),
          SafeArea(
            child: Column(
              children: [
                const Spacer(flex: 2),
                SizedBox(
                  width: 184,
                  height: 184,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      AnimatedBuilder(
                        animation: _ringController,
                        builder: (context, child) => Transform.rotate(
                          angle: _ringController.value * 2 * math.pi,
                          child: const _DashedRing(size: 166, color: NovaColors.gold, alpha: 64),
                        ),
                      ),
                      AnimatedBuilder(
                        animation: _reverseRingController,
                        builder: (context, child) => Transform.rotate(
                          angle: -_reverseRingController.value * 2 * math.pi,
                          child: const _DashedRing(size: 124, color: NovaColors.violet, alpha: 92),
                        ),
                      ),
                      Container(
                        width: 92,
                        height: 92,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(color: NovaColors.violet.withAlpha(52)),
                        ),
                      ),
                      FadeTransition(
                        opacity: _logoOpacity,
                        child: ScaleTransition(
                          scale: _logoScale,
                          child: const NovaBrandMark(size: 86),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 26),
                const _BrandName(),
                const SizedBox(height: 8),
                const Text(
                  'متجرك الفاخر',
                  style: TextStyle(
                    color: NovaColors.muted,
                    fontSize: 13,
                    letterSpacing: 4,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const Spacer(flex: 2),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 82),
                  child: Column(
                    children: [
                      const Text(
                        'LOADING',
                        style: TextStyle(
                          color: Color(0xFF3D2A5A),
                          fontSize: 9,
                          letterSpacing: 4,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      const SizedBox(height: 8),
                      AnimatedBuilder(
                        animation: _progressController,
                        builder: (context, child) => ClipRRect(
                          borderRadius: BorderRadius.circular(999),
                          child: LinearProgressIndicator(
                            value: Curves.easeInOut.transform(_progressController.value),
                            minHeight: 3,
                            color: NovaColors.gold,
                            backgroundColor: NovaColors.violet.withAlpha(45),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 34),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _BrandName extends StatelessWidget {
  const _BrandName();

  @override
  Widget build(BuildContext context) {
    return const Text.rich(
      TextSpan(
        text: 'Nova ',
        children: [
          TextSpan(text: 'Store', style: TextStyle(color: NovaColors.gold)),
        ],
      ),
      style: TextStyle(
        color: NovaColors.cream,
        fontSize: 32,
        fontWeight: FontWeight.w600,
        letterSpacing: 2,
      ),
    );
  }
}

class _DashedRing extends StatelessWidget {
  const _DashedRing({required this.size, required this.color, required this.alpha});

  final double size;
  final Color color;
  final int alpha;

  @override
  Widget build(BuildContext context) {
    return CustomPaint(
      size: Size.square(size),
      painter: _RingPainter(color.withAlpha(alpha)),
    );
  }
}

class _SplashGlowPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final purpleCenter = Offset(size.width / 2, size.height * .42);
    final purplePaint = Paint()
      ..shader = RadialGradient(
        colors: [NovaColors.violet.withAlpha(48), Colors.transparent],
      ).createShader(Rect.fromCircle(center: purpleCenter, radius: 230));
    canvas.drawCircle(purpleCenter, 230, purplePaint);

    final goldCenter = Offset(size.width / 2 + 34, size.height * .39);
    final goldPaint = Paint()
      ..shader = RadialGradient(
        colors: [NovaColors.gold.withAlpha(26), Colors.transparent],
      ).createShader(Rect.fromCircle(center: goldCenter, radius: 150));
    canvas.drawCircle(goldCenter, 150, goldPaint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

class _RingPainter extends CustomPainter {
  const _RingPainter(this.color);

  final Color color;

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = color
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1
      ..strokeCap = StrokeCap.round;
    final radius = size.width / 2 - 1;
    final center = Offset(size.width / 2, size.height / 2);
    const segments = 34;
    for (var index = 0; index < segments; index++) {
      final start = index * 2 * math.pi / segments;
      canvas.drawArc(Rect.fromCircle(center: center, radius: radius), start, .11, false, paint);
    }
  }

  @override
  bool shouldRepaint(covariant _RingPainter oldDelegate) => oldDelegate.color != color;
}
