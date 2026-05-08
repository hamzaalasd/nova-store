<?php

namespace App\Filament\Admin\Resources\ProductGroups;

use App\Filament\Admin\Resources\ProductGroups\Pages\CreateProductGroup;
use App\Filament\Admin\Resources\ProductGroups\Pages\EditProductGroup;
use App\Filament\Admin\Resources\ProductGroups\Pages\ListProductGroups;
use App\Filament\Admin\Resources\ProductGroups\Schemas\ProductGroupForm;
use App\Filament\Admin\Resources\ProductGroups\Tables\ProductGroupsTable;
use App\Models\ProductGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductGroupResource extends Resource
{
    protected static ?string $model = ProductGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProductGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductGroups::route('/'),
            'create' => CreateProductGroup::route('/create'),
            'edit' => EditProductGroup::route('/{record}/edit'),
        ];
    }
}
