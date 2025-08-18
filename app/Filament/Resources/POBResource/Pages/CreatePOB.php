<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Settings\POBSettings;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\POBResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePOB extends CreateRecord
{
    protected static string $resource = POBResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;

        // Get the customer model instance based on the polymorphic relationship
        $customerType = $data['customer_type'];
        $customerId = $data['customer_id'];

        if ($customerType && $customerId) {
            // Use Relation::getMorphedModel to get the actual class from the morph map
            $customerClass = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($customerType);
            $customerModel = $customerClass::find($customerId);
            if ($customerModel && isset($customerModel->headquarter_id)) {
                $data['headquarter_id'] = $customerModel->headquarter_id;
            }
        }
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
