<?php

namespace App\Filament\Admin\Resources\Currencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name_ar')
                    ->required(),
                TextInput::make('name_en')
                    ->required(),
                TextInput::make('symbol_ar')
                    ->required(),
                TextInput::make('symbol_en')
                    ->required(),
                TextInput::make('exchange_rate')
                    ->required()
                    ->numeric()
                    ->default(1),
                Toggle::make('is_default')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('decimal_places')
                    ->required()
                    ->numeric()
                    ->default(2),
                TextInput::make('symbol_position')
                    ->required()
                    ->default('before'),
                TextInput::make('rounding_mode')
                    ->required()
                    ->default('none'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
