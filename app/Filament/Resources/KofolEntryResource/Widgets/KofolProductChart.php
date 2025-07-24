<?php

namespace App\Filament\Resources\KofolEntryResource\Widgets;

use Filament\Widgets\ChartWidget;

class KofolProductChart extends ChartWidget
{
    // protected static ?string $heading = 'Kofol Products';

    protected static string $color = 'primary';

    protected static ?string $pollingInterval = '60s';

    // protected static ?string $maxHeight = '900px';

    protected function getData(): array
    {
        // Get all Kofol products
        $kofolBrandId = \App\Models\Brand::where('name', 'Kofol')->value('id');
        $kofolProducts = \App\Models\Product::where('brand_id', $kofolBrandId)->get();

        // Pre-calculate total quantity for each product from approved KofolEntries
        $totals = [];
        $entries = \App\Models\KofolEntry::where('status', 'Approved')->get();
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

        // Prepare chart data
        $labels = $kofolProducts->pluck('name')->toArray();
        $data = $kofolProducts->map(function ($product) use ($totals) {
            return $totals[$product->id] ?? 0;
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Quantity',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
