<?php

namespace App\Filament\Exports;

use App\Models\KofolEntryCoupon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class KofolEntryResourceExporter extends Exporter
{
    protected static ?string $model = KofolEntryCoupon::class;

    public static function getEloquentQuery(): Builder
    {
        // Optimized base query mirroring the report page
        return KofolEntryCoupon::query()
            ->select(['id', 'coupon_code', 'kofol_entry_id'])
            ->with([
                'kofolEntry:id,customer_id,user_id,headquarter_id,customer_type,invoice_amount,products',
                'kofolEntry.customer:id,name,email,phone',
                'kofolEntry.user:id,name',
                'kofolEntry.headquarter:id,name,area_id',
                'kofolEntry.headquarter.area:id,name,region_id',
                'kofolEntry.headquarter.area.region:id,name,zone_id',
                'kofolEntry.headquarter.area.region.zone:id,name',
            ]);
    }

    public static function getColumns(): array
    {
        // Cache Kofol products once for the export lifecycle
        $kofolProducts = Cache::remember('export_kofol_products', 86400, function () {
            return \App\Models\Product::whereHas('brand', function ($q) {
                $q->where('name', 'Kofol');
            })
                ->select(['id', 'name'])
                ->get();
        });

        $columns = [
            ExportColumn::make('coupon_code')->label('Coupon No.'),
            ExportColumn::make('kofol_entry_id')->label('KSV ID'),

            // Relationship columns
            ExportColumn::make('kofolEntry.customer.name')->label('Cx'),
            ExportColumn::make('kofolEntry.customer_type')->label('Cx Type'),
            ExportColumn::make('kofolEntry.customer.email')->label('Cx Email'),
            ExportColumn::make('kofolEntry.customer.phone')->label('Cx Phone'),
            ExportColumn::make('kofolEntry.user.name')->label('User'),
            ExportColumn::make('kofolEntry.headquarter.name')->label('HQ'),
            ExportColumn::make('kofolEntry.headquarter.area.name')->label('Area'),
            ExportColumn::make('kofolEntry.headquarter.area.region.name')->label('Region'),
            ExportColumn::make('kofolEntry.headquarter.area.region.zone.name')->label('Zone'),
            ExportColumn::make('kofolEntry.invoice_amount')->label('POB Amount'),
        ];

        // Per-entry product quantities cache to avoid repeated JSON decoding per column
        // Uses static within the closure so it's shared across rows/columns during the process
        foreach ($kofolProducts as $product) {
            $productId = (string) $product->id;
            $productName = $product->name;

            $columns[] = ExportColumn::make('kofolEntry.product_' . $productId . '_qty')
                ->label($productName)
                ->getStateUsing(function ($record) use ($productId) {
                    static $entryProductMap = [];

                    $entry = $record->kofolEntry;
                    if (!$entry) {
                        return '';
                    }

                    if (!array_key_exists($entry->id, $entryProductMap)) {
                        $products = $entry->products ?? [];
                        if (is_string($products)) {
                            $products = json_decode($products, true);
                        }
                        $products = is_array($products) ? $products : [];

                        $map = [];
                        foreach ($products as $item) {
                            if (!isset($item['product_id'])) {
                                continue;
                            }
                            $map[(string) $item['product_id']] = $item['quantity'] ?? '-';
                        }

                        $entryProductMap[$entry->id] = $map;
                    }

                    return $entryProductMap[$entry->id][$productId] ?? '-';
                });
        }

        return $columns;
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your KSV coupon export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public static function getChunkSize(): int
    {
        // Larger chunk size to reduce round-trips; adjust as needed
        return 2000;
    }
}
