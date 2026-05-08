@extends('layouts.storefront')

@section('title', $product->name_ar.' | NOVA')

@section('content')
    @php
        $mainImage = $product->main_image ?: $product->images->first()?->image_path;
        $rating = $product->approved_reviews_avg_rating ? number_format((float) $product->approved_reviews_avg_rating, 1) : null;
        $reviewsCount = (int) ($product->approved_reviews_count ?? 0);
        $discountPercent = $product->sale_price && (float) $product->base_price > 0
            ? round((1 - ((float) $product->sale_price / (float) $product->base_price)) * 100)
            : null;
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid overflow-hidden rounded-xl nova-shell lg:grid-cols-[1fr_1fr]">
            <div class="bg-nova-warm p-5">
                <div class="grid aspect-[4/3] place-items-center overflow-hidden rounded-xl border border-[#E0D8CE] bg-white">
                    @if($mainImage)
                        <img src="{{ str_starts_with($mainImage, 'http') ? $mainImage : asset('storage/'.$mainImage) }}" alt="{{ $product->name_ar }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-6xl font-black text-nova-violet">{{ mb_substr($product->name_ar, 0, 2) }}</span>
                    @endif
                </div>
                @if($product->images->isNotEmpty())
                    <div class="mt-3 flex gap-2 overflow-x-auto">
                        @foreach($product->images->take(5) as $image)
                            <img src="{{ str_starts_with($image->image_path, 'http') ? $image->image_path : asset('storage/'.$image->image_path) }}" alt="{{ $image->alt_ar ?: $product->name_ar }}" class="size-16 rounded-lg border border-[#E0D8CE] object-cover">
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="p-6 lg:p-8">
                <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}" class="nova-eyebrow">{{ $product->category?->name_ar }}</a>
                <h1 class="mt-3 text-3xl font-black leading-tight">{{ $product->name_ar }}</h1>

                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                    <span class="text-nova-copper">★★★★★</span>
                    <span class="text-nova-muted">{{ $rating ? $rating.' ('.$reviewsCount.' تقييم)' : 'لا توجد تقييمات بعد' }}</span>
                    <span class="font-black text-nova-copper">{{ $product->stock_status === 'in_stock' ? 'في المخزون' : 'غير متوفر' }}</span>
                </div>

                <p class="mt-5 text-sm leading-8 text-nova-muted">{{ $product->description_ar ?: $product->short_description_ar ?: 'تفاصيل المنتج ستظهر هنا عند إضافتها من لوحة الإدارة.' }}</p>

                <div class="mt-6 rounded-xl bg-nova-warm p-5">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="text-3xl font-black">{{ number_format((float) $product->effective_price, 2) }} ر.س</div>
                        @if($product->sale_price)
                            <div class="text-sm text-[#8A8292] line-through">{{ number_format((float) $product->base_price, 2) }} ر.س</div>
                            <span class="rounded-md px-2 py-1 text-xs font-black nova-badge-gold">وفرت {{ $discountPercent }}%</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('cart.add') }}" class="mt-6 grid gap-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    @if($product->variants->isNotEmpty())
                        <label class="grid gap-2 text-sm font-black">
                            اختر المتغير
                            <select name="product_variant_id" class="rounded-lg px-4 py-3 text-sm nova-input">
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}">{{ $variant->sku }} - {{ number_format((float) $variant->effective_price, 2) }} ر.س</option>
                                @endforeach
                            </select>
                        </label>
                    @endif
                    <div class="grid gap-3 sm:grid-cols-[120px_1fr_1fr]">
                        <input type="number" name="quantity" value="1" min="1" class="rounded-lg px-4 py-3 text-sm nova-input">
                        <button class="rounded-lg px-5 py-3 text-sm font-black nova-btn-dark" type="submit">أضف للسلة</button>
                        <button class="rounded-lg px-5 py-3 text-sm font-black nova-btn-primary" type="submit">اشتر الآن</button>
                    </div>
                </form>

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border border-[#E0D8CE] bg-white p-4 text-sm font-black">دفع آمن</div>
                    <div class="rounded-lg border border-[#E0D8CE] bg-white p-4 text-sm font-black">تتبع الطلب</div>
                    <div class="rounded-lg border border-[#E0D8CE] bg-white p-4 text-sm font-black">دعم عربي</div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-6 px-4 pb-12 sm:px-6 lg:grid-cols-[1fr_1fr] lg:px-8">
            <div class="rounded-xl p-5 nova-panel">
                <h2 class="text-xl font-black">المواصفات</h2>
                <div class="mt-4 grid gap-3">
                    @forelse($product->specifications as $spec)
                        <div class="flex justify-between rounded-lg bg-white p-3 text-sm">
                            <span class="text-nova-muted">{{ $spec->name_ar ?? $spec->key }}</span>
                            <strong>{{ $spec->value_ar ?? $spec->value }}</strong>
                        </div>
                    @empty
                        <p class="text-sm text-nova-muted">لم تتم إضافة مواصفات لهذا المنتج بعد.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl p-5 nova-panel">
                <h2 class="text-xl font-black">آراء العملاء</h2>
                <div class="mt-4 grid gap-3">
                    @forelse($product->approvedReviews->take(3) as $review)
                        <div class="rounded-lg bg-white p-4">
                            <div class="flex items-center justify-between">
                                <strong class="text-sm">{{ $review->title ?: $review->user?->name ?: 'عميل NOVA' }}</strong>
                                <span class="text-xs text-nova-copper">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            @if($review->comment)
                                <p class="mt-2 text-sm leading-7 text-nova-muted">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-nova-muted">لا توجد مراجعات معتمدة بعد.</p>
                    @endforelse
                </div>
                @auth
                    <form method="POST" action="{{ route('products.reviews.store', $product) }}" class="mt-5 grid gap-3 rounded-lg bg-white p-4">
                        @csrf
                        <div class="text-sm font-black">قيّم المنتج بعد الشراء</div>
                        <select name="rating" class="rounded-lg px-4 py-3 text-sm nova-input">
                            <option value="5">5 نجوم</option>
                            <option value="4">4 نجوم</option>
                            <option value="3">3 نجوم</option>
                            <option value="2">2 نجوم</option>
                            <option value="1">1 نجمة</option>
                        </select>
                        <input name="title" class="rounded-lg px-4 py-3 text-sm nova-input" placeholder="عنوان التقييم">
                        <textarea name="comment" rows="3" class="rounded-lg px-4 py-3 text-sm nova-input" placeholder="اكتب تجربتك"></textarea>
                        <button class="rounded-lg px-4 py-3 text-sm font-black nova-btn-dark" type="submit">إرسال التقييم</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="mt-5 block rounded-lg bg-white p-4 text-sm font-black text-nova-copper">سجل الدخول لإرسال تقييم بعد الشراء.</a>
                @endauth
            </div>
    </section>

    @if($relatedProducts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
            <div class="mb-5 flex items-end justify-between">
                <h2 class="text-2xl font-black">منتجات مشابهة</h2>
                <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}" class="nova-link text-sm">عرض القسم</a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($relatedProducts as $related)
                    @include('partials.product-card', ['product' => $related])
                @endforeach
            </div>
        </section>
    @endif
@endsection
