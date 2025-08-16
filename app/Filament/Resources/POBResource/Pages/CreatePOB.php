<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
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
        $data['status_id'] = 1; // Assuming 1 is the ID for 'Pending' status

        return $data;
    }
}
