<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('الطلب')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('customer_name')
                    ->label('العميل')
                    ->description(fn ($record): string => $record->customer_phone ?: $record->customer_email ?: '-')
                    ->searchable(['customer_name', 'customer_email', 'customer_phone']),
                TextColumn::make('total_base')
                    ->label('الإجمالي')
                    ->money('SAR')
                    ->sortable(),
                SelectColumn::make('order_status')
                    ->label('حالة الطلب')
                    ->options([
                        'pending_payment' => 'بانتظار الدفع',
                        'pending_bank_review' => 'مراجعة تحويل',
                        'confirmed' => 'مؤكد',
                        'processing' => 'قيد التجهيز',
                        'ready_to_ship' => 'جاهز للشحن',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                        'returned' => 'مرتجع',
                        'refunded' => 'مسترد',
                    ]),
                SelectColumn::make('payment_status')
                    ->label('الدفع')
                    ->options([
                        'unpaid' => 'غير مدفوع',
                        'initiated' => 'بدأ الدفع',
                        'paid' => 'مدفوع',
                        'failed' => 'فشل',
                        'cancelled' => 'ملغي',
                        'refunded' => 'مسترد',
                        'partially_refunded' => 'استرداد جزئي',
                    ]),
                TextColumn::make('items_count')
                    ->label('المنتجات')
                    ->counts('items')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('order_status')
                    ->label('حالة الطلب')
                    ->options([
                        'pending_payment' => 'بانتظار الدفع',
                        'confirmed' => 'مؤكد',
                        'processing' => 'قيد التجهيز',
                        'ready_to_ship' => 'جاهز للشحن',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options([
                        'unpaid' => 'غير مدفوع',
                        'initiated' => 'بدأ الدفع',
                        'paid' => 'مدفوع',
                        'failed' => 'فشل',
                        'refunded' => 'مسترد',
                    ]),
                Filter::make('today')
                    ->label('طلبات اليوم')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),
                Filter::make('needs_attention')
                    ->label('تحتاج متابعة')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereIn('order_status', ['pending_payment', 'pending_bank_review', 'processing'])
                        ->orWhereIn('payment_status', ['unpaid', 'initiated', 'failed'])),
                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make()->label('إدارة'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }
}
