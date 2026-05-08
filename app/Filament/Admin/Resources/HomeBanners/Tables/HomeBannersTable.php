<?php

namespace App\Filament\Admin\Resources\HomeBanners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HomeBannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->disk('public')
                    ->height(54)
                    ->width(96),
                TextColumn::make('title_ar')
                    ->label('العنوان')
                    ->description(fn ($record): string => $record->subtitle_ar ? str($record->subtitle_ar)->limit(55)->toString() : '')
                    ->searchable(['title_ar', 'title_en', 'subtitle_ar'])
                    ->sortable(),
                TextColumn::make('link_type')
                    ->label('الرابط')
                    ->badge(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('يبدأ')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('ينتهي')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('active_now')
                    ->label('ظاهر الآن')
                    ->query(fn (Builder $query): Builder => $query->visible()),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make()->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }
}
