<?php

namespace App\Filament\Admin\Resources\HomeBanners;

use App\Filament\Admin\Resources\HomeBanners\Pages\CreateHomeBanner;
use App\Filament\Admin\Resources\HomeBanners\Pages\EditHomeBanner;
use App\Filament\Admin\Resources\HomeBanners\Pages\ListHomeBanners;
use App\Filament\Admin\Resources\HomeBanners\Schemas\HomeBannerForm;
use App\Filament\Admin\Resources\HomeBanners\Tables\HomeBannersTable;
use App\Models\HomeBanner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class HomeBannerResource extends Resource
{
    protected static ?string $model = HomeBanner::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    public static function getNavigationGroup(): ?string
    {
        return 'التسويق والمحتوى';
    }

    public static function getNavigationLabel(): string
    {
        return 'سلايدر الرئيسية';
    }

    public static function getModelLabel(): string
    {
        return 'بنر';
    }

    public static function getPluralModelLabel(): string
    {
        return 'بنرات الرئيسية';
    }

    public static function form(Schema $schema): Schema
    {
        return HomeBannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeBannersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomeBanners::route('/'),
            'create' => CreateHomeBanner::route('/create'),
            'edit' => EditHomeBanner::route('/{record}/edit'),
        ];
    }
}
