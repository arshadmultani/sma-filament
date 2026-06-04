<?php

namespace App\Filament\Resources\ArCreativeResource\Pages;

use App\Filament\Resources\ArCreativeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateArCreative extends CreateRecord
{
    protected static string $resource = ArCreativeResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    /**
     * You can't publish before compiling, and compiling only happens on the edit
     * screen — so a brand-new creative is always forced to draft.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['status'] ?? null) === 'published') {
            $data['status'] = 'draft';

            Notification::make()
                ->warning()
                ->title('Saved as draft')
                ->body('Compile the tracking file on this screen, then publish.')
                ->send();
        }

        return $data;
    }
}
