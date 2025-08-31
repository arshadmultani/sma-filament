<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Filament\Resources\MicrositeResource;
use App\Models\Doctor;
use App\Models\Microsite;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateMicrosite extends CreateRecord
{
    protected static string $resource = MicrositeResource::class;
    protected static bool $canCreateAnother = false;


    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
        $data['is_active'] = false;
        // $data['status'] = 'Pending';
        $data['user_id'] = Auth::user()->id;
        $data['headquarter_id'] = $doctor->headquarter_id;
        // $data['state_id'] = app(POBSettings::class)->start_state;


        if (isset($data['doctor']['reviews'])) {
            unset($data['doctor']['reviews']);
        }

        if (isset($data['doctor'])) {
            unset($data['doctor']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $campaignId = $this->data['campaign_id'];
        $this->record->campaignEntry()->create([
            'campaign_id' => $campaignId,
            'customer_id' => $this->record->doctor_id,
            'customer_type' => 'doctor',
        ]);
    }
}
