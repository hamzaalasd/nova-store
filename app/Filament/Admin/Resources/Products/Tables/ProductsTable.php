<?php

namespace App\Filament\Admin\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('main_image')
                    ->label('الصورة')
                    ->square(),
                TextColumn::make('name_ar')
                    ->label('المنتج')
                    ->description(fn ($record): string => $record->sku)
                    ->searchable(['name_ar', 'name_en', 'sku'])
                    ->sortable(),
                TextColumn::make('category.name_ar')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('base_price')
                    ->label('السعر')
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('العرض')
                    ->money('SAR')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->label('المخزون')
                    ->badge()
                    ->color(fn ($record): string => $record->stock_quantity !== null && $record->low_stock_threshold !== null && $record->stock_quantity <= $record->low_stock_threshold ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('النشر')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'draft' => 'gray',
                        'inactive' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('stock_status')
                    ->label('حالة المخزون')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_stock' => 'success',
                        'pre_order' => 'warning',
                        'out_of_stock' => 'danger',
                        default => 'gray',
                    }),
                IconColumn::make('is_featured')
                    ->label('مميز')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('أضيف')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('حالة النشر')
                    ->options([
                        'draft' => 'مسودة',
                        'active' => 'نشط',
                        'inactive' => 'مخفي',
                        'archived' => 'مؤرشف',
                    ]),
                SelectFilter::make('stock_status')
                    ->label('المخزون')
                    ->options([
                        'in_stock' => 'متوفر',
                        'out_of_stock' => 'غير متوفر',
                        'pre_order' => 'طلب مسبق',
                    ]),
                SelectFilter::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name_ar')
                    ->searchable()
                    ->preload(),
                Filter::make('low_stock')
                    ->label('مخزون منخفض')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('manage_stock', true)
                        ->whereNotNull('stock_quantity')
                        ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')),
                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make()->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف المحدد'),
                    RestoreBulkAction::make()->label('استعادة'),
                    ForceDeleteBulkAction::make()->label('حذف نهائي'),
                ]),
            ]);
    }
}
