<?php

namespace App\Filament\Doctor\Resources\DoctorWebsiteResource\Pages;

use App\Models\POB;
use App\Models\State;
use Filament\Actions;
use App\Models\Doctor;
use App\Models\Campaign;
use App\Models\Microsite;
use Illuminate\Support\Str;
use App\Enums\StateCategory;
use Illuminate\Support\Facades\Log;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Scopes\CampaignVisibilityScope;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Filament\Doctor\Resources\DoctorWebsiteResource;

class CreateDoctorWebsite extends CreateRecord
{
    protected static string $resource = DoctorWebsiteResource::class;

    public ?Campaign $campaign = null;



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
                ->modalDescription('Are you sure you want to create this website? Please ensure all information is correct before proceeding.')
                ->modalSubmitActionLabel('Yes'),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $doctor = DoctorWebsiteResource::currentDoctor();

        unset($data['profile_photo']);
        unset($data['practice_since']);


        $parts = explode(' ', trim($doctor->name));

        if (isset($parts[0]) && in_array(strtolower(trim($parts[0])), ['dr', 'dr.'])) {
            array_shift($parts);
        }

        $slug = Str::slug(implode(' ', $parts));

        do {
            $random = Str::lower(Str::random(5));
            $url = $slug . '-' . $random;
        } while (Microsite::where('url', $url)->exists());

        $data['url'] = $url;

        $data['user_id'] = auth()->user()->id;
        $data['is_active'] = true;
        $data['state_id'] = State::where('category', StateCategory::FINALIZED)->first()->id;
        $data['headquarter_id'] = $doctor->headquarter_id;
        $data['doctor_id'] = $doctor->id;


        return $data;
    }

    protected function afterCreate(): void
    {
        $doctor = DoctorWebsiteResource::currentDoctor();

        $formData = $this->form->getState();

        Log::info($formData);

        if (isset($formData['profile_photo']) && $doctor) {
            $doctor->profile_photo = $formData['profile_photo'];
            $doctor->save();
        }
        if (isset($formData['practice_since']) && $doctor) {
            $doctor->practice_since = $formData['practice_since'];
            $doctor->save();
        }


        if ($this->campaign) {
            $this->record->campaignEntry()->create([
                'campaign_id' => $this->campaign->id,
                'customer_id' => $doctor->id,
                'customer_type' => Relation::getMorphAlias(Doctor::class),
            ]);
        }
    }

    protected function beforeCreate(): void
    {
        $this->campaign = Campaign::query()->withoutGlobalScopes()
            ->forEntryType(Relation::getMorphAlias(Microsite::class))
            ->active()
            ->first();

        if (!$this->campaign) {
            Notification::make()
                ->danger()
                ->title('You cannot create a website for now. ')
                ->body('This action is paused until further notice. Please contact our support team for more information.')
                ->send();

            throw new Halt();
        }
    }
}
