<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Imports\AreaImporter;
use App\Filament\Resources\AreaResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;

class ListAreas extends ListRecords
{
    protected static string $resource = AreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\ImportAction::make()
                    ->importer(AreaImporter::class)
                    ->label('Import Areas')
                    ->maxRows(2000)
                    ->color('primary'),
            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }
}
