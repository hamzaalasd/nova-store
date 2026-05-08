@extends('layouts.storefront')

@section('title', 'إنشاء حساب | NOVA')

@section('content')
    <section class="mx-auto grid min-h-[70vh] max-w-7xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div>
            <h1 class="text-4xl font-black">أنشئ حسابك</h1>
            <p class="mt-4 max-w-md text-base leading-8 text-nova-muted">حساب العميل يربط السلة والطلبات ويجهز تجربة تطبيق Flutter لاحقاً.</p>
        </div>
        <form method="POST" action="{{ route('register.store') }}" class="rounded-lg p-6 nova-panel">
            @csrf
            <label class="grid gap-2 text-sm font-black">
                الاسم
                <input name="name" value="{{ old('name') }}" class="rounded-lg px-4 py-3 font-normal nova-input">
            </label>
            <label class="mt-4 grid gap-2 text-sm font-black">
                البريد الإلكتروني
                <input name="email" value="{{ old('email') }}" type="email" class="rounded-lg px-4 py-3 font-normal nova-input">
            </label>
            <label class="mt-4 grid gap-2 text-sm font-black">
                الجوال
                <input name="phone" value="{{ old('phone') }}" class="rounded-lg px-4 py-3 font-normal nova-input">
            </label>
            <label class="mt-4 grid gap-2 text-sm font-black">
                كلمة المرور
                <input name="password" type="password" class="rounded-lg px-4 py-3 font-normal nova-input">
            </label>
            <label class="mt-4 grid gap-2 text-sm font-black">
                تأكيد كلمة المرور
                <input name="password_confirmation" type="password" class="rounded-lg px-4 py-3 font-normal nova-input">
            </label>
            <button class="mt-6 w-full rounded-lg px-5 py-3 text-sm font-black nova-btn-primary" type="submit">إنشاء الحساب</button>
            <p class="mt-5 text-center text-sm text-nova-muted">لديك حساب؟ <a class="font-black text-nova-copper" href="{{ route('login') }}">تسجيل الدخول</a></p>
        </form>
    </section>
@endsection


