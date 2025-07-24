<?php

namespace App\Filament\Clusters\Report\Pages;

use App\Filament\Clusters\Report;
use App\Models\KofolEntryCoupon;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;



class KofolCouponReport extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.report.pages.kofol-coupon-report';

    protected static ?string $cluster = Report::class;

    public function table(Table $table): Table
    {
        // Fetch all products with brand 'Kofol'
        $kofolProducts = \App\Models\Product::whereHas('brand', function ($q) {
            $q->where('name', 'Kofol');
        })->get();

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

        // Add a column for each Kofol product
        foreach ($kofolProducts as $product) {
            $columns[] = TextColumn::make('kofolEntry.product_' . $product->id . '_qty')
                ->label($product->name)
                ->getStateUsing(function ($record) use ($product) {
                    $products = $record->kofolEntry->products ?? [];
                    if (is_string($products)) {
                        $products = json_decode($products, true);
                    }
                    if (!is_array($products)) {
                        return '';
                    }
                    $item = collect($products)->firstWhere('product_id', (string)$product->id);
                    return $item['quantity'] ?? '-';
                });
        }

        // $columns[] = TextColumn::make('kofolEntry.products')
        //     ->label('Products');

        return $table
            ->paginated([50, 100, 250])
            ->defaultSort('coupon_code', 'asc')
            ->extremePaginationLinks()
            ->heading('Kofol Coupon Report')
            ->query(
                KofolEntryCoupon::query()->with([
                    'kofolEntry.customer',
                    'kofolEntry.user',
                    'kofolEntry.headquarter.area.region.zone',
                ])
            )
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
            ->bulkActions([
                ExportBulkAction::make('export')
                // ->queue()
            ])
            ->headerActions([
                ExportAction::make('export')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                    ])
            ]);
    }
}
