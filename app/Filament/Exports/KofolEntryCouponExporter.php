<?php

namespace App\Filament\Exports;

use App\Models\KofolEntryCoupon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class KofolEntryCouponExporter extends Exporter
{
    protected static ?string $model = KofolEntryCoupon::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('coupon_code')
                ->label('Coupon No'),
            ExportColumn::make('kofol_entry_id')
                ->label('KSV Booking No.'),
            ExportColumn::make('kofolEntry.user.name')
                ->label('User Name'),
            ExportColumn::make('kofolEntry.customer.name')
                ->label('Customer Name'),
            ExportColumn::make('kofolEntry.customer.headquarter.name')
                ->label('Headquarter'),
            ExportColumn::make('kofolEntry.customer.email')
                ->label('Customer Email'),
            ExportColumn::make('kofolEntry.customer.phone')
                ->label('Customer Phone'),

            ExportColumn::make('kofolEntry.created_at')
                ->label('Created Date'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your kofol entry coupon export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'kofol-entry-coupons-' . now()->format('Y-m-d-H-i-s') . '.csv';
    }

    public static function modifyQuery(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return KofolEntryCoupon::query()->with([
            'kofolEntry.user',
            'kofolEntry.customer' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\Doctor::class => ['headquarter'],
                    \App\Models\Chemist::class => ['headquarter'],
                ]);
            },
        ]);
    }
}
