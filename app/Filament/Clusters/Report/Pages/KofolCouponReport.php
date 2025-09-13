<?php

namespace App\Filament\Clusters\Report\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\KofolEntryCoupon;
use App\Filament\Clusters\Report;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\Cache;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Exports\KofolEntryResourceExporter;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;



class KofolCouponReport extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.report.pages.kofol-coupon-report';
    protected static ?string $navigationLabel = 'KSV Coupon Report';
    protected static ?int $navigationSort = 2;


    protected static ?string $cluster = Report::class;

    public function table(Table $table): Table
    {
        // Fetch all products with brand 'Kofol'
        $kofolProducts = Cache::remember('kofol_products', 86400, function () {
            return \App\Models\Product::whereHas('brand', function ($q) {
                $q->where('name', 'Kofol');
            })->get();
        });

        // Optimize: Only select columns needed for KofolEntryCoupon and related models
        $couponQuery = \App\Models\KofolEntryCoupon::query()
            ->select(['id', 'coupon_code', 'kofol_entry_id'])
            ->with([
                'kofolEntry:id,customer_id,user_id,headquarter_id,customer_type,invoice_amount,products',
                'kofolEntry.customer:id,name,email,phone',
                'kofolEntry.user:id,name',
                'kofolEntry.headquarter:id,name,area_id',
                'kofolEntry.headquarter.area:id,name,region_id',
                'kofolEntry.headquarter.area.region:id,name,zone_id',
                'kofolEntry.headquarter.area.region.zone:id,name',
            ]);

        // Precompute product quantities for all KofolEntry IDs on the current page
        $precomputeProductQuantities = function ($records) use ($kofolProducts) {
            $entryProductMap = [];
            foreach ($records as $record) {
                $entry = $record->kofolEntry;
                if (!$entry)
                    continue;
                $products = Cache::remember("kofol_entry_products_{$entry->id}", 3600, function () use ($entry) {
                    $products = $entry->products ?? [];
                    if (is_string($products)) {
                        $products = json_decode($products, true);
                    }
                    return is_array($products) ? $products : [];
                });
                foreach ($kofolProducts as $product) {
                    $item = collect($products)->firstWhere('product_id', (string) $product->id);
                    $entryProductMap[$entry->id][$product->id] = $item['quantity'] ?? '-';
                }
            }
            return $entryProductMap;
        };

        $columns = [
            TextColumn::make('coupon_code')
                ->label('Coupon No.')
                ->searchable()
                ->sortable(),
            TextColumn::make('kofol_entry_id')
                ->label('KSV ID')
                ->searchable()
                ->sortable(),
            TextColumn::make('kofolEntry.customer.name')
                ->label('Cx'),
            TextColumn::make('kofolEntry.customer_type')
                ->label('Cx Type'),
            TextColumn::make('kofolEntry.customer.email')
                ->label('Cx Email'),
            TextColumn::make('kofolEntry.customer.phone')
                ->label('Cx Phone'),
            TextColumn::make('kofolEntry.user.name')
                ->label('User')
                ->searchable(),
            TextColumn::make('kofolEntry.headquarter.name')
                ->label('HQ')
                ->searchable(),
            TextColumn::make('kofolEntry.headquarter.area.name')
                ->label('Area'),
            TextColumn::make('kofolEntry.headquarter.area.region.name')
                ->label('Region'),
            TextColumn::make('kofolEntry.headquarter.area.region.zone.name')
                ->label('Zone'),
            TextColumn::make('kofolEntry.invoice_amount')
                ->label('POB Amount')
                ->numeric(),
        ];

        // Add a column for each Kofol product, using precomputed quantities
        // foreach ($kofolProducts as $product) {
        //     $columns[] = TextColumn::make('kofolEntry.product_' . $product->id . '_qty')
        //         ->label($product->name)
        //         ->getStateUsing(function ($record) use ($product, &$precomputeProductQuantities) {
        //             static $entryProductMap = null;
        //             if ($entryProductMap === null) {
        //                 // Precompute once per page
        //                 $entryProductMap = $precomputeProductQuantities(func_get_args()[1] ?? []);
        //             }
        //             $entry = $record->kofolEntry;
        //             if (!$entry)
        //                 return '';
        //             return $entryProductMap[$entry->id][$product->id] ?? '-';
        //         });
        // }

        foreach ($kofolProducts as $product) {
            $columns[] = TextColumn::make('kofolEntry.product_' . $product->id . '_qty')
                ->label($product->name)
                ->getStateUsing(function ($record) use ($product, $precomputeProductQuantities) {
                    static $entryProductMap = [];

                    $entry = $record->kofolEntry;
                    if (!$entry) {
                        return '';
                    }

                    // Lazy precompute only if this entry hasn't been cached yet
                    if (!isset($entryProductMap[$entry->id])) {
                        $entryProductMap = array_merge(
                            $entryProductMap,
                            $precomputeProductQuantities([$record])
                        );
                    }

                    return $entryProductMap[$entry->id][$product->id] ?? '-';
                });
        }


        return $table
            ->paginated([50, 100, 250])
            ->defaultSort('coupon_code', 'asc')
            ->extremePaginationLinks()
            ->heading('KSV Coupons')
            ->query($couponQuery)
            ->groups([
                Group::make('kofolEntry.headquarter.name')
                    ->collapsible()
                    ->label('HQ'),
                Group::make('kofolEntry.headquarter.area.name')
                    ->collapsible()
                    ->label('Area'),
                Group::make('kofolEntry.headquarter.area.region.name')
                    ->collapsible()
                    ->label('Region'),
                Group::make('kofolEntry.headquarter.area.region.zone.name')
                    ->collapsible()
                    ->label('Zone'),
            ])
            ->columns($columns)
            ->headerActions([
                ExportAction::make()
                    ->exporter(KofolEntryResourceExporter::class)
                    ->label('Download Report')
                    ->color('primary'),
                // ExportAction::make('export')
                //     ->exports([
                //         ExcelExport::make()
                //             ->fromTable()
                //             ->queue()
                //             ->withChunkSize(1000)
                //     ])
            ]);
    }
}
