<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'مؤشرات المتجر';

    protected ?string $description = 'قراءة سريعة للمبيعات والطلبات والمخزون.';

    protected function getStats(): array
    {
        $todayRevenue = Order::query()
            ->whereDate('created_at', today())
            ->sum('total_base');

        $monthRevenue = Order::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_base');

        $openOrders = Order::query()
            ->whereNotIn('order_status', ['delivered', 'cancelled', 'returned', 'refunded'])
            ->count();

        $lowStock = Product::query()
            ->where('manage_stock', true)
            ->whereNotNull('stock_quantity')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();

        return [
            Stat::make('مبيعات اليوم', number_format((float) $todayRevenue, 2).' ر.س')
                ->description('إجمالي الطلبات المسجلة اليوم')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('مبيعات الشهر', number_format((float) $monthRevenue, 2).' ر.س')
                ->description('من بداية الشهر الحالي')
                ->color('primary')
                ->icon('heroicon-o-chart-bar-square'),

            Stat::make('طلبات مفتوحة', $openOrders)
                ->description('تحتاج متابعة تشغيلية')
                ->color($openOrders > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('مخزون منخفض', $lowStock)
                ->description('منتجات تحتاج إعادة توريد')
                ->color($lowStock > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('العملاء', User::query()->where('type', 'customer')->count())
                ->description('إجمالي حسابات العملاء')
                ->color('info')
                ->icon('heroicon-o-users'),
        ];
    }
}
