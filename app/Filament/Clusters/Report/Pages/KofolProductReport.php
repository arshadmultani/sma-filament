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
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class KofolProductReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.report.pages.kofol-product-report';

    protected static ?string $cluster = Report::class;

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
        // Calculate totals before rendering table
        $this->calculateProductTotals();

        return $table
            ->heading('Kofol Product Report')
            ->paginated(false)
            ->query(Product::query()->where('brand_id', Brand::where('name', 'Kofol')->value('id')))
            ->headerActions([
                ExportAction::make('export')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            // ->queue()
                    ])
            ])
            ->columns([
                TextColumn::make('name')->label('Product'),
                TextColumn::make('total_qty')
                    ->label('Qty')
                    ->getStateUsing(function ($record) {
                        // Use pre-calculated totals
                        return $this->productTotals[(string) $record->id] ?? 0;
                    }),
            ]);
    }
}
