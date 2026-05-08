@extends('layouts.storefront')

@section('title', 'NOVA | متجر التقنية الراقية')

@section('content')
    @php
        $heroTitle = $heroBanner?->title_ar ?: 'اكتشف عالماً من التقنية الراقية';
        $heroSubtitle = $heroBanner?->subtitle_ar ?: 'أجهزة مختارة بعناية لمن يقدر الجودة، مع تجربة شراء مرتبطة بالسلة والطلبات والشحن والدفع.';
        $heroAction = $heroBanner?->button_text_ar ?: 'تسوق الآن';
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-xl nova-shell">
            <div class="grid gap-8 p-6 nova-hero lg:grid-cols-[1fr_320px] lg:p-10">
                <div>
                    <div class="nova-eyebrow">مجموعة NOVA الجديدة</div>
                    <h1 class="mt-3 max-w-2xl text-4xl font-black leading-tight sm:text-5xl">
                        {{ $heroTitle }}
                        <span class="block nova-gold-text">بتجربة شراء متكاملة</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-sm leading-8 text-white/60">{{ $heroSubtitle }}</p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ $heroBanner?->link ?: route('products.index') }}" class="rounded-lg px-6 py-3 text-sm font-black nova-btn-primary">{{ $heroAction }}</a>
                        <a href="{{ route('products.index', ['sort' => 'featured']) }}" class="rounded-lg border border-nova-gold-soft/40 px-6 py-3 text-sm font-black text-nova-gold-soft transition hover:bg-white/5">اكتشف العروض</a>
                    </div>
                    <div class="mt-8 grid max-w-xl grid-cols-3 gap-4 border-t border-nova-gold-soft/15 pt-6">
                        <div>
                            <div class="text-2xl font-black text-nova-gold-soft">+{{ number_format($stats['customers']) }}</div>
                            <div class="mt-1 text-xs text-white/45">عميل راض</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-nova-gold-soft">+{{ number_format($stats['products']) }}</div>
                            <div class="mt-1 text-xs text-white/45">منتج نشط</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-nova-gold-soft">{{ $stats['rating'] }}</div>
                            <div class="mt-1 text-xs text-white/45">تقييم عام</div>
                        </div>
                    </div>
                </div>

                <div class="grid place-items-center rounded-xl bg-nova-violet p-6 text-center text-nova-gold-soft">
                    <div>
                        <div class="text-6xl font-black">N</div>
                        <div class="mt-4 text-sm leading-7 text-white/60">منتجات، عروض، شحن، دفع، وكوبونات في واجهة واحدة.</div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 border-t border-nova-gold-soft/10 p-4 nova-trust-bar sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <div class="text-sm font-black text-white">شحن مرن</div>
                    <div class="mt-1 text-xs text-white/45">{{ $shippingMethods->first()?->name_ar ?: 'يدعم طرق شحن متعددة' }}</div>
                </div>
                <div>
                    <div class="text-sm font-black text-white">دفع آمن</div>
                    <div class="mt-1 text-xs text-white/45">{{ $paymentMethods->pluck('name_ar')->take(2)->join(' · ') ?: 'mada · Apple Pay · STC Pay' }}</div>
                </div>
                <div>
                    <div class="text-sm font-black text-white">عروض مباشرة</div>
                    <div class="mt-1 text-xs text-white/45">{{ $activeCoupons->first()?->code ?: 'خصومات من لوحة الإدارة' }}</div>
                </div>
                <div>
                    <div class="text-sm font-black text-white">تتبع الطلب</div>
                    <div class="mt-1 text-xs text-white/45">حالات الطلب والدفع محفوظة في النظام</div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <div class="nova-eyebrow">التصنيفات</div>
                <h2 class="mt-2 text-2xl font-black">تسوق حسب القسم</h2>
            </div>
            <a href="{{ route('products.index') }}" class="nova-link text-sm">كل المنتجات</a>
        </div>

        <div class="flex gap-2 overflow-x-auto rounded-xl border border-[#E0D8CE] bg-nova-warm p-3">
            <a href="{{ route('products.index') }}" class="shrink-0 rounded-full px-4 py-2 text-sm font-black nova-category-pill nova-category-pill-active">الكل</a>
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="shrink-0 rounded-full px-4 py-2 text-sm font-black nova-category-pill">{{ $category->name_ar }} · {{ $category->products_count }}</a>
            @endforeach
        </div>
    </section>

    @if($activeCoupons->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
            <div class="grid gap-3 lg:grid-cols-3">
                @foreach($activeCoupons as $coupon)
                    <div class="rounded-xl border border-nova-gold/25 bg-white p-4 shadow-sm">
                        <div class="text-xs font-black text-nova-copper">كوبون نشط</div>
                        <div class="mt-2 text-xl font-black">{{ $coupon->code }}</div>
                        <div class="mt-1 text-sm text-nova-muted">{{ $coupon->name_ar }} · حد أدنى {{ number_format((float) ($coupon->minimum_order_amount ?? 0), 0) }} ر.س</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <div class="nova-eyebrow">الأكثر تميزاً</div>
                <h2 class="mt-2 text-2xl font-black">منتجات مختارة</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'featured']) }}" class="nova-link text-sm">عرض المزيد</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($featuredProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-[#E0D8CE] bg-white p-10 text-center">
                    <h3 class="text-xl font-black">المتجر جاهز للمنتجات</h3>
                    <p class="mt-2 text-sm text-nova-muted">أضف منتجات نشطة من لوحة الإدارة وستظهر هنا تلقائياً.</p>
                </div>
            @endforelse
        </div>
    </section>

    @if($offerProducts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-nova-warm p-5">
                <div class="mb-5 flex items-end justify-between gap-4">
                    <div>
                        <div class="nova-eyebrow">العروض</div>
                        <h2 class="mt-2 text-2xl font-black">خصومات فعالة من الباك اند</h2>
                    </div>
                    <a href="{{ route('products.index', ['sort' => 'featured']) }}" class="nova-link text-sm">كل العروض</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($offerProducts as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
