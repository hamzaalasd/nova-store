<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_group_id')
                    ->label('المجموعة')
                    ->relationship('productGroup', 'name_ar')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                Select::make('parent_id')
                    ->label('التصنيف الأب')
                    ->options(function (callable $get, ?\App\Models\Category $record) {
                        $productGroupId = $get('product_group_id');
                        if (! $productGroupId) {
                            return [];
                        }
                        return \App\Models\Category::query()
                            ->where('product_group_id', $productGroupId)
                            ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                            ->orderBy('sort_order')
                            ->pluck('name_ar', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('اختياري. استخدمه إذا أردت تصنيفًا فرعيًا داخل تصنيف رئيسي.')
                    ->disabled(fn (callable $get) => ! $get('product_group_id')),

                TextInput::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->required()
                    ->maxLength(255),

                TextInput::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->label('الرابط')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('يفضل كتابته بالإنجليزية مثل: solar-panels أو smart-screens.'),

                Textarea::make('description_ar')
                    ->label('الوصف بالعربية')
                    ->rows(4),

                Textarea::make('description_en')
                    ->label('الوصف بالإنجليزية')
                    ->rows(4),

                FileUpload::make('image')
                    ->label('صورة التصنيف')
                    ->disk('public')
                    ->image()
                    ->directory('categories')
                    ->imageEditor(),

                FileUpload::make('banner_image')
                    ->label('بنر التصنيف')
                    ->disk('public')
                    ->image()
                    ->directory('categories/banners')
                    ->imageEditor(),

                Toggle::make('is_active')
                    ->label('مفعل')
                    ->default(true),

                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),

                TextInput::make('seo_title_ar')
                    ->label('عنوان SEO بالعربية')
                    ->maxLength(255),

                TextInput::make('seo_title_en')
                    ->label('عنوان SEO بالإنجليزية')
                    ->maxLength(255),

                Textarea::make('seo_description_ar')
                    ->label('وصف SEO بالعربية')
                    ->rows(3),

                Textarea::make('seo_description_en')
                    ->label('وصف SEO بالإنجليزية')
                    ->rows(3),
            ]);
    }
}
