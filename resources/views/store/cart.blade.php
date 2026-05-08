@extends('layouts.storefront')

@section('title', 'السلة | NOVA')

@section('content')
    @php
        $subtotal = $cart->items->sum(fn ($item) => (float) $item->unit_price * $item->quantity);
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-xl nova-shell">
            <div class="flex items-center justify-between border-b border-[#E0D8CE] p-5">
                <div>
                    <div class="nova-eyebrow">سلة التسوق</div>
                    <h1 class="mt-1 text-2xl font-black">سلتك</h1>
                </div>
                <span class="text-sm text-nova-muted">{{ $cart->items->sum('quantity') }} منتج</span>
            </div>

            @if($cart->items->isEmpty())
                <div class="p-12 text-center">
                    <h2 class="text-xl font-black">السلة فارغة</h2>
                    <p class="mt-2 text-sm text-nova-muted">ابدأ بإضافة منتجات من المتجر.</p>
                    <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-lg px-5 py-3 text-sm font-black nova-btn-dark">تصفح المنتجات</a>
                </div>
            @else
                <div class="grid lg:grid-cols-[1fr_340px]">
                    <div class="divide-y divide-[#E0D8CE] p-5">
                        @foreach($cart->items as $item)
                            @php
                                $image = $item->product->main_image ?: $item->product->images->first()?->image_path;
                            @endphp
                            <div class="grid gap-4 py-4 sm:grid-cols-[72px_1fr_auto] sm:items-center">
                                <div class="grid size-18 place-items-center overflow-hidden rounded-lg bg-nova-warm text-xl font-black text-nova-violet">
                                    @if($image)
                                        <img src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}" alt="{{ $item->product->name_ar }}" class="h-full w-full object-cover">
                                    @else
                                        {{ mb_substr($item->product->name_ar, 0, 2) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-black">{{ $item->product->name_ar }}</div>
                                    <div class="mt-1 text-xs text-nova-muted">SKU: {{ $item->variant?->sku ?? $item->product->sku }}</div>
                                    <div class="mt-2 text-sm font-black">{{ number_format((float) $item->unit_price, 2) }} ر.س</div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                    <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" class="w-20 rounded-lg px-3 py-2 text-sm nova-input">
                                        <button class="rounded-lg border border-[#E0D8CE] px-3 py-2 text-sm font-black transition hover:border-nova-copper" type="submit">تحديث</button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg bg-nova-warm px-3 py-2 text-sm font-black text-nova-danger" type="submit">حذف</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <aside class="border-t border-[#E0D8CE] bg-nova-warm p-5 lg:border-r lg:border-t-0">
                        <h2 class="text-xl font-black">ملخص الطلب</h2>
                        <div class="mt-5 grid gap-3 text-sm">
                            <div class="flex justify-between"><span>الإجمالي الفرعي</span><strong>{{ number_format($subtotal, 2) }} ر.س</strong></div>
                            <div class="flex justify-between text-nova-muted"><span>الشحن</span><span>يتم اختياره في الدفع</span></div>
                            <div class="flex justify-between text-nova-muted"><span>الكوبون</span><span>متاح في صفحة الدفع</span></div>
                            <div class="flex justify-between pt-4 nova-total-row"><span>الإجمالي المتوقع</span><strong>{{ number_format($subtotal, 2) }} ر.س</strong></div>
                        </div>
                        <div class="mt-5">
                            @auth
                                <a href="{{ route('checkout.show') }}" class="block rounded-lg px-5 py-3 text-center text-sm font-black nova-btn-primary">إكمال الشراء</a>
                            @else
                                <a href="{{ route('login') }}" class="block rounded-lg px-5 py-3 text-center text-sm font-black nova-btn-dark">سجل الدخول للشراء</a>
                            @endauth
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection
