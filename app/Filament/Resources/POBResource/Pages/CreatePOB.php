<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Settings\POBSettings;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\POBResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePOB extends CreateRecord
{
    protected static string $resource = POBResource::class;

    public function getTitle(): string
    {
        return 'New POB';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSubmitFormAction()->label('Submit'),
            $this->getCancelFormAction(),
        ];

    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;

        // Get the customer 
        $customerType = $data['customer_type'];
        $customerId = $data['customer_id'];

        //set HQ
        if ($customerType && $customerId) {
            $customerClass = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($customerType);
            $customerModel = $customerClass::find($customerId);
            if ($customerModel && isset($customerModel->headquarter_id)) {
                $data['headquarter_id'] = $customerModel->headquarter_id;
            }
        }
        // set default state
        $data['state_id'] = app(POBSettings::class)->start_state;

        return $data;
    }

    public function afterCreate(): void
    {
        $campaignId = $this->data['campaign_id'];
        $this->record->campaignEntry()->create([
            'campaign_id' => $campaignId,
            'customer_id' => $this->record->customer_id,
            'customer_type' => $this->record->customer_type,
        ]);
    }
}
