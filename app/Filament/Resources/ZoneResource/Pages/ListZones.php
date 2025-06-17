<?php

namespace App\Filament\Resources\ZoneResource\Pages;

use App\Filament\Imports\ZoneImporter;
use App\Filament\Resources\ZoneResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;

class ListZones extends ListRecords
{
    protected static string $resource = ZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\ImportAction::make()
                    ->importer(ZoneImporter::class)
                    ->label('Import Zones')
                    ->maxRows(2000)
                    ->color('primary'),
            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }
}
