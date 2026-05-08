<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Cart;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        View::composer('layouts.storefront', function ($view): void {
            $cartQuery = Cart::query();

            if (Auth::check()) {
                $cartQuery->where('user_id', Auth::id());
            } elseif (session()->isStarted()) {
                $cartQuery->where('session_id', session()->getId());
            }

            $cartCount = $cartQuery->with('items')->first()?->items->sum('quantity') ?? 0;

            $view->with([
                'navCategories' => Category::query()
                    ->where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->take(6)
                    ->get(),
                'cartCount' => $cartCount,
            ]);
        });
    }
}
