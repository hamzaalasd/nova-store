import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/api_client.dart';

final homeRepositoryProvider = Provider<HomeRepository>((ref) {
  return HomeRepository(ref.watch(dioProvider));
});

final homeBannersProvider = FutureProvider<List<HomeBanner>>((ref) {
  return ref.watch(homeRepositoryProvider).banners();
});

class HomeRepository {
  HomeRepository(this._dio);

  final Dio _dio;

  Future<List<HomeBanner>> banners() async {
    final response = await _dio.get<dynamic>(
      '/home-banners',
      queryParameters: {'_ts': DateTime.now().millisecondsSinceEpoch},
      options: Options(headers: {'Cache-Control': 'no-cache'}),
    );
    final data = apiData<List<dynamic>>(response);
    return data.whereType<Map<String, dynamic>>().map(HomeBanner.fromJson).toList();
  }
}

class HomeBanner {
  const HomeBanner({
    required this.id,
    required this.title,
    required this.backgroundColor,
    required this.accentColor,
    required this.linkType,
    this.showTextOverlay = true,
    this.subtitle,
    this.badge,
    this.buttonLabel,
    this.imagePath,
    this.linkValue,
  });

  final int id;
  final String title;
  final String? subtitle;
  final String? badge;
  final String? buttonLabel;
  final String? imagePath;
  final Color backgroundColor;
  final Color accentColor;
  final String linkType;
  final bool showTextOverlay;
  final String? linkValue;

  factory HomeBanner.fromJson(Map<String, dynamic> json) {
    return HomeBanner(
      id: (json['id'] as num).toInt(),
      title: '${json['title_ar'] ?? json['title_en'] ?? ''}',
      subtitle: json['subtitle_ar'] as String? ?? json['subtitle_en'] as String?,
      badge: json['badge_ar'] as String? ?? json['badge_en'] as String?,
      buttonLabel: json['button_label_ar'] as String? ?? json['button_label_en'] as String?,
      imagePath: json['image_path'] as String?,
      backgroundColor: _parseColor('${json['background_color'] ?? '#2D2438'}'),
      accentColor: _parseColor('${json['accent_color'] ?? '#B8965A'}'),
      linkType: '${json['link_type'] ?? 'products'}',
      showTextOverlay: json['show_text_overlay'] != false,
      linkValue: json['link_value'] as String?,
    );
  }

  static Color _parseColor(String value) {
    final normalized = value.trim().replaceFirst('#', '');
    final hex = normalized.length == 6 ? 'FF$normalized' : normalized;
    final parsed = int.tryParse(hex, radix: 16);
    return parsed == null ? const Color(0xFF2D2438) : Color(parsed);
  }
}
