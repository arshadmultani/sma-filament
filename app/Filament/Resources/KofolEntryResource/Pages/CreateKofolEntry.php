<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use App\Filament\Resources\KofolEntryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateKofolEntry extends CreateRecord
{
    protected static string $resource = KofolEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        $data['status'] = 'Pending';

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSubmitFormAction()->label('Submit'),
            $this->getCancelFormAction(),
        ];

    }

    protected function afterCreate(): void
    {
        $campaignId = $this->data['campaign_id'];
        $this->record->campaignEntry()->create([
            'campaign_id'   => $campaignId,
            'customer_id'   => $this->record->customer_id,
            'customer_type' => $this->record->customer_type,
        ]);
    }
}
