<?php

namespace App\Filament\Imports;

use App\Models\Region;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class RegionImporter extends Importer
{
    protected static ?string $model = Region::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping(),
            ImportColumn::make('zone_id')
                ->label('Zone')
                ->requiredMapping()
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
        ];
    }

    public function resolveRecord(): ?Region
    {
        $region = new Region;
        $region->name = $this->data['name'] ?? null;

        if (! empty($this->data['zone_id'])) {
            $zone = \App\Models\Zone::whereRaw('LOWER(name) = ?', [strtolower($this->data['zone_id'])])->first();
            $region->zone_id = $zone ? $zone->id : null;
        }

        return $region;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your region import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
