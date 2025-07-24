<?php

namespace App\Filament\Resources\KofolEntryResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;
use App\Models\Brand;
use App\Models\KofolEntry;
use Filament\Tables\Columns\TextColumn;

class KofolProductTable extends BaseWidget
{
    protected static ?string $heading = 'Kofol Products';

    public function table(Table $table): Table
    {
        // Get all Kofol products (query builder)
        $kofolBrandId = Brand::where('name', 'Kofol')->value('id');
        $productQuery = Product::query()->where('brand_id', $kofolBrandId);

        // Precompute total quantity for each product from approved KofolEntries
        $totals = [];
        $entries = KofolEntry::where('status', 'Approved')->get(['products']);
        foreach ($entries as $entry) {
            foreach ($entry->products as $product) {
                $productId = (string) $product['product_id'];
                $qty = (int) ($product['quantity'] ?? 0);
                if (!isset($totals[$productId])) {
                    $totals[$productId] = 0;
                }
                $totals[$productId] += $qty;
            }
        }

        return $table
            ->paginated(false)
            ->defaultSort('name', 'asc')
            ->query($productQuery)
            ->columns([
                TextColumn::make('name')->label('Name'),
                TextColumn::make('total_quantity')
                    ->label('Qty')
                    ->getStateUsing(function ($record) use ($totals) {
                        return $totals[$record->id] ?? 0;
                    }),
            ]);
    }
}
