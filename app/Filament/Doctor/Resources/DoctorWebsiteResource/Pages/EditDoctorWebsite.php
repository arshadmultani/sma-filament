<?php

namespace App\Filament\Doctor\Resources\DoctorWebsiteResource\Pages;

use App\Filament\Doctor\Resources\DoctorWebsiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorWebsite extends EditRecord
{
    protected static string $resource = DoctorWebsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['profile_photo']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->saveProfilePhoto();
    }

    protected function saveProfilePhoto(): void // <-- Add this method
    {
        $doctor = DoctorWebsiteResource::currentDoctor();
        $formData = $this->form->getState();

        if (isset($formData['profile_photo']) && $doctor) {
            $doctor->profile_photo = $formData['profile_photo'];
            $doctor->save();
        }
    }
}




