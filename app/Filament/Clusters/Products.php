<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Products extends Cluster
{
    protected static ?string $navigationIcon = 'healthicons-o-pill-1';

    // protected static ?string $navigationGroup = 'Product';
    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 1;

    // protected static ?string $slug = 'shop/products';

}
