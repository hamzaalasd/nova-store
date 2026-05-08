<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrdersWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('آخر الطلبات')
            ->query(fn (): Builder => Order::query()->latest()->limit(8))
            ->columns([
                TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('العميل')
                    ->searchable(),
                TextColumn::make('total_base')
                    ->label('الإجمالي')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('order_status')
                    ->label('حالة الطلب')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'delivered' => 'success',
                        'cancelled', 'returned', 'refunded' => 'danger',
                        'shipped', 'ready_to_ship' => 'info',
                        default => 'warning',
                    }),
                TextColumn::make('payment_status')
                    ->label('الدفع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'failed', 'cancelled' => 'danger',
                        'refunded', 'partially_refunded' => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->since(),
            ]);
    }
}
