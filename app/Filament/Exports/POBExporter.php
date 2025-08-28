<?php

namespace App\Filament\Exports;

use App\Models\POB;
use Carbon\CarbonInterface;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class POBExporter extends Exporter
{
    protected static ?string $model = POB::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('Invoice Number')
                ->formatStateUsing(function ($state, $record) {
                    return 'KSV/POB/' . $record->id;
                }),
            ExportColumn::make('campaignEntry.campaign.name')->label('Campaign Name'),
            ExportColumn::make('customer.name')->label('Customer Name'),
            ExportColumn::make('customer_type')->label('Customer Type'),
            ExportColumn::make('invoice_amount')->label('Invoice Amount'),
            ExportColumn::make('customer.phone')->label('Customer Phone'),
            ExportColumn::make('customer.email')->label('Customer Email'),
            ExportColumn::make('headquarter.name')->label('Headquarter'),
            ExportColumn::make('headquarter.area.name')->label('Area'),
            ExportColumn::make('headquarter.area.region.name')->label('Region'),
            ExportColumn::make('headquarter.area.region.zone.name')->label('Zone'),
            ExportColumn::make('user.name')->label('User Name'),
            ExportColumn::make('state.name')->label('Status'),
            ExportColumn::make('user.roles.name')->label('User Role'),
            ExportColumn::make('user.division.name')->label(label: 'User Division'),
            ExportColumn::make('created_at')->label('Created At'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your p o b export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
    public function getJobRetryUntil(): ?CarbonInterface
    {
        return now()->addMinutes(15);
    }
}
