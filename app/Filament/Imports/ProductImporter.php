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
                ->rules(['required', 'max:255']),

            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric(decimalPlaces: 2),

            ImportColumn::make('division_id')
                ->label('Division')
                ->requiredMapping()
                ->castStateUsing(fn($state) => trim($state))
                ->relationship('division', 'name'),
            ImportColumn::make('brand_id')
                ->label('Brand')
                ->requiredMapping()
                ->castStateUsing(fn($state) => trim($state))
                ->relationship('brand', 'name'),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // if(!empty($this->data['division_id'])){
        //     $division = \App\Models\Division::whereRaw('LOWER(name) = ?', [strtolower($this->data['division_id'])])->first();
        //     if($division){
        //         $this->data['division_id'] = $division->id;
        //     }
        // }
        // if(!empty($this->data['brand_id'])){
        //     $brand = \App\Models\Brand::whereRaw('LOWER(name) = ?', [strtolower($this->data['brand_id'])])->first();
        //     if($brand){
        //         $this->data['brand_id'] = $brand->id;
        //     }
        // }
        

        return new Product();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
