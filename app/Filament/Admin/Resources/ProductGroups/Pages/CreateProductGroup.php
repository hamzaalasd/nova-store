<?php

namespace App\Filament\Admin\Resources\ProductGroups\Pages;

use App\Filament\Admin\Resources\ProductGroups\ProductGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductGroup extends CreateRecord
{
    protected static string $resource = ProductGroupResource::class;
}
