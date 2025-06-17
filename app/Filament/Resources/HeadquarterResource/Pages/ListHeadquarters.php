<?php

namespace App\Filament\Resources\HeadquarterResource\Pages;

use App\Filament\Imports\HeadquarterImporter;
use App\Filament\Resources\HeadquarterResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class ListHeadquarters extends ListRecords
{
    protected static string $resource = HeadquarterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\ImportAction::make()
                    ->importer(HeadquarterImporter::class)
                    ->label('Import Headquarters')
                    ->maxRows(2000)
                    ->color('primary'),
            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }

    // protected function paginateTableQuery(Builder $query): CursorPaginator
    // {
    //     return $query->cursorPaginate($this->getTableRecordsPerPage());
    // }
}
