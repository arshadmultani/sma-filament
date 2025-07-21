<?php

namespace App\Filament\Exports;

use App\Models\KofolEntry;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Carbon\CarbonInterface;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class KofolEntryExporter extends Exporter
{
    protected static ?string $model = KofolEntry::class;
    protected static array $productNames = [];

    protected static function loadProductNames()
    {
        if (empty(self::$productNames)) {
            self::$productNames = \App\Models\Product::pluck('name', 'id')->toArray();
        }
    }
    public function getJobRetryUntil(): ?CarbonInterface
    {
        return now()->addMinutes(15);
    }
    public function getJobMiddleware(): array
    {
        return [
            (new WithoutOverlapping("export{$this->export->getKey()}"))->expireAfter(600),
        ];
    }
    public static function getColumns(): array
    {
        // Preload all product names for efficient lookup
        static $productNames = null;
        if ($productNames === null) {
            $productNames = \App\Models\Product::pluck('name', 'id')->toArray();
        }
        return [
            ExportColumn::make('campaignEntry.campaign.name')->label('Campaign Name'),
            ExportColumn::make('id')->label('Invoice Number')
                ->formatStateUsing(function ($state, $record) {
                    return 'KSV/POB/' . $record->id;
                }),
            ExportColumn::make('customer.name')->label('Customer Name'),
            ExportColumn::make('customer_type')->label('Customer Type'),
            ExportColumn::make('customer.phone')->label('Customer Phone'),
            ExportColumn::make('customer.email')->label('Customer Email'),
            ExportColumn::make('customer.headquarter.name')->label('Headquarter'),
            ExportColumn::make('customer.headquarter.area.name')->label('Area'),
            ExportColumn::make('customer.headquarter.area.region.name')->label('Region'),
            ExportColumn::make('customer.headquarter.area.region.zone.name')->label('Zone'),
            ExportColumn::make('user.name')->label('User Name'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('user.roles.name')->label('User Role'),
            ExportColumn::make('user.division.name')->label(label: 'User Division'),
            ExportColumn::make('products')
                ->label('Products')
                ->formatStateUsing(function ($state, $record) use ($productNames) {
                    if (empty($record->products)) {
                        return '';
                    }
                    return collect($record->products)
                        ->map(function ($item) use ($productNames) {
                            $name = $productNames[$item['product_id']] ?? 'Unknown';
                            $qty = $item['quantity'] ?? 1;
                            return "{$name} x{$qty}";
                        })
                        ->implode(', ');
                }),
            ExportColumn::make('product_count')
                ->label('Product Count')
                ->formatStateUsing(function ($state, $record) {
                    return is_array($record->products) ? count($record->products) : 0;
                }),
            ExportColumn::make('invoice_amount')->label('Invoice Amount'),
            ExportColumn::make('coupons')
                ->label('Coupon Codes')
                ->formatStateUsing(function ($state, $record) {
                    return $record->coupons?->pluck('coupon_code')->implode(', ') ?? '';
                }),
            ExportColumn::make('coupon_count')
                ->label('Coupon Count')
                ->formatStateUsing(function ($state, $record) {
                    return $record->coupons?->count() ?? 0;
                }),
            ExportColumn::make('created_at')->label('Created At'),
            ExportColumn::make('updated_at')->label('Updated At'),
            ExportColumn::make('notification_status')
                ->label('Notification')
                ->formatStateUsing(function (
                    $state,
                    $record
                ) {
                    $customer = $record->customer;
                    if (!$customer) return 'No customer';
                    return $customer->notifications()
                        ->where('type', \App\Notifications\KofolCouponNotification::class)
                        ->where('data->kofol_entry_id', $record->id)
                        ->exists() ? 'Sent' : 'Not Sent';
                }),
            ExportColumn::make('notified_at')
                ->label('Notified At')
                ->formatStateUsing(function ($state, $record) {
                    $customer = $record->customer;
                    if (!$customer) return '';
                    $notification = $customer->notifications()
                        ->where('type', \App\Notifications\KofolCouponNotification::class)
                        ->where('data->kofol_entry_id', $record->id)
                        ->orderByDesc('created_at')
                        ->first();
                    return $notification ? $notification->created_at->format('Y-m-d H:i:s') : '';
                }),
        ];
    }

    public static function modifyQuery(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return KofolEntry::query()->with([
            'user',
            'customer' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\Doctor::class => ['headquarter', 'headquarter.area', 'headquarter.area.region', 'headquarter.area.region.zone'],
                    \App\Models\Chemist::class => ['headquarter', 'headquarter.area', 'headquarter.area.region', 'headquarter.area.region.zone'],
                ]);
            },
        ]);
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your kofol entry export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
