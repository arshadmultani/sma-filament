<?php

namespace App\Filament\Imports;

use App\Models\Area;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class AreaImporter extends Importer
{
    protected static ?string $model = Area::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping(),   
            ImportColumn::make('region_id')
                ->label('Region')
                ->requiredMapping()
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
        ];
    }

    public function resolveRecord(): ?Area
    {
        $area = new Area();

        $area->name = Str::title($this->data['name'] ?? null);


        if (! empty($this->data['region_id'])) {
            $region = \App\Models\Region::whereRaw('LOWER(name) = ?', [strtolower($this->data['region_id'])])->first();
            $area->region_id = $region ? $region->id : null;
        }

        return $area;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your area import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
