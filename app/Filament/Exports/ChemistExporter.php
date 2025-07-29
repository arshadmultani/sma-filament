<?php

namespace App\Filament\Exports;

use App\Models\Chemist;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ChemistExporter extends Exporter
{
    protected static ?string $model = Chemist::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('status'),
            ExportColumn::make('user.name'),
            ExportColumn::make('user.roles.name'),
            ExportColumn::make('type'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('address'),
            ExportColumn::make('town'),
            ExportColumn::make('headquarter.name'),
            ExportColumn::make('headquarter.area.name'),
            ExportColumn::make('headquarter.area.region.name'),
            ExportColumn::make('headquarter.area.region.zone.name'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your chemist export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
