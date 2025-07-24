<?php

namespace App\Filament\Clusters\Report\Pages;

use App\Filament\Clusters\Report;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Product;
use App\Models\Brand;
use App\Models\KofolEntry;
use App\Models\User;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Tables\Grouping\Group;



class KofolProductReport extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.report.pages.kofol-product-report';

    protected static ?string $cluster = Report::class;
    protected static ?string $navigationLabel = 'KSV HQ Report';

    protected static ?int $navigationSort = 1;

    protected array $productTotals = [];
    protected function calculateProductTotals()
    {
        // Only calculate once
        if (!empty($this->productTotals)) {
            return;
        }

        $totals = [];
        // Fetch all approved entries once
        $entries = KofolEntry::where('status', 'Approved')->get();
        foreach ($entries as $entry) {
            foreach ($entry->products as $product) {
                $productId = (string) $product['product_id'];
                $qty = (int) ($product['quantity'] ?? 0);
                if (!isset($totals[$productId])) {
                    $totals[$productId] = 0;
                }
                $totals[$productId] += $qty;
            }
        }
        $this->productTotals = $totals;
    }
    public function table(Table $table): Table
    {
        // Get all Kofol products
        $kofolBrandId = \App\Models\Brand::where('name', 'Kofol')->value('id');
        $kofolProducts = \App\Models\Product::where('brand_id', $kofolBrandId)->get();

        // Get all headquarters
        $headquarters = \App\Models\Headquarter::orderBy('name')->get();

        // Pre-calculate quantities: [headquarter_id][product_id] => qty
        $quantities = [];
        $entries = \App\Models\KofolEntry::where('status', 'Approved')
            ->whereNotNull('headquarter_id')
            ->get();
        foreach ($entries as $entry) {
            $hqId = $entry->headquarter_id;
            foreach ($entry->products as $product) {
                $productId = (string) $product['product_id'];
                $qty = (int) ($product['quantity'] ?? 0);
                if (!isset($quantities[$hqId])) {
                    $quantities[$hqId] = [];
                }
                if (!isset($quantities[$hqId][$productId])) {
                    $quantities[$hqId][$productId] = 0;
                }
                $quantities[$hqId][$productId] += $qty;
            }
        }

        // Pre-calculate total invoice_amount per headquarter (optimized)
        $hqInvoiceTotals = \App\Models\KofolEntry::query()
            ->where('status', 'Approved')
            ->whereNotNull('headquarter_id')
            ->groupBy('headquarter_id')
            ->selectRaw('headquarter_id, SUM(invoice_amount) as total')
            ->pluck('total', 'headquarter_id');

        // Pre-calculate total coupon count per headquarter (fixed to match coupon report logic)
        $hqCouponCounts = \App\Models\KofolEntryCoupon::query()
            ->whereHas('kofolEntry', function ($q) {
                $q->where('status', 'Approved')
                    ->whereNotNull('headquarter_id');
            })
            ->with('kofolEntry')
            ->get()
            ->groupBy(function ($coupon) {
                return $coupon->kofolEntry->headquarter_id ?? null;
            })
            ->map(function ($group) {
                return $group->count();
            });

        // Pre-calculate unique customer, Dr, and Chemist counts per headquarter
        $approvedEntries = \App\Models\KofolEntry::where('status', 'Approved')
            ->whereNotNull('headquarter_id')
            ->get(['headquarter_id', 'customer_id', 'customer_type']);

        $hqUniqueCustomers = $approvedEntries->groupBy('headquarter_id')->map(function ($entries) {
            return $entries->pluck('customer_id')->unique()->count();
        });
        $hqUniqueDrs = $approvedEntries->where('customer_type', 'doctor')->groupBy('headquarter_id')->map(function ($entries) {
            return $entries->pluck('customer_id')->unique()->count();
        });
        $hqUniqueChemists = $approvedEntries->where('customer_type', 'chemist')->groupBy('headquarter_id')->map(function ($entries) {
            return $entries->pluck('customer_id')->unique()->count();
        });

        // Pre-calculate number of invoices per headquarter
        $hqInvoiceCounts = $approvedEntries->groupBy('headquarter_id')->map(function ($entries) {
            return $entries->count();
        });

        // Build columns: first is HQ, then one per Kofol product
        $columns = [
            TextColumn::make('name')->label('DSA')
                ->searchable()
                ->sortable(),
            TextColumn::make('kofol_coupon_count')
                ->label('Coupons')
                ->sortable()
                ->getStateUsing(function ($record) use ($hqCouponCounts) {
                    if (!$record->location instanceof \App\Models\Headquarter) {
                        return '-';
                    }
                    $hqId = $record->location->id;
                    return $hqCouponCounts[$hqId] ?? 0;
                }),
            TextColumn::make('hq_invoice_count')
                ->label('No. of Invoices')
                ->getStateUsing(function ($record) use ($hqInvoiceCounts) {
                    if (!$record->location instanceof \App\Models\Headquarter) {
                        return '-';
                    }
                    $hqId = $record->location->id;
                    return $hqInvoiceCounts[$hqId] ?? 0;
                }),
            TextColumn::make('kofol_invoice_total')
                ->label('Invoice Amount')
                ->getStateUsing(function ($record) use ($hqInvoiceTotals) {
                    if (!$record->location instanceof \App\Models\Headquarter) {
                        return '-';
                    }
                    $hqId = $record->location->id;
                    $total = $hqInvoiceTotals[$hqId] ?? 0;
                    return number_format($total, 0);
                }),
            TextColumn::make('hq_unique_customers')
                ->label('Unique Customers')
                ->getStateUsing(function ($record) use ($hqUniqueCustomers) {
                    if (!$record->location instanceof \App\Models\Headquarter) {
                        return '-';
                    }
                    $hqId = $record->location->id;
                    return $hqUniqueCustomers[$hqId] ?? 0;
                }),
            TextColumn::make('hq_unique_drs')
                ->label('Unique Drs')
                ->getStateUsing(function ($record) use ($hqUniqueDrs) {
                    if (!$record->location instanceof \App\Models\Headquarter) {
                        return '-';
                    }
                    $hqId = $record->location->id;
                    return $hqUniqueDrs[$hqId] ?? 0;
                }),
            TextColumn::make('hq_unique_chemists')
                ->label('Unique Chemists')
                ->getStateUsing(function ($record) use ($hqUniqueChemists) {
                    if (!$record->location instanceof \App\Models\Headquarter) {
                        return '-';
                    }
                    $hqId = $record->location->id;
                    return $hqUniqueChemists[$hqId] ?? 0;
                }),
            TextColumn::make('headquarter_name')
                ->label('HQ'),
            TextColumn::make('area_name')
                ->label('Area'),
            TextColumn::make('region_name')
                ->label('Region'),
            TextColumn::make('zone_name')
                ->label('Zone'),
            TextColumn::make('division.name')->label('Division')
                ->searchable()
                ->sortable(),

        ];
        foreach ($kofolProducts as $product) {
            $columns[] = TextColumn::make('product_' . $product->id . '_qty')
                ->label($product->name)
                ->getStateUsing(function ($record) use ($product, $quantities) {
                    $hqId = $record->id;
                    return $quantities[$hqId][$product->id] ?? '-';
                });
        }

        // Use headquarters as the table data
        return $table
            ->heading('KSV HQ Report')
            ->paginated([50, 100, 250])
            ->defaultSort('name', 'asc')
            ->query(User::query()->whereHas('roles', fn($q) => $q->where('name', 'DSA'))->with('division', 'location'))
            // ->groups([
            //     Group::make('headquarter_name')
            //         ->collapsible()
            //         ->orderQueryUsing(fn($query, $direction) => $query) // disables SQL ordering
            //         ->label('HQ'),
            //     Group::make('area_name')
            //         ->collapsible()
            //         ->orderQueryUsing(fn($query, $direction) => $query)
            //         ->groupQueryUsing(fn($query)=>$query->groupBy('area_name'))
            //         ->label('Area'),
            // Group::make('location.area.name')
            //     ->collapsible()
            //     ->label('Area'),
            // Group::make('location.area.region.name')
            //     ->collapsible()
            //     ->label('Region'),
            // Group::make('location.area.region.zone.name')
            //     ->collapsible()
            //     ->label('Zone'),
            // ])
            ->headerActions([
                ExportAction::make('export')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                    ])
            ])
            ->columns($columns);
    }
}
