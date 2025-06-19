<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'decimal:0,2']),
            ImportColumn::make('division_id')
                ->label('Division')
                ->requiredMapping()
                ->rules(['required'])
                ->examples(['Pharma', 'Phytonova'])
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
            ImportColumn::make('brand_id')
                ->label('Brand')
                ->requiredMapping()
                ->rules(['required'])
                ->examples(['Kofol', 'Moha'])
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
        ];
    }

    public function resolveRecord(): ?Product
    {
        $product = new Product;
        $product->name = $this->data['name'] ?? null;
        $product->price = $this->data['price'] ?? null;

        // Resolve division name to ID
        if (! empty($this->data['division_id'])) {
            $division = \App\Models\Division::whereRaw('LOWER(name) = ?', [strtolower($this->data['division_id'])])->first();
            $product->division_id = $division ? $division->id : null;
        }

        // Resolve brand name to ID
        if (! empty($this->data['brand_id'])) {
            $brand = \App\Models\Brand::whereRaw('LOWER(name) = ?', [strtolower($this->data['brand_id'])])->first();
            if (! $brand) {
                $brand = \App\Models\Brand::create(['name' => $this->data['brand_id']]);
            }
            $product->brand_id = $brand ? $brand->id : null;
        }

        return $product;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
