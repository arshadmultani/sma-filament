<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Form;
use App\Models\Campaign;
use App\Models\KofolEntry;
use Filament\Forms\Components\Section;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Product;
use App\Models\Brand;   

class Reports extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.reports';

    protected array $productTotals = [];


    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
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
            ->query(Product::query()->where('brand_id', Brand::where('name', 'Kofol')->value('id')))
            ->columns([
                TextColumn::make('name')->label('Product'),
                TextColumn::make('total_qty')
                    ->label('Qty')
                    ->getStateUsing(function ($record) {
                        // Use pre-calculated totals
                        return $this->productTotals[(string)$record->id] ?? 0;
                    }),
            ]);
    }


}

//table 1 of campaign 1

//table 2 of campaign 1

//table 1 of campaign 2

//table 2 of campaign 2



