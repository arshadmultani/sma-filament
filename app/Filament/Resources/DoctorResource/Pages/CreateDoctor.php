<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    protected static bool $canCreateAnother = false;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        if (Auth::user()->hasRole('DSA')) {
            $data['headquarter_id'] = Auth::user()->location_id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $doctor = $this->record;
        $tags = $this->data['tags'] ?? [];

        if (!empty($tags)) {
            $syncData = [];
            foreach ($tags as $tagId) {
                $syncData[$tagId] = ['user_id' => Auth::id()];
            }
            $doctor->tags()->sync($syncData);
        }
    }
}
