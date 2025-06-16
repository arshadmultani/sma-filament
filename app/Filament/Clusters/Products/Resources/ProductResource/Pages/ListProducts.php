<?php

namespace App\Filament\Clusters\Products\Resources\ProductResource\Pages;

use App\Filament\Clusters\Products\Resources\ProductResource;
use App\Filament\Imports\ProductImporter;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                // Actions\ExportAction::make()
                //     ->exporter(UserExporter::class)
                //     ->label('Download All Users')
                //     // ->icon('heroicon-m-arrow-long-up')
                //     ->maxRows(2000)
                //     ->color('primary'),
                ImportAction::make()
                    ->importer(ProductImporter::class)
                    ->label('Import Products')
                    // ->icon('heroicon-m-arrow-down-on-square')
                    ->color('primary'),
            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }
}
