import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/settings/app_preferences.dart';
import '../../../core/settings/currency_repository.dart';
import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/async_state_view.dart';
import '../../../core/widgets/nova_app_bar.dart';
import '../../../core/widgets/nova_network_image.dart';
import '../../catalog/data/catalog_models.dart';
import '../../catalog/data/catalog_repository.dart';
import '../../catalog/presentation/product_card.dart';
import '../data/home_repository.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  Future<void> _refresh(WidgetRef ref) async {
    ref.invalidate(homeBannersProvider);
    ref.invalidate(categoriesProvider);
    ref.invalidate(featuredProductsProvider);
    await Future.wait([
      ref.read(homeBannersProvider.future),
      ref.read(categoriesProvider.future),
      ref.read(featuredProductsProvider.future),
    ]);
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final products = ref.watch(featuredProductsProvider);
    final categories = ref.watch(categoriesProvider);
    ref.watch(appPreferencesProvider);
    final currency = ref.watch(selectedCurrencyProvider);

    return RefreshIndicator(
      onRefresh: () => _refresh(ref),
      color: NovaColors.gold,
      child: CustomScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        slivers: [
          SliverToBoxAdapter(
            child: NovaAppBar(
              title: 'NOVA Store',
              actions: [
                Padding(
                  padding: const EdgeInsetsDirectional.only(end: 12),
                  child: Chip(
                    label: Text(currency.code),
                    avatar: const Icon(Icons.payments_outlined, size: 17),
                    backgroundColor: NovaColors.gold.withAlpha(35),
                    side: BorderSide.none,
                  ),
                ),
              ],
            ),
          ),
          SliverPadding(
            padding: const EdgeInsets.all(20),
            sliver: SliverList.list(
              children: [
                const _HomeBannerSlider(),
                const SizedBox(height: 18),
                Row(
                  children: [
                    const Text('الأقسام', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                    const Spacer(),
                    TextButton(onPressed: () => context.push('/products'), child: const Text('عرض الكل')),
                  ],
                ),
                const SizedBox(height: 6),
                AsyncStateView(
                  value: categories,
                  builder: (items) => SizedBox(
                    height: 104,
                    child: ListView.separated(
                      scrollDirection: Axis.horizontal,
                      itemCount: items.length,
                      separatorBuilder: (context, index) => const SizedBox(width: 10),
                      itemBuilder: (context, index) {
                        final category = items[index];
                        return _CategoryCard(
                          category: category,
                          onTap: () => context.push(
                            Uri(
                              path: '/products',
                              queryParameters: {
                                'category_id': '${category.id}',
                                'category_name': category.name,
                              },
                            ).toString(),
                          ),
                        );
                      },
                    ),
                  ),
                ),
                const SizedBox(height: 24),
                Row(
                  children: [
                    const Expanded(
                      child: Text('منتجات مختارة', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900)),
                    ),
                    TextButton(onPressed: () => context.push('/products'), child: const Text('تسوق الآن')),
                  ],
                ),
              ],
            ),
          ),
          products.when(
            data: (items) => SliverPadding(
              padding: const EdgeInsets.fromLTRB(20, 0, 20, 24),
              sliver: SliverGrid.builder(
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  childAspectRatio: .68,
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                ),
                itemCount: items.length,
                itemBuilder: (context, index) => ProductCard(product: items[index]),
              ),
            ),
            loading: () => const SliverToBoxAdapter(
              child: Padding(
                padding: EdgeInsets.all(32),
                child: Center(child: CircularProgressIndicator(color: NovaColors.gold)),
              ),
            ),
            error: (error, stackTrace) => SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Text('$error', textAlign: TextAlign.center, style: const TextStyle(color: NovaColors.danger)),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _HomeBannerSlider extends ConsumerStatefulWidget {
  const _HomeBannerSlider();

  @override
  ConsumerState<_HomeBannerSlider> createState() => _HomeBannerSliderState();
}

class _HomeBannerSliderState extends ConsumerState<_HomeBannerSlider> {
  final PageController _controller = PageController();
  Timer? _timer;
  int _index = 0;
  int _itemsCount = 0;

  @override
  void initState() {
    super.initState();
    _timer = Timer.periodic(const Duration(seconds: 5), (_) {
      if (!mounted || _itemsCount < 2 || !_controller.hasClients) return;
      final next = (_index + 1) % _itemsCount;
      _controller.animateToPage(
        next,
        duration: const Duration(milliseconds: 650),
        curve: Curves.easeOutCubic,
      );
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final banners = ref.watch(homeBannersProvider);

    return banners.when(
      data: (items) {
        final visibleItems = items.isEmpty ? [_fallbackBanner] : items;
        _itemsCount = visibleItems.length;
        if (_index >= visibleItems.length) _index = 0;

        return Column(
          children: [
            AspectRatio(
              aspectRatio: 1.62,
              child: PageView.builder(
                controller: _controller,
                itemCount: visibleItems.length,
                onPageChanged: (value) => setState(() => _index = value),
                itemBuilder: (context, index) => Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 2),
                  child: _BannerCard(
                    banner: visibleItems[index],
                    onTap: () => _openBanner(context, visibleItems[index]),
                  ),
                ),
              ),
            ),
            if (visibleItems.length > 1) ...[
              const SizedBox(height: 10),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(
                  visibleItems.length,
                  (dotIndex) => AnimatedContainer(
                    duration: const Duration(milliseconds: 250),
                    width: dotIndex == _index ? 24 : 8,
                    height: 8,
                    margin: const EdgeInsets.symmetric(horizontal: 3),
                    decoration: BoxDecoration(
                      color: dotIndex == _index ? NovaColors.gold : NovaColors.violet.withAlpha(55),
                      borderRadius: BorderRadius.circular(999),
                    ),
                  ),
                ),
              ),
            ],
          ],
        );
      },
      loading: () => const AspectRatio(
        aspectRatio: 1.62,
        child: Center(child: CircularProgressIndicator(color: NovaColors.gold)),
      ),
      error: (error, stackTrace) => _BannerCard(
        banner: _fallbackBanner,
        onTap: () => context.push('/products'),
      ),
    );
  }

  void _openBanner(BuildContext context, HomeBanner banner) {
    switch (banner.linkType) {
      case 'category':
        final categoryId = int.tryParse((banner.linkValue ?? '').trim());
        if (categoryId == null) return;
        context.push(Uri(path: '/products', queryParameters: {'category_id': '$categoryId'}).toString());
      case 'product':
        final slug = (banner.linkValue ?? '').trim();
        if (slug.isEmpty) return;
        context.push('/products/$slug');
      case 'none':
      case 'external':
        return;
      case 'products':
      default:
        context.push('/products');
    }
  }
}

class _BannerCard extends StatelessWidget {
  const _BannerCard({required this.banner, required this.onTap});

  final HomeBanner banner;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(30),
        child: Ink(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(30),
            color: banner.backgroundColor,
            boxShadow: const [
              BoxShadow(color: Color(0x263D1F6E), blurRadius: 28, offset: Offset(0, 18)),
            ],
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(30),
            child: Stack(
              fit: StackFit.expand,
              children: [
                if (banner.imagePath != null)
                  NovaNetworkImage(
                    path: banner.imagePath,
                    fallbackText: banner.title,
                  ),
                if (banner.showTextOverlay)
                  DecoratedBox(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.centerRight,
                        end: Alignment.centerLeft,
                        colors: [
                          banner.backgroundColor.withAlpha(banner.imagePath == null ? 255 : 230),
                          banner.backgroundColor.withAlpha(banner.imagePath == null ? 225 : 160),
                          banner.backgroundColor.withAlpha(banner.imagePath == null ? 205 : 70),
                        ],
                      ),
                    ),
                  ),
                if (banner.showTextOverlay)
                  PositionedDirectional(
                    start: -36,
                    top: -30,
                    child: Container(
                      width: 128,
                      height: 128,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: banner.accentColor.withAlpha(80), width: 17),
                      ),
                    ),
                  ),
                if (banner.showTextOverlay)
                  Padding(
                    padding: const EdgeInsets.fromLTRB(24, 20, 24, 20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if ((banner.badge ?? '').isNotEmpty)
                          Text(
                            banner.badge!,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(color: banner.accentColor, fontWeight: FontWeight.w900, fontSize: 12),
                          ),
                        const Spacer(),
                        Text(
                          banner.title,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 25,
                            fontWeight: FontWeight.w900,
                            height: 1.24,
                          ),
                        ),
                        if ((banner.subtitle ?? '').isNotEmpty) ...[
                          const SizedBox(height: 8),
                          Text(
                            banner.subtitle!,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(color: Colors.white70, height: 1.55, fontWeight: FontWeight.w700),
                          ),
                        ],
                        const SizedBox(height: 14),
                        if ((banner.buttonLabel ?? '').isNotEmpty)
                          Align(
                            alignment: AlignmentDirectional.centerStart,
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                              decoration: BoxDecoration(
                                color: banner.accentColor,
                                borderRadius: BorderRadius.circular(999),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.auto_awesome, size: 16, color: NovaColors.text),
                                  const SizedBox(width: 7),
                                  Text(
                                    banner.buttonLabel!,
                                    style: const TextStyle(color: NovaColors.text, fontWeight: FontWeight.w900),
                                  ),
                                ],
                              ),
                            ),
                          ),
                      ],
                    ),
                  ),
                if (!banner.showTextOverlay && banner.imagePath == null)
                  Center(
                    child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Text(
                        banner.title,
                        textAlign: TextAlign.center,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 24,
                          fontWeight: FontWeight.w900,
                          height: 1.3,
                        ),
                      ),
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _CategoryCard extends StatelessWidget {
  const _CategoryCard({required this.category, this.onTap});

  final Category category;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(18),
      child: Container(
        width: 118,
        padding: const EdgeInsets.all(9),
        decoration: BoxDecoration(
          color: isDark ? NovaColors.darkSurface : Colors.white,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: isDark ? NovaColors.darkBorder : NovaColors.border),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: NovaNetworkImage(
                path: category.image,
                fallbackText: category.name,
                borderRadius: BorderRadius.circular(13),
              ),
            ),
            const SizedBox(height: 7),
            Text(
              category.name,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 12),
            ),
          ],
        ),
      ),
    );
  }
}

const _fallbackBanner = HomeBanner(
  id: 0,
  title: 'تقنية راقية بتجربة تسوق فاخرة',
  subtitle: 'منتجات مختارة، سلة ذكية، عملات متعددة، وتجربة داكنة تناسب متجر عالمي.',
  badge: 'مجموعة NOVA الجديدة',
  buttonLabel: 'اكتشف المنتجات',
  backgroundColor: NovaColors.deepNight,
  accentColor: NovaColors.gold,
  linkType: 'products',
  showTextOverlay: true,
);
