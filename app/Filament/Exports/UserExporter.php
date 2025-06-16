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
            ExportColumn::make('division.name'),
            ExportColumn::make('roles.name'),
            ExportColumn::make('region')
                ->label('Region')
                ->formatStateUsing(function ($state, $record) {
                    if ($record->location instanceof \App\Models\Region) {
                        return $record->location->name;
                    } elseif ($record->location instanceof \App\Models\Area) {
                        return $record->location->region?->name;
                    } elseif ($record->location instanceof \App\Models\Headquarter) {
                        return $record->location->area?->region?->name;
                    }

                    return '-';
                }),
            ExportColumn::make('area')
                ->label('Area')
                ->formatStateUsing(function ($state, $record) {
                    if ($record->location instanceof \App\Models\Area) {
                        return $record->location->name;
                    } elseif ($record->location instanceof \App\Models\Headquarter) {
                        return $record->location->area?->name;
                    }

                    return '-';
                }),
            ExportColumn::make('headquarter')
                ->label('Headquarter')
                ->formatStateUsing(function ($state, $record) {
                    if ($record->location instanceof \App\Models\Headquarter) {
                        return $record->location->name;
                    }

                    return '-';
                }),
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
