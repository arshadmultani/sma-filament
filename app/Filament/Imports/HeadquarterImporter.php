<?php

namespace App\Filament\Imports;

use App\Models\Headquarter;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class HeadquarterImporter extends Importer
{
    protected static ?string $model = Headquarter::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping(),
            ImportColumn::make('area_id')
                ->label('Area')
                ->requiredMapping()
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
        ];
    }

    public function resolveRecord(): ?Headquarter
    {
        $headquarter = new Headquarter();

        $headquarter->name = Str::title($this->data['name'] ?? null);

        if (! empty($this->data['area_id'])) {
            $area = \App\Models\Area::whereRaw('LOWER(name) = ?', [strtolower($this->data['area_id'])])->first();
            $headquarter->area_id = $area ? $area->id : null;
        }

        return $headquarter;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your headquarter import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
