class Category {
  const Category({
    required this.id,
    required this.name,
    required this.slug,
    this.image,
    this.bannerImage,
    this.productsCount = 0,
    this.children = const [],
  });

  final int id;
  final String name;
  final String slug;
  final String? image;
  final String? bannerImage;
  final int productsCount;
  final List<Category> children;

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: (json['id'] as num).toInt(),
      name: '${json['name_ar'] ?? json['name_en'] ?? ''}',
      slug: '${json['slug'] ?? ''}',
      image: json['image'] as String?,
      bannerImage: json['banner_image'] as String?,
      productsCount: (json['products_count'] as num?)?.toInt() ?? 0,
      children: (json['children'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(Category.fromJson)
          .toList(),
    );
  }
}

class ProductImage {
  const ProductImage({required this.path, this.alt});

  final String path;
  final String? alt;

  factory ProductImage.fromJson(Map<String, dynamic> json) {
    return ProductImage(
      path: '${json['image_path'] ?? json['url'] ?? ''}',
      alt: json['alt_ar'] as String?,
    );
  }
}

class ProductVariant {
  const ProductVariant({
    required this.id,
    required this.sku,
    required this.price,
    required this.stockStatus,
  });

  final int id;
  final String sku;
  final double price;
  final String stockStatus;

  factory ProductVariant.fromJson(Map<String, dynamic> json) {
    return ProductVariant(
      id: (json['id'] as num).toInt(),
      sku: '${json['sku'] ?? ''}',
      price: double.tryParse('${json['effective_price'] ?? json['price'] ?? 0}') ?? 0,
      stockStatus: '${json['stock_status'] ?? 'in_stock'}',
    );
  }
}

class Product {
  const Product({
    required this.id,
    required this.name,
    required this.slug,
    required this.sku,
    required this.price,
    this.basePrice,
    this.salePrice,
    this.shortDescription,
    this.description,
    this.stockStatus = 'in_stock',
    this.isFeatured = false,
    this.mainImage,
    this.category,
    this.images = const [],
    this.variants = const [],
  });

  final int id;
  final String name;
  final String slug;
  final String sku;
  final double price;
  final double? basePrice;
  final double? salePrice;
  final String? shortDescription;
  final String? description;
  final String stockStatus;
  final bool isFeatured;
  final String? mainImage;
  final Category? category;
  final List<ProductImage> images;
  final List<ProductVariant> variants;

  bool get hasSale => salePrice != null;

  int? get discountPercent {
    final base = basePrice;
    final sale = salePrice;
    if (base == null || sale == null || base <= 0) return null;
    return ((1 - (sale / base)) * 100).round();
  }

  factory Product.fromJson(Map<String, dynamic> json) {
    final categoryJson = json['category'];
    return Product(
      id: (json['id'] as num).toInt(),
      name: '${json['name_ar'] ?? json['name_en'] ?? ''}',
      slug: '${json['slug'] ?? ''}',
      sku: '${json['sku'] ?? ''}',
      price: double.tryParse('${json['effective_price'] ?? 0}') ?? 0,
      basePrice: double.tryParse('${json['base_price'] ?? ''}'),
      salePrice: json['sale_price'] == null ? null : double.tryParse('${json['sale_price']}'),
      shortDescription: json['short_description_ar'] as String?,
      description: json['description_ar'] as String?,
      stockStatus: '${json['stock_status'] ?? 'in_stock'}',
      isFeatured: json['is_featured'] == true,
      mainImage: json['main_image'] as String?,
      category: categoryJson is Map<String, dynamic> ? Category.fromJson(categoryJson) : null,
      images: (json['images'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(ProductImage.fromJson)
          .toList(),
      variants: (json['variants'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(ProductVariant.fromJson)
          .toList(),
    );
  }
}
