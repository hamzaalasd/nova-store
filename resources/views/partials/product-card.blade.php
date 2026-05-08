@php
    $image = $product->main_image ?: $product->images->first()?->image_path;
    $rating = $product->approved_reviews_avg_rating ? number_format((float) $product->approved_reviews_avg_rating, 1) : null;
    $reviewsCount = (int) ($product->approved_reviews_count ?? 0);
    $discountPercent = $product->sale_price && (float) $product->base_price > 0
        ? round((1 - ((float) $product->sale_price / (float) $product->base_price)) * 100)
        : null;
@endphp

<article class="group overflow-hidden rounded-xl nova-product-card nova-card-hover">
    <a href="{{ route('products.show', $product) }}" class="block">
        <div class="relative grid aspect-[4/3] place-items-center overflow-hidden nova-product-media">
            @if($image)
                <img src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}" alt="{{ $product->name_ar }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            @else
                <div class="grid size-full place-items-center bg-nova-warm p-6 text-center text-nova-violet">
                    <span class="text-4xl font-black">{{ mb_substr($product->name_ar, 0, 2) }}</span>
                </div>
            @endif

            @if($discountPercent)
                <span class="absolute right-3 top-3 rounded-md px-2 py-1 text-xs font-black nova-badge-gold">-{{ $discountPercent }}%</span>
            @elseif($product->is_featured)
                <span class="absolute right-3 top-3 rounded-md px-2 py-1 text-xs font-black nova-badge-dark">مميز</span>
            @endif
        </div>
    </a>

    <div class="p-4">
        <div class="mb-1 text-xs font-bold tracking-wide text-nova-muted">{{ $product->category?->name_ar ?: 'NOVA' }}</div>
        <a href="{{ route('products.show', $product) }}" class="block min-h-11 text-sm font-black leading-6 text-nova-ink transition hover:text-nova-copper">{{ $product->name_ar }}</a>

        <div class="mt-2 flex items-center gap-2">
            <span class="text-xs text-nova-copper">★★★★★</span>
            <span class="text-xs text-nova-muted">{{ $rating ? $rating.' ('.$reviewsCount.')' : 'جديد' }}</span>
        </div>

        <div class="mt-4 flex items-end justify-between gap-3">
            <div>
                <div class="text-base font-black text-nova-ink">{{ number_format((float) $product->effective_price, 2) }} ر.س</div>
                @if($product->sale_price)
                    <div class="text-xs text-[#8A8292] line-through">{{ number_format((float) $product->base_price, 2) }} ر.س</div>
                @endif
            </div>
            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button class="grid size-8 place-items-center rounded-lg bg-nova-ink text-lg font-black text-nova-gold-soft transition hover:bg-nova-violet" type="submit" title="إضافة للسلة">+</button>
            </form>
        </div>
    </div>
</article>
