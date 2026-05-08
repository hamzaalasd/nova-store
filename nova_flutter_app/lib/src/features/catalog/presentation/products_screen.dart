import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/api_client.dart';
import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/nova_app_bar.dart';
import '../data/catalog_repository.dart';
import 'product_card.dart';

class ProductsScreen extends ConsumerStatefulWidget {
  const ProductsScreen({super.key, this.categoryId, this.categoryName});

  final int? categoryId;
  final String? categoryName;

  @override
  ConsumerState<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends ConsumerState<ProductsScreen> {
  String _search = '';
  String _sort = 'newest';

  ProductQuery get _query => ProductQuery(
        search: _search,
        categoryId: widget.categoryId,
        sort: _sort,
      );

  Future<void> _refresh() async {
    ref.invalidate(productsProvider(_query));
    await ref.read(productsProvider(_query).future);
  }

  @override
  Widget build(BuildContext context) {
    final products = ref.watch(productsProvider(_query));
    final title = widget.categoryName?.trim().isNotEmpty == true ? widget.categoryName! : 'المتجر';

    return Column(
      children: [
        NovaAppBar(
          title: title,
          actions: [
            IconButton(
              tooltip: 'تحديث',
              onPressed: _refresh,
              icon: const Icon(Icons.refresh),
            ),
            PopupMenuButton<String>(
              initialValue: _sort,
              onSelected: (value) => setState(() => _sort = value),
              itemBuilder: (context) => const [
                PopupMenuItem(value: 'newest', child: Text('الأحدث')),
                PopupMenuItem(value: 'featured', child: Text('المميز')),
                PopupMenuItem(value: 'price_asc', child: Text('الأقل سعرا')),
                PopupMenuItem(value: 'price_desc', child: Text('الأعلى سعرا')),
              ],
            ),
          ],
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(20, 0, 20, 12),
          child: TextField(
            decoration: const InputDecoration(
              prefixIcon: Icon(Icons.search),
              hintText: 'ابحث باسم المنتج أو SKU',
            ),
            onSubmitted: (value) => setState(() => _search = value),
          ),
        ),
        Expanded(
          child: RefreshIndicator(
            onRefresh: _refresh,
            color: NovaColors.gold,
            child: products.when(
              data: (items) {
                if (items.isEmpty) {
                  return const _EmptyProducts();
                }

                return GridView.builder(
                  physics: const AlwaysScrollableScrollPhysics(),
                  padding: const EdgeInsets.fromLTRB(20, 0, 20, 24),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    childAspectRatio: .68,
                    crossAxisSpacing: 12,
                    mainAxisSpacing: 12,
                  ),
                  itemCount: items.length,
                  itemBuilder: (context, index) => ProductCard(product: items[index]),
                );
              },
              loading: () => const _RefreshableMessage(
                child: CircularProgressIndicator(color: NovaColors.gold),
              ),
              error: (error, stackTrace) => _RefreshableMessage(
                child: Text(
                  readableApiError(error),
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: NovaColors.danger, fontWeight: FontWeight.w800),
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

class _EmptyProducts extends StatelessWidget {
  const _EmptyProducts();

  @override
  Widget build(BuildContext context) {
    return const _RefreshableMessage(
      child: Text(
        'لا توجد منتجات في هذا القسم حاليا',
        textAlign: TextAlign.center,
        style: TextStyle(fontWeight: FontWeight.w800),
      ),
    );
  }
}

class _RefreshableMessage extends StatelessWidget {
  const _RefreshableMessage({required this.child});

  final Widget child;

  @override
  Widget build(BuildContext context) {
    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.all(24),
      children: [
        SizedBox(height: MediaQuery.sizeOf(context).height * .24),
        Center(child: child),
      ],
    );
  }
}
