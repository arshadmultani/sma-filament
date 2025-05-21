<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->label('Export')
                    ->icon('heroicon-m-arrow-down-on-square')
                    ->maxRows(2000)
                    ->color('primary'),
            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }
}
