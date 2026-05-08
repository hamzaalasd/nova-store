<?php

namespace App\Filament\Admin\Resources\HomeBanners\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('محتوى البنر')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title_ar')
                                ->label('العنوان بالعربية')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('title_en')
                                ->label('العنوان بالإنجليزية')
                                ->maxLength(255),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('badge_ar')
                                ->label('شارة صغيرة بالعربية')
                                ->placeholder('مثال: مجموعة NOVA الجديدة')
                                ->maxLength(255),
                            TextInput::make('badge_en')
                                ->label('شارة صغيرة بالإنجليزية')
                                ->maxLength(255),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('subtitle_ar')
                                ->label('الوصف بالعربية')
                                ->rows(3),
                            Textarea::make('subtitle_en')
                                ->label('الوصف بالإنجليزية')
                                ->rows(3),
                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('الصورة والتصميم')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('صورة البنر')
                            ->disk('public')
                            ->directory('home-banners')
                            ->image()
                            ->imageEditor()
                            ->helperText('يفضل مقاس قريب من 1600x900 أو 1200x700 حتى يظهر كمستطيل أنيق في التطبيق.')
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            ColorPicker::make('background_color')
                                ->label('لون الخلفية')
                                ->default('#2D2438')
                                ->required(),
                            ColorPicker::make('accent_color')
                                ->label('لون التمييز')
                                ->default('#B8965A')
                                ->required(),
                        ]),
                        Toggle::make('show_text_overlay')
                            ->label('إظهار النصوص والزر فوق الصورة')
                            ->helperText('أغلقه إذا كانت صورة البنر مصممة بالكامل وتحتوي النص والزر داخل التصميم.')
                            ->default(true),
                    ])
                    ->columnSpanFull(),

                Section::make('الزر والرابط')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('button_label_ar')
                                ->label('نص الزر بالعربية')
                                ->default('اكتشف المنتجات')
                                ->maxLength(255),
                            TextInput::make('button_label_en')
                                ->label('نص الزر بالإنجليزية')
                                ->default('Explore products')
                                ->maxLength(255),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('link_type')
                                ->label('نوع الرابط')
                                ->options([
                                    'none' => 'بدون رابط',
                                    'products' => 'كل المنتجات',
                                    'category' => 'تصنيف',
                                    'product' => 'منتج',
                                    'external' => 'رابط خارجي',
                                ])
                                ->default('products')
                                ->required(),
                            TextInput::make('link_value')
                                ->label('قيمة الرابط')
                                ->helperText('للتصنيف اكتب ID التصنيف، وللمنتج اكتب slug المنتج، وللرابط الخارجي اكتب الرابط كاملاً. اتركه فارغاً عند اختيار كل المنتجات.'),
                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('النشر والترتيب')
                    ->schema([
                        Grid::make(4)->schema([
                            Toggle::make('is_active')
                                ->label('نشط')
                                ->default(true),
                            TextInput::make('sort_order')
                                ->label('الترتيب')
                                ->numeric()
                                ->default(0)
                                ->required(),
                            DateTimePicker::make('starts_at')
                                ->label('يبدأ في'),
                            DateTimePicker::make('ends_at')
                                ->label('ينتهي في'),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
