<?php

namespace Webkul\Purchase\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages;

use Webkul\Product\Filament\Resources\CategoryResource\Pages\ListCategories;
use Webkul\Purchase\Filament\Clusters\Configurations\Resources\ProductCategoryResource;

class ListProductCategories extends ListCategories
{
    protected static string $resource = ProductCategoryResource::class;
}
