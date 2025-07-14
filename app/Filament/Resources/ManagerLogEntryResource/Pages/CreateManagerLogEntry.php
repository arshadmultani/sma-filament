<?php

namespace App\Filament\Resources\ManagerLogEntryResource\Pages;

use App\Filament\Resources\ManagerLogEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;

class CreateManagerLogEntry extends CreateRecord
{
    protected static string $resource = ManagerLogEntryResource::class;

    public function getTitle(): string
    {
        return 'New Track';
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submitWithConfirmation')
                ->label('Submit')
                ->requiresConfirmation()
                ->modalHeading('Are you sure you want to submit?')
                ->modalDescription('Please check data before submission. You cannot edit this later.')
                ->action(fn () => $this->create()),
            $this->getCancelFormAction(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        Log::info('mutateFormDataBeforeCreate',[$data]);

        return $data;
    }
    protected function afterCreate(): void
{
    $managerLogEntry = $this->record;
    $campaignId = $this->data['campaign_id'] ?? null;

    foreach ($managerLogEntry->activities as $activity) {
        if ($activity->customer_id && $activity->customer_type && $campaignId) {
            $managerLogEntry->campaignEntry()->create([
                'campaign_id'   => $campaignId,
                'customer_id'   => $activity->customer_id,
                'customer_type' => 'doctor',
            ]);
        }
    }
}
   
}
