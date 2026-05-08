@extends('layouts.storefront')

@section('title', 'تسجيل الدخول | NOVA')

@section('content')
    <section class="mx-auto grid min-h-[70vh] max-w-7xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div>
            <h1 class="text-4xl font-black">أهلاً بعودتك</h1>
            <p class="mt-4 max-w-md text-base leading-8 text-nova-muted">ادخل لحسابك لإكمال الشراء ومتابعة الطلبات.</p>
        </div>
        <form method="POST" action="{{ route('login.store') }}" class="rounded-lg p-6 nova-panel">
            @csrf
            <label class="grid gap-2 text-sm font-black">
                البريد الإلكتروني
                <input name="email" value="{{ old('email') }}" type="email" class="rounded-lg px-4 py-3 font-normal nova-input">
            </label>
            <label class="mt-4 grid gap-2 text-sm font-black">
                كلمة المرور
                <input name="password" type="password" class="rounded-lg px-4 py-3 font-normal nova-input">
            </label>
            <label class="mt-4 flex items-center gap-2 text-sm font-black">
                <input name="remember" type="checkbox" class="size-4 rounded border-black/20 text-nova-emerald">
                تذكرني
            </label>
            <button class="mt-6 w-full rounded-lg px-5 py-3 text-sm font-black nova-btn-dark" type="submit">دخول</button>
            <p class="mt-5 text-center text-sm text-nova-muted">ليس لديك حساب؟ <a class="font-black text-nova-copper" href="{{ route('register') }}">إنشاء حساب</a></p>
        </form>
    </section>
@endsection


