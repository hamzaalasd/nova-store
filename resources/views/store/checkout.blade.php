@extends('layouts.storefront')

@section('title', 'إكمال الشراء | NOVA')

@section('content')
    @php
        $subtotal = $cart->items->sum(fn ($item) => (float) $item->unit_price * $item->quantity);
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <div class="nova-eyebrow">Checkout</div>
            <h1 class="mt-2 text-3xl font-black">إكمال الشراء</h1>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <form method="POST" action="{{ route('checkout.store') }}" class="rounded-xl p-5 nova-panel">
                @csrf
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-black">بيانات العميل والشحن</h2>
                    <span class="text-xs font-black text-nova-copper">خطوة آمنة</span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-2 text-sm font-black">
                        الاسم
                        <input name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                    <label class="grid gap-2 text-sm font-black">
                        البريد
                        <input name="customer_email" value="{{ old('customer_email', auth()->user()->email) }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                    <label class="grid gap-2 text-sm font-black">
                        الجوال
                        <input name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone) }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                    <label class="grid gap-2 text-sm font-black">
                        المدينة
                        <input name="shipping_address[city]" value="{{ old('shipping_address.city') }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                    <label class="grid gap-2 text-sm font-black">
                        الحي
                        <input name="shipping_address[district]" value="{{ old('shipping_address.district') }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                    <label class="grid gap-2 text-sm font-black">
                        الشارع
                        <input name="shipping_address[street]" value="{{ old('shipping_address.street') }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                    <label class="grid gap-2 text-sm font-black">
                        رقم المبنى
                        <input name="shipping_address[building_number]" value="{{ old('shipping_address.building_number') }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                    <label class="grid gap-2 text-sm font-black">
                        الرمز البريدي
                        <input name="shipping_address[postal_code]" value="{{ old('shipping_address.postal_code') }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                </div>

                <div class="mt-6 grid gap-5 lg:grid-cols-2">
                    <div>
                        <h3 class="mb-3 text-sm font-black">طريقة الشحن</h3>
                        <div class="grid gap-2">
                            @forelse($shippingMethods as $method)
                                <label class="flex cursor-pointer items-center justify-between rounded-lg border border-[#E0D8CE] bg-white p-3 text-sm">
                                    <span>
                                        <span class="block font-black">{{ $method->name_ar }}</span>
                                        <span class="text-xs text-nova-muted">{{ $method->estimated_days_min }}-{{ $method->estimated_days_max }} أيام</span>
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <span class="font-black">{{ (float) $method->price > 0 ? number_format((float) $method->price, 2).' ر.س' : 'مجاني' }}</span>
                                        <input type="radio" name="shipping_method_id" value="{{ $method->id }}" @checked((string) old('shipping_method_id') === (string) $method->id || ($loop->first && ! old('shipping_method_id')))>
                                    </span>
                                </label>
                            @empty
                                <div class="rounded-lg border border-[#E0D8CE] bg-white p-3 text-sm text-nova-muted">لا توجد طرق شحن مفعلة.</div>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-3 text-sm font-black">طريقة الدفع</h3>
                        <div class="grid gap-2">
                            @forelse($paymentMethods as $method)
                                <label class="flex cursor-pointer items-center justify-between rounded-lg border border-[#E0D8CE] bg-white p-3 text-sm">
                                    <span>
                                        <span class="block font-black">{{ $method->name_ar }}</span>
                                        <span class="text-xs text-nova-muted">{{ $method->description_ar ?: $method->type }}</span>
                                    </span>
                                    <input type="radio" name="payment_method_id" value="{{ $method->id }}" @checked((string) old('payment_method_id') === (string) $method->id || ($loop->first && ! old('payment_method_id')))>
                                </label>
                            @empty
                                <div class="rounded-lg border border-[#E0D8CE] bg-white p-3 text-sm text-nova-muted">لا توجد طرق دفع مفعلة.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-2 text-sm font-black">
                        كود الخصم
                        <input name="coupon_code" value="{{ old('coupon_code') }}" placeholder="{{ $activeCoupons->first()?->code ?: 'مثال: NOVA10' }}" class="rounded-lg px-4 py-3 font-normal nova-input">
                    </label>
                    <label class="grid gap-2 text-sm font-black">
                        ملاحظات الطلب
                        <textarea name="customer_notes" rows="3" class="rounded-lg px-4 py-3 font-normal nova-input">{{ old('customer_notes') }}</textarea>
                    </label>
                    <label class="grid gap-2 text-sm font-black sm:col-span-2">
                        ملاحظات العنوان
                        <textarea name="shipping_address[notes]" rows="3" class="rounded-lg px-4 py-3 font-normal nova-input">{{ old('shipping_address.notes') }}</textarea>
                    </label>
                </div>

                <button class="mt-6 w-full rounded-lg px-5 py-3 text-sm font-black nova-btn-primary" type="submit">تأكيد الطلب</button>
            </form>

            <aside class="h-fit rounded-xl p-5 nova-panel">
                <h2 class="text-xl font-black">ملخص الطلب</h2>
                <div class="mt-5 grid gap-3">
                    @foreach($cart->items as $item)
                        <div class="flex justify-between gap-4 text-sm">
                            <span>{{ $item->product->name_ar }} × {{ $item->quantity }}</span>
                            <strong>{{ number_format((float) $item->unit_price * $item->quantity, 2) }} ر.س</strong>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 grid gap-3 border-t border-[#E0D8CE] pt-5 text-sm">
                    <div class="flex justify-between"><span>الإجمالي الفرعي</span><strong>{{ number_format($subtotal, 2) }} ر.س</strong></div>
                    <div class="flex justify-between text-nova-muted"><span>الشحن</span><span>حسب الاختيار</span></div>
                    <div class="flex justify-between text-nova-muted"><span>الخصم</span><span>حسب الكوبون</span></div>
                    <div class="flex justify-between pt-4 nova-total-row"><span>الإجمالي</span><strong>{{ number_format($subtotal, 2) }} ر.س</strong></div>
                </div>
                @if($activeCoupons->isNotEmpty())
                    <div class="mt-5 rounded-lg bg-white p-4 text-sm">
                        <div class="font-black">كوبونات متاحة</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($activeCoupons as $coupon)
                                <span class="rounded-md bg-nova-warm px-2 py-1 text-xs font-black text-nova-violet">{{ $coupon->code }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                <p class="mt-4 text-xs leading-6 text-nova-muted">يتم إنشاء Payment بحالة pending داخل النظام، ويظل الربط مع بوابة الدفع جاهزاً للتفعيل.</p>
            </aside>
        </div>
    </section>
@endsection
