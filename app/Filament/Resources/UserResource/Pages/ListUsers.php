<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\UserImporter;

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
                    ->label('Download All Users')
                    // ->icon('heroicon-m-arrow-long-up')
                    ->maxRows(2000)
                    ->color('primary'),
                ImportAction::make()
                    ->importer(UserImporter::class)
                    ->label('Import Users')
                    // ->icon('heroicon-m-arrow-down-on-square')
                    ->color('primary'),

            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }
}
