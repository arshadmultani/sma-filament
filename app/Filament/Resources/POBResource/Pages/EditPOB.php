<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPOB extends EditRecord
{
    protected static string $resource = POBResource::class;

    // 500 error of attempt to read campaign null, hence commented out
    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     $data['campaign_id'] = $this->record->campaignEntry->campaign->name; //show campaign name
    //     return $data;
    // }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
