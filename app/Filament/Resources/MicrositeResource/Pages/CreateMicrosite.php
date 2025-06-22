<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Filament\Resources\MicrositeResource;
use App\Models\Doctor;
use App\Models\Microsite;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateMicrosite extends CreateRecord
{
    protected static string $resource = MicrositeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $doctor = Doctor::find($data['doctor_id']);
        $firstName = explode(' ', $doctor->name)[0];
        $slug = Str::slug($firstName);

        do {
            $random = Str::lower(Str::random(5));
            $url = $slug . '-' . $random;
        } while (Microsite::where('url', $url)->exists());

        $data['url'] = $url;
        $data['is_active'] = true;
        $data['status'] = 'published';

        if (isset($data['doctor']['reviews'])) {
            unset($data['doctor']['reviews']);
        }
        
        if(isset($data['doctor'])) {
            unset($data['doctor']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $campaignId = $this->data['campaign_id'];
        $this->record->campaignEntry()->create([
            'campaign_id'   => $campaignId,
            'customer_id'   => $this->record->doctor_id,
            'customer_type' => 'doctor',
        ]);
    }
}
