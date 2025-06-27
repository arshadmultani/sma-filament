<?php

namespace App\Filament\Imports;

use App\Models\Zone;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ZoneImporter extends Importer
{
    protected static ?string $model = Zone::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping(),
            ImportColumn::make('division_id')
                ->label('Division')
                ->requiredMapping()
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
        ];
    }

    public function resolveRecord(): ?Zone
    {
        $zone = new Zone();
        $zone->name = $this->data['name'] ?? null;

        if (! empty($this->data['division_id'])) {
            $division = \App\Models\Division::whereRaw('LOWER(name) = ?', [strtolower($this->data['division_id'])])->first();
            $zone->division_id = $division ? $division->id : null;
        }

        return $zone;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your zone import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
