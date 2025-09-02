<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Models\State;
use Filament\Actions;
use App\Models\Doctor;
use App\Models\Microsite;
use Illuminate\Support\Str;
use App\Enums\StateCategory;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MicrositeResource;
use Illuminate\Database\Eloquent\Relations\Relation;

class CreateMicrosite extends CreateRecord
{
    protected static string $resource = MicrositeResource::class;
    protected static bool $canCreateAnother = false;

    protected $doctorShowcases = [];


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
        $data['user_id'] = Auth::user()->id;
        $data['is_active'] = true;
        // $data['customer_type'] = 'doctor';
        $data['state_id'] = State::where('category', StateCategory::PENDING)->first()->id;
        $data['headquarter_id'] = $doctor->headquarter_id;
        // $data['state_id'] = app(POBSettings::class)->start_state;


        if (isset($data['doctor']['reviews'])) {
            unset($data['doctor']['reviews']);
        }

        if (isset($data['doctor'])) {
            unset($data['doctor']);
        }

        // Store doctor showcases separately to handle after creation
        if (isset($data['showcases_data'])) {
            $this->doctorShowcases = $data['showcases_data'];
            unset($data['showcases_data']);
        }

        return $data;
    }

    // protected function afterCreate(): void
    // {
    //     $campaignId = $this->data['campaign_id'];
    //     $this->record->campaignEntry()->create([
    //         'campaign_id' => $campaignId,
    //         'customer_id' => $this->record->customer_id,
    //         'customer_type' => 'doctor',
    //     ]);
    // }

    protected function afterCreate(): void
    {
        // Step 1: Get the campaign ID from the form data. This is correct.
        $campaignId = $this->data['campaign_id'];

        // Step 2: Get the actual customer—the Doctor—from the newly created microsite record.
        $doctor = $this->record->doctor;

        // Step 3: Create the campaign entry on the Doctor model's relationship.
        // We check if a doctor is actually associated before proceeding.
        if ($doctor) {
            $doctor->campaignEntries()->create([
                'entryable_type' => 'microsite',
                'entryable_id' => $this->record->id,
                'campaign_id' => $campaignId,
                // That's it! Eloquent's polymorphic relationship automatically fills
                // 'customerable_id' with the doctor's ID and
                // 'customerable_type' with 'App\Models\Doctor'.
            ]);

            // Step 4: Create doctor showcases if any were provided
            if (!empty($this->doctorShowcases)) {
                foreach ($this->doctorShowcases as $showcase) {
                    $doctor->showcases()->create([
                        'title' => $showcase['title'] ?? null,
                        'description' => $showcase['description'] ?? null,
                        'media_url' => $showcase['media_url'] ?? null,
                        'media_type' => 'video', // Since we only accept videos
                    ]);
                }
            }
        }
    }
}
