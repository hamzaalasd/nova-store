@extends('layouts.storefront')

@section('title', 'المتجر | NOVA')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-xl nova-shell">
            <div class="p-6 nova-hero sm:p-8">
                <div class="nova-eyebrow">المتجر</div>
                <h1 class="mt-2 text-3xl font-black">كل المنتجات</h1>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-white/60">بحث، تصفية، فرز، عروض، ومخزون مرتبط مباشرة بقاعدة بيانات NOVA.</p>
            </div>

            <div class="flex gap-2 overflow-x-auto border-b border-[#E0D8CE] bg-nova-warm p-4">
                <a href="{{ route('products.index', request()->except('category')) }}" class="shrink-0 rounded-full px-4 py-2 text-sm font-black nova-category-pill {{ request('category') ? '' : 'nova-category-pill-active' }}">الكل</a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $category->slug])) }}" class="shrink-0 rounded-full px-4 py-2 text-sm font-black nova-category-pill {{ request('category') === $category->slug ? 'nova-category-pill-active' : '' }}">{{ $category->name_ar }}</a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('products.index') }}" class="grid gap-3 border-b border-[#E0D8CE] bg-white p-4 lg:grid-cols-[1.4fr_1fr_.75fr_.75fr_.75fr_.75fr]">
                <input name="q" value="{{ request('q') }}" placeholder="ابحث باسم المنتج أو SKU" class="rounded-lg px-4 py-3 text-sm nova-input">
                <select name="category" class="rounded-lg px-4 py-3 text-sm nova-input">
                    <option value="">كل التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name_ar }}</option>
                    @endforeach
                </select>
                <input name="min" value="{{ request('min') }}" placeholder="من سعر" class="rounded-lg px-4 py-3 text-sm nova-input">
                <input name="max" value="{{ request('max') }}" placeholder="إلى سعر" class="rounded-lg px-4 py-3 text-sm nova-input">
                <select name="stock" class="rounded-lg px-4 py-3 text-sm nova-input">
                    <option value="">كل المخزون</option>
                    <option value="in_stock" @selected(request('stock') === 'in_stock')>متوفر</option>
                    <option value="pre_order" @selected(request('stock') === 'pre_order')>طلب مسبق</option>
                    <option value="out_of_stock" @selected(request('stock') === 'out_of_stock')>غير متوفر</option>
                </select>
                <select name="sort" class="rounded-lg px-4 py-3 text-sm nova-input">
                    <option value="newest" @selected(request('sort') === 'newest')>الأحدث</option>
                    <option value="featured" @selected(request('sort') === 'featured')>المميز</option>
                    <option value="rating" @selected(request('sort') === 'rating')>الأعلى تقييماً</option>
                    <option value="price_low" @selected(request('sort') === 'price_low')>السعر الأقل</option>
                    <option value="price_high" @selected(request('sort') === 'price_high')>السعر الأعلى</option>
                </select>
                <button class="rounded-lg px-5 py-3 text-sm font-black lg:col-start-6 nova-btn-primary" type="submit">تطبيق</button>
            </form>
        </div>
    </section>

    @if($activeCoupons->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-6 sm:px-6 lg:px-8">
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($activeCoupons as $coupon)
                    <div class="rounded-xl border border-nova-gold/25 bg-white p-4">
                        <span class="text-xs font-black text-nova-copper">كوبون</span>
                        <strong class="mx-2">{{ $coupon->code }}</strong>
                        <span class="text-sm text-nova-muted">{{ $coupon->name_ar }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-xl font-black">{{ $products->total() }} منتج</h2>
            <a href="{{ route('products.index') }}" class="nova-link text-sm">إزالة الفلاتر</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($products as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-[#E0D8CE] bg-white p-10 text-center">
                    <h2 class="text-xl font-black">لا توجد منتجات مطابقة</h2>
                    <p class="mt-2 text-sm text-nova-muted">غيّر الفلاتر أو أضف منتجات نشطة من لوحة الإدارة.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </section>
@endsection
