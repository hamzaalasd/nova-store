@extends('layouts.storefront')

@section('title', 'حسابي | NOVA')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-nova-ink p-6 text-white shadow-xl shadow-black/15 sm:p-8">
            <h1 class="text-3xl font-black">حسابي</h1>
            <p class="mt-3 text-sm text-white/70">مرحباً {{ auth()->user()->name }}، هنا ملخص طلباتك وحسابك.</p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[280px_1fr]">
            <aside class="h-fit rounded-lg p-4 nova-panel">
                <a href="{{ route('account.dashboard') }}" class="block rounded-lg bg-nova-surface px-4 py-3 text-sm font-black">لوحة الحساب</a>
                <a href="{{ route('orders.index') }}" class="mt-2 block rounded-lg px-4 py-3 text-sm font-black transition hover:bg-nova-surface">طلباتي</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="w-full rounded-lg px-4 py-3 text-right text-sm font-black text-nova-danger transition hover:bg-[#F0EDE8]" type="submit">تسجيل الخروج</button>
                </form>
            </aside>

            <div class="rounded-lg p-5 nova-panel">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black">آخر الطلبات</h2>
                    <a href="{{ route('orders.index') }}" class="nova-link text-sm">كل الطلبات</a>
                </div>
                <div class="mt-5 grid gap-3">
                    @forelse($orders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="grid gap-2 rounded-lg border border-black/10 bg-white p-4 transition hover:border-nova-emerald sm:grid-cols-4">
                            <strong>{{ $order->order_number }}</strong>
                            <span class="text-sm text-nova-muted">{{ $order->items_count }} منتجات</span>
                            <span class="text-sm font-black">{{ $order->total_base }} ر.س</span>
                            <span class="text-sm font-black text-nova-emerald">{{ $order->order_status }}</span>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-black/20 bg-white p-8 text-center text-sm text-nova-muted">لا توجد طلبات بعد.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection


