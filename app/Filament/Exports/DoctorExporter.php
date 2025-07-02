<?php

namespace App\Filament\Exports;

use App\Models\Doctor;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class DoctorExporter extends Exporter
{
    protected static ?string $model = Doctor::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('headquarter_name')->label('Headquarter'),
            ExportColumn::make('division_id'),
           
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your doctor export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
