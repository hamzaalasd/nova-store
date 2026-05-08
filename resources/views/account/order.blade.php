@extends('layouts.storefront')

@section('title', 'طلب '.$order->order_number.' | NOVA')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('orders.index') }}" class="nova-link text-sm">العودة للطلبات</a>
        <div class="mt-4 rounded-lg bg-nova-ink p-6 text-white shadow-xl shadow-black/15">
            <h1 class="text-3xl font-black">{{ $order->order_number }}</h1>
            <p class="mt-2 text-sm text-white/70">الحالة: {{ $order->order_status }} | الدفع: {{ $order->payment_status }}</p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="rounded-lg p-5 nova-panel">
                <h2 class="text-xl font-black">المنتجات</h2>
                <div class="mt-5 grid gap-3">
                    @foreach($order->items as $item)
                        <div class="grid gap-2 rounded-lg border border-black/10 bg-white p-4 sm:grid-cols-4">
                            <strong>{{ $item->product_name_ar }}</strong>
                            <span class="text-sm text-nova-muted">{{ $item->sku }}</span>
                            <span class="text-sm">الكمية: {{ $item->quantity }}</span>
                            <span class="text-sm font-black">{{ $item->total_base }} ر.س</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="h-fit rounded-lg p-5 nova-panel">
                <h2 class="text-xl font-black">الملخص</h2>
                <div class="mt-5 grid gap-3 text-sm">
                    <div class="flex justify-between"><span>الإجمالي الفرعي</span><strong>{{ $order->subtotal_base }} ر.س</strong></div>
                    <div class="flex justify-between"><span>الشحن</span><strong>{{ $order->shipping_base }} ر.س</strong></div>
                    <div class="flex justify-between"><span>الخصم</span><strong>{{ $order->discount_base }} ر.س</strong></div>
                </div>
                <div class="mt-5 border-t border-black/10 pt-5 text-lg font-black">الإجمالي: {{ $order->total_base }} ر.س</div>
                <div class="mt-6 rounded-lg bg-nova-surface p-4 text-sm leading-7 text-nova-muted">
                    {{ data_get($order->shipping_address_snapshot, 'city') }}
                    {{ data_get($order->shipping_address_snapshot, 'district') }}
                    {{ data_get($order->shipping_address_snapshot, 'street') }}
                </div>
            </aside>
        </div>
    </section>
@endsection


