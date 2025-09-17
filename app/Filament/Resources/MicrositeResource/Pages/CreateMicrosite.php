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
use Illuminate\Support\Facades\Log;

class CreateMicrosite extends CreateRecord
{
    protected static string $resource = MicrositeResource::class;
    protected static bool $canCreateAnother = false;

    protected $doctorShowcases = [];
    protected $doctorReviews = [];



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $doctor = Doctor::find($data['doctor_id']);

        // if (!empty($data['profile_photo'])) {
        //     $doctor->profile_photo = $data['profile_photo'];
        //     $doctor->save();
        // }
        // unset($data['profile_photo']);


        // Split into parts
        $parts = explode(' ', trim($doctor->name));

        // Remove "Dr" or "Dr." prefix if present
        if (isset($parts[0]) && in_array(strtolower(trim($parts[0])), ['dr', 'dr.'])) {
            array_shift($parts); // remove the prefix
        }

        // Slugify the remaining full name
        $slug = Str::slug(implode(' ', $parts));

        // Generate unique URL
        do {
            $random = Str::lower(Str::random(5));
            $url = $slug . '-' . $random;
        } while (Microsite::where('url', $url)->exists());

        $data['url'] = $url;

        $data['user_id'] = Auth::user()->id;
        $data['is_active'] = true;
        $data['state_id'] = State::where('category', StateCategory::PENDING)->first()->id;
        $data['headquarter_id'] = $doctor->headquarter_id;


        if (isset($data['doctor']['reviews'])) {
            unset($data['doctor']['reviews']);
        }

        if (isset($data['doctor'])) {
            unset($data['doctor']);
        }

        if (isset($data['showcases_data'])) {
            $this->doctorShowcases = $data['showcases_data'];
            unset($data['showcases_data']);
        }

        if (isset($data['reviews'])) {
            $this->doctorReviews = $data['reviews'];
            unset($data['reviews']);
        }


        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Create Website')
                ->action(fn() => $this->create())
                ->keyBindings(['mod+s'])
                ->requiresConfirmation()
                ->modalHeading('Create Website')
                ->modalIcon('heroicon-o-globe-alt')
                ->modalDescription('Are you sure you want to create this website? You cannot edit this later. Only the doctor can do the changes. Please be sure all data is correct.')
                ->modalSubmitActionLabel('Yes, create it'),
            $this->getCancelFormAction(),
        ];
    }

    protected function afterCreate(): void
    {
        $campaignId = $this->data['campaign_id'];

        $doctor = $this->record->doctor;

        Log::info('Doctor Profile Photo: ', ['profile_photo' => $this->data['profile_photo']]);
        $doctor->profile_photo = reset($this->data['profile_photo']);
        // dd($doctor->profile_photo);
        $doctor->save();
        Log::info('Doctor profile photo  after: ', ['profile_photo' => $doctor->profile_photo]);

        if ($doctor) {
            $doctor->campaignEntries()->create([
                'entryable_type' => 'microsite',
                'entryable_id' => $this->record->id,
                'campaign_id' => $campaignId,
            ]);

            if (!empty($this->doctorReviews)) {
                foreach ($this->doctorReviews as $review) {
                    $doctor->reviews()->create([
                        'reviewer_name' => $review['reviewer_name'],
                        'is_verified' => false,
                        'state_id' => State::where('category', StateCategory::PENDING)->first()->id,
                        'review_text' => $review['review_text'],
                        'media_url' => $review['media_url'] ?? null,
                        'media_type' => 'video'
                    ]);
                }
            }

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
