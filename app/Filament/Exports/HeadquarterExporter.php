<?php

namespace App\Filament\Exports;

use App\Models\Headquarter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class HeadquarterExporter extends Exporter
{
    protected static ?string $model = Headquarter::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Headquarter'),
            ExportColumn::make('area.name')->label('Area'),
            ExportColumn::make('area.region.name')->label('Region'),
            ExportColumn::make('area.region.zone.name')->label('Zone'),
            ExportColumn::make('area.region.division.name')->label('Division'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your headquarter export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
