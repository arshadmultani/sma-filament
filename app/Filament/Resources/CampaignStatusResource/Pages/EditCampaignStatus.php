<?php

namespace App\Filament\Resources\CampaignStatusResource\Pages;

use App\Filament\Resources\CampaignStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaignStatus extends EditRecord
{
    protected static string $resource = CampaignStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
