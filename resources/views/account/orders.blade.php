@extends('layouts.storefront')

@section('title', 'طلباتي | NOVA')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black">طلباتي</h1>

        <div class="mt-8 overflow-hidden rounded-lg border border-black/10 bg-white shadow-sm">
            @forelse($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="grid gap-2 border-b border-black/10 p-4 transition hover:bg-nova-surface sm:grid-cols-5">
                    <strong>{{ $order->order_number }}</strong>
                    <span class="text-sm text-nova-muted">{{ $order->created_at->format('Y-m-d') }}</span>
                    <span class="text-sm">{{ $order->order_status }}</span>
                    <span class="text-sm">{{ $order->payment_status }}</span>
                    <span class="text-sm font-black">{{ $order->total_base }} ر.س</span>
                </a>
            @empty
                <div class="p-10 text-center text-sm text-nova-muted">لا توجد طلبات بعد.</div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </section>
@endsection


