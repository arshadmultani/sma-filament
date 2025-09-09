<?php

namespace App\Filament\Resources\PanelAccessRequestResource\Pages;

use App\Filament\Resources\PanelAccessRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPanelAccessRequests extends ListRecords
{
    protected static string $resource = PanelAccessRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
