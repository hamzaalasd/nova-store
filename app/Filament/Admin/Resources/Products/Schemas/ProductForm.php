<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الأساسية')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('product_group_id')
                                ->label('مجموعة المنتج')
                                ->relationship('productGroup', 'name_ar')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(),
                            Select::make('category_id')
                                ->label('التصنيف')
                                ->options(fn (callable $get) => Category::query()
                                    ->when($get('product_group_id'), fn ($query, $groupId) => $query->where('product_group_id', $groupId))
                                    ->orderBy('sort_order')
                                    ->pluck('name_ar', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('name_ar')
                                ->label('اسم المنتج بالعربية')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('name_en')
                                ->label('اسم المنتج بالإنجليزية')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set): void {
                                    if ($state) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('slug')
                                ->label('الرابط')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            TextInput::make('sku')
                                ->label('SKU')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                        ]),
                        Textarea::make('short_description_ar')
                            ->label('وصف مختصر بالعربية')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('description_ar')
                            ->label('وصف تفصيلي بالعربية')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('التسعير والمخزون')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('base_price')
                                ->label('السعر الأساسي')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->prefix('SAR'),
                            TextInput::make('sale_price')
                                ->label('سعر العرض')
                                ->numeric()
                                ->nullable()
                                ->minValue(0)
                                ->prefix('SAR'),
                            TextInput::make('cost_price')
                                ->label('التكلفة')
                                ->numeric()
                                ->nullable()
                                ->minValue(0)
                                ->prefix('SAR'),
                        ]),
                        Grid::make(4)->schema([
                            Toggle::make('manage_stock')
                                ->label('إدارة المخزون')
                                ->default(true),
                            TextInput::make('stock_quantity')
                                ->label('الكمية')
                                ->numeric()
                                ->nullable()
                                ->minValue(0),
                            TextInput::make('low_stock_threshold')
                                ->label('حد التنبيه')
                                ->numeric()
                                ->nullable()
                                ->minValue(0),
                            Select::make('stock_status')
                                ->label('حالة المخزون')
                                ->options([
                                    'in_stock' => 'متوفر',
                                    'out_of_stock' => 'غير متوفر',
                                    'pre_order' => 'طلب مسبق',
                                ])
                                ->default('in_stock')
                                ->required(),
                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('النشر والظهور')
                    ->schema([
                        Grid::make(4)->schema([
                            Select::make('status')
                                ->label('حالة المنتج')
                                ->options([
                                    'draft' => 'مسودة',
                                    'active' => 'نشط',
                                    'inactive' => 'مخفي',
                                    'archived' => 'مؤرشف',
                                ])
                                ->default('draft')
                                ->required(),
                            Toggle::make('is_featured')
                                ->label('منتج مميز'),
                            Toggle::make('has_variants')
                                ->label('له خيارات/مقاسات'),
                            DateTimePicker::make('published_at')
                                ->label('تاريخ النشر'),
                        ]),
                        FileUpload::make('main_image')
                            ->label('الصورة الرئيسية')
                            ->disk('public')
                            ->image()
                            ->directory('products')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('الشحن والأبعاد')
                    ->collapsed()
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('weight')->label('الوزن')->numeric()->nullable(),
                            TextInput::make('length')->label('الطول')->numeric()->nullable(),
                            TextInput::make('width')->label('العرض')->numeric()->nullable(),
                            TextInput::make('height')->label('الارتفاع')->numeric()->nullable(),
                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('SEO')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('seo_title_ar')->label('SEO Title عربي')->maxLength(255),
                            TextInput::make('seo_title_en')->label('SEO Title إنجليزي')->maxLength(255),
                        ]),
                        Textarea::make('seo_description_ar')->label('SEO Description عربي')->rows(3),
                        Textarea::make('seo_description_en')->label('SEO Description إنجليزي')->rows(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
