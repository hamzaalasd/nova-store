<?php

namespace App\Filament\Resources\ProductGroups\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('الرابط')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description_ar')
                    ->label('الوصف بالعربية')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('description_en')
                    ->label('الوصف بالإنجليزية')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('مفعل')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
                FileUpload::make('image')
                    ->label('صورة المجموعة')
                    ->image()
                    ->directory('product-groups'),
                FileUpload::make('banner_image')
                    ->label('بنر المجموعة')
                    ->image()
                    ->directory('product-groups/banners'),
            ]);
    }
}
