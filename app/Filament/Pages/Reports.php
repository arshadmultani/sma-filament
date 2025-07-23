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

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->where('brand_id', Brand::where('name', 'Kofol')->value('id')))
            ->columns([
                TextColumn::make('name')->label('Product'),
                TextColumn::make('total_qty')
                    ->label('Qty')
                    ->getStateUsing(function ($record) {
                        // $record is a Product
                        // Get all KofolEntry records
                        $entries = KofolEntry::all()->where('status', 'Approved');
                        $total = 0;
                        foreach ($entries as $entry) {
                            $products = $entry->products; // assuming this is cast to array
                            foreach ($products as $product) {
                                if (
                                    isset($product['product_id']) &&
                                    (string)$product['product_id'] === (string)$record->id // compare as strings
                                ) {
                                    $total += (int)($product['quantity'] ?? 0); // use 'quantity'
                                }
                            }
                        }
                        return $total;
                    }),
            ]);
    }


}

//table 1 of campaign 1

//table 2 of campaign 1

//table 1 of campaign 2

//table 2 of campaign 2



