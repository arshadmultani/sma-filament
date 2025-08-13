<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPOBS extends ListRecords
{
    protected static string $resource = POBResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
