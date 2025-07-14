<?php

namespace App\Filament\Resources\ManagerLogEntryResource\Pages;

use App\Filament\Resources\ManagerLogEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManagerLogEntries extends ListRecords
{
    protected static string $resource = ManagerLogEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Track'),
        ];
    }
}
