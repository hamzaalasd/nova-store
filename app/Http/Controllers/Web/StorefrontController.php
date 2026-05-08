<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Review;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home()
    {
        $featuredProducts = Product::query()
            ->visible()
            ->with(['category', 'images'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::query()
                ->visible()
                ->with(['category', 'images'])
                ->withAvg('approvedReviews', 'rating')
                ->withCount('approvedReviews')
                ->latest()
                ->take(8)
                ->get();
        }

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['products' => fn ($query) => $query->visible()])
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $heroBanner = Banner::query()
            ->active()
            ->where('position', 'hero')
            ->orderBy('sort_order')
            ->first();

        $offerProducts = Product::query()
            ->visible()
            ->with(['category', 'images'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->whereNotNull('sale_price')
            ->latest()
            ->take(4)
            ->get();

        $stats = [
            'customers' => max(12000, Order::query()->distinct('user_id')->count('user_id')),
            'products' => Product::query()->visible()->count(),
            'rating' => round((float) (Review::query()->approved()->avg('rating') ?: 4.9), 1),
        ];

        return view('store.home', [
            'featuredProducts' => $featuredProducts,
            'offerProducts' => $offerProducts,
            'categories' => $categories,
            'heroBanner' => $heroBanner,
            'activeCoupons' => Coupon::query()->active()->latest()->take(3)->get(),
            'shippingMethods' => ShippingMethod::query()->where('is_active', true)->orderBy('sort_order')->take(4)->get(),
            'paymentMethods' => PaymentMethod::query()->where('is_active', true)->orderBy('sort_order')->take(4)->get(),
            'stats' => $stats,
        ]);
    }

    public function products(Request $request)
    {
        $products = Product::query()
            ->visible()
            ->with(['category', 'images'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('category', fn ($query) => $query->where('slug', $request->input('category')));
            })
            ->when($request->filled('stock'), function ($query) use ($request): void {
                $query->where('stock_status', $request->input('stock'));
            })
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = trim((string) $request->input('q'));
                $query->where(function ($query) use ($search): void {
                    $query->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description_ar', 'like', "%{$search}%")
                        ->orWhere('short_description_en', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('min'), fn ($query) => $query->where('base_price', '>=', $request->input('min')))
            ->when($request->filled('max'), fn ($query) => $query->where('base_price', '<=', $request->input('max')));

        match ($request->input('sort', 'newest')) {
            'price_low' => $products->orderBy('base_price'),
            'price_high' => $products->orderByDesc('base_price'),
            'rating' => $products->orderByDesc('approved_reviews_avg_rating'),
            'featured' => $products->orderByDesc('is_featured')->latest(),
            default => $products->latest(),
        };

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('store.products', [
            'products' => $products->paginate(12)->withQueryString(),
            'categories' => $categories,
            'activeCoupons' => Coupon::query()->active()->latest()->take(2)->get(),
        ]);
    }

    public function product(Product $product)
    {
        abort_unless($product->status === 'active', 404);

        $product->load([
            'category',
            'images',
            'specifications',
            'approvedReviews.user',
            'variants' => fn ($query) => $query->where('is_active', true),
        ]);

        $product->loadAvg('approvedReviews', 'rating');
        $product->loadCount('approvedReviews');

        $relatedProducts = Product::query()
            ->visible()
            ->with(['images', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'relatedProducts'));
    }

    public function storeReview(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $hasPurchased = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) use ($request): void {
                $query->where('user_id', $request->user()->id)
                    ->whereIn('order_status', ['confirmed', 'processing', 'ready_to_ship', 'shipped', 'delivered']);
            })
            ->exists();

        if (! $hasPurchased) {
            return back()->withErrors(['review' => 'يمكن تقييم المنتج بعد شرائه فقط.']);
        }

        Review::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => $request->user()->id],
            [
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'status' => 'pending',
            ]
        );

        return back()->with('status', 'تم إرسال تقييمك للمراجعة.');
    }
}
