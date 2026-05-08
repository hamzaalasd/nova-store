<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'مبيعات آخر 14 يوم';

    protected ?string $description = 'اتجاه الإيرادات اليومية حسب تاريخ إنشاء الطلب.';

    protected string $color = 'success';

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn (int $daysAgo) => now()->subDays($daysAgo)->startOfDay());

        $values = $days->map(function ($day): float {
            return (float) Order::query()
                ->whereBetween('created_at', [$day, $day->copy()->endOfDay()])
                ->sum('total_base');
        });

        return [
            'datasets' => [
                [
                    'label' => 'المبيعات',
                    'data' => $values->all(),
                    'fill' => true,
                ],
            ],
            'labels' => $days->map(fn ($day) => $day->format('m-d'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
