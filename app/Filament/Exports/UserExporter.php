<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('phone_number'),
            ExportColumn::make('division_id'),
            // ExportColumn::make('region')->label('Region')->formatStateUsing(function ($state, $record) {
            //     if ($record->location_type === \App\Models\Region::class && $record->location) {
            //         return $record->location->name;
            //     }
            //     if ($record->location_type === \App\Models\Area::class && $record->location && $record->location->region) {
            //         return $record->location->region->name;
            //     }
            //     if ($record->location_type === \App\Models\Headquarter::class && $record->location && $record->location->area && $record->location->area->region) {
            //         return $record->location->area->region->name;
            //     }
            //     return null;
            // }),
            // ExportColumn::make('area')->label('Area')->formatStateUsing(function ($state, $record) {
            //     if ($record->location_type === \App\Models\Area::class && $record->location) {
            //         return $record->location->name;
            //     }
            //     if ($record->location_type === \App\Models\Headquarter::class && $record->location && $record->location->area) {
            //         return $record->location->area->name;
            //     }
            //     return null;
            // }),
            // ExportColumn::make('headquarter')->label('Headquarter')->formatStateUsing(function ($state, $record) {
            //     if ($record->location_type === \App\Models\Headquarter::class && $record->location) {
            //         return $record->location->name;
            //     }
            //     return null;
            // }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
