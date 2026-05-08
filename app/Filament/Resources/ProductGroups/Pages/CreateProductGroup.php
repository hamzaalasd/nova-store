<?php

namespace App\Filament\Resources\ProductGroups\Pages;

use App\Filament\Resources\ProductGroups\ProductGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductGroup extends CreateRecord
{
    protected static string $resource = ProductGroupResource::class;

    public function getTitle(): string
    {
        return 'إضافة مجموعة منتجات';
    }

    protected function getCreatedRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
