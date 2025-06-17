<?php

namespace App\Filament\Resources\RegionResource\Pages;

use App\Filament\Imports\RegionImporter;
use App\Filament\Resources\RegionResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\ImportAction::make()
                    ->importer(RegionImporter::class)
                    ->label('Import Regions')
                    ->maxRows(2000)
                    ->color('primary'),
            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }
}
