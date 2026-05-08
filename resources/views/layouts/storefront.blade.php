<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'NOVA Store')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-nova-surface font-sans text-nova-ink antialiased">
    <div class="min-h-screen">
        <header class="sticky top-0 z-40 border-b border-[#3b3048] nova-nav shadow-lg shadow-[#2D2438]/10">
            <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3" aria-label="NOVA">
                    <span class="text-xl font-black tracking-[0.18em] text-nova-gold-soft">NOVA</span>
                </a>

                <nav class="hidden items-center gap-5 lg:flex">
                    <a class="nova-nav-link {{ request()->routeIs('home') ? 'nova-nav-link-active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
                    <a class="nova-nav-link {{ request()->routeIs('products.*') ? 'nova-nav-link-active' : '' }}" href="{{ route('products.index') }}">المتجر</a>
                    @foreach($navCategories->take(4) as $category)
                        <a class="nova-nav-link" href="{{ route('products.index', ['category' => $category->slug]) }}">{{ $category->name_ar }}</a>
                    @endforeach
                </nav>

                <form method="GET" action="{{ route('products.index') }}" class="mr-auto hidden min-w-[220px] items-center gap-2 rounded-lg border border-nova-gold-soft/20 bg-white/5 px-3 py-2 text-sm lg:flex">
                    <span class="text-nova-gold-soft">⌕</span>
                    <input name="q" value="{{ request('q') }}" class="w-full bg-transparent text-white outline-none placeholder:text-white/35" placeholder="ابحث عن منتج...">
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('products.index', ['sort' => 'featured']) }}" class="nova-icon-button hidden sm:inline-grid" title="العروض">%</a>
                    <a href="{{ route('cart.show') }}" class="nova-icon-button relative" title="السلة">
                        <span>سلة</span>
                        @if($cartCount > 0)
                            <span class="absolute -left-1.5 -top-1.5 grid min-w-5 place-items-center rounded-full bg-nova-gold px-1 text-[10px] font-black text-nova-ink">{{ $cartCount }}</span>
                        @endif
                    </a>
                    @auth
                        <a href="{{ route('account.dashboard') }}" class="nova-icon-button" title="حسابي">حساب</a>
                    @else
                        <a href="{{ route('login') }}" class="nova-icon-button" title="تسجيل الدخول">دخول</a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            @if(session('status'))
                <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                    <div class="rounded-lg border border-nova-gold/30 bg-nova-warm px-4 py-3 text-sm font-bold text-nova-violet">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                    <div class="rounded-lg border border-nova-danger/20 bg-nova-warm px-4 py-3 text-sm font-bold text-nova-danger">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="mt-20 border-t border-white/10 bg-nova-ink text-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.2fr_0.8fr_0.8fr] lg:px-8">
                <div>
                    <div class="text-2xl font-black tracking-[0.18em] text-nova-gold-soft">NOVA</div>
                    <p class="mt-3 max-w-md text-sm leading-7 text-white/65">متجر إلكتروني بتجربة شراء واضحة، منتجات منظمة، سلة ذكية، طلبات قابلة للتتبع، وربط جاهز مع الشحن والدفع والكوبونات.</p>
                </div>
                <div>
                    <div class="text-sm font-black text-white">التصفح</div>
                    <div class="mt-4 grid gap-3 text-sm text-white/65">
                        <a class="transition hover:text-nova-gold-soft" href="{{ route('products.index') }}">كل المنتجات</a>
                        <a class="transition hover:text-nova-gold-soft" href="{{ route('cart.show') }}">السلة</a>
                        <a class="transition hover:text-nova-gold-soft" href="{{ route('account.dashboard') }}">حسابي</a>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-black text-white">التجربة</div>
                    <p class="mt-4 text-sm leading-7 text-white/65">الواجهة مرتبطة بالمنتجات، التصنيفات، العروض، الكوبونات، طرق الدفع، طرق الشحن، والطلبات الموجودة في Laravel.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
