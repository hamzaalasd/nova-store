import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/api_client.dart';
import 'catalog_models.dart';

final catalogRepositoryProvider = Provider<CatalogRepository>((ref) {
  return CatalogRepository(ref.watch(dioProvider));
});

final categoriesProvider = FutureProvider<List<Category>>((ref) {
  return ref.watch(catalogRepositoryProvider).categories();
});

final featuredProductsProvider = FutureProvider<List<Product>>((ref) {
  return ref.watch(catalogRepositoryProvider).products(sort: 'featured');
});

final productsProvider = FutureProvider.family<List<Product>, ProductQuery>((ref, query) {
  return ref.watch(catalogRepositoryProvider).products(
        search: query.search,
        categoryId: query.categoryId,
        sort: query.sort,
      );
});

final productProvider = FutureProvider.family<Product, String>((ref, slug) {
  return ref.watch(catalogRepositoryProvider).product(slug);
});

class ProductQuery {
  const ProductQuery({this.search, this.categoryId, this.sort = 'newest'});

  final String? search;
  final int? categoryId;
  final String sort;

  @override
  bool operator ==(Object other) {
    return other is ProductQuery &&
        other.search == search &&
        other.categoryId == categoryId &&
        other.sort == sort;
  }

  @override
  int get hashCode => Object.hash(search, categoryId, sort);
}

class CatalogRepository {
  CatalogRepository(this._dio);

  final Dio _dio;

  Future<List<Product>> products({String? search, int? categoryId, String sort = 'newest'}) async {
    final queryParameters = <String, dynamic>{
      if (search != null && search.trim().isNotEmpty) 'search': search.trim(),
      'sort': sort,
      'per_page': 50,
    };
    if (categoryId != null) {
      queryParameters['category_id'] = categoryId;
    }

    final products = <Product>[];
    var page = 1;
    var lastPage = 1;

    do {
      final response = await _dio.get<dynamic>(
        '/products',
        queryParameters: {
          ...queryParameters,
          'page': page,
          '_ts': DateTime.now().millisecondsSinceEpoch,
        },
        options: Options(headers: {'Cache-Control': 'no-cache'}),
      );
      final data = apiData<List<dynamic>>(response);
      products.addAll(data.whereType<Map<String, dynamic>>().map(Product.fromJson));

      final body = response.data;
      if (body is Map<String, dynamic>) {
        final meta = body['meta'];
        if (meta is Map<String, dynamic>) {
          lastPage = (meta['last_page'] as num?)?.toInt() ?? 1;
        }
      }
      page++;
    } while (page <= lastPage);

    return products;
  }

  Future<Product> product(String slug) async {
    final response = await _dio.get<dynamic>('/products/$slug');
    return Product.fromJson(apiData<Map<String, dynamic>>(response));
  }

  Future<List<Category>> categories() async {
    final response = await _dio.get<dynamic>(
      '/categories',
      queryParameters: {'_ts': DateTime.now().millisecondsSinceEpoch},
      options: Options(headers: {'Cache-Control': 'no-cache'}),
    );
    final data = apiData<List<dynamic>>(response);
    final roots = data.whereType<Map<String, dynamic>>().map(Category.fromJson).toList();
    return _flattenCategories(roots);
  }

  List<Category> _flattenCategories(List<Category> categories) {
    return [
      for (final category in categories) ...[
        category,
        ..._flattenCategories(category.children),
      ],
    ];
  }
}
