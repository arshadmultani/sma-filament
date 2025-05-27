<?php

namespace App\Filament\Resources\KofolCampaignResource\Pages;

use App\Filament\Resources\KofolCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKofolCampaigns extends ListRecords
{
    protected static string $resource = KofolCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
