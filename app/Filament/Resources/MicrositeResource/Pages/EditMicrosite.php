<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Filament\Resources\MicrositeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMicrosite extends EditRecord
{
    protected static string $resource = MicrositeResource::class;

    protected $doctorShowcases = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing showcases for the doctor
        if ($this->record->doctor) {
            $showcases = $this->record->doctor->showcases()
                ->select(['title', 'description', 'media_url'])
                ->get()
                ->toArray();

            $data['showcases_data'] = $showcases;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store doctor showcases separately to handle after save
        if (isset($data['showcases_data'])) {
            $this->doctorShowcases = $data['showcases_data'];
            unset($data['showcases_data']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Update doctor showcases if any were provided
        if (isset($this->doctorShowcases) && $this->record->doctor) {
            // Delete existing showcases
            $this->record->doctor->showcases()->delete();

            // Create new showcases
            foreach ($this->doctorShowcases as $showcase) {
                if (!empty($showcase['media_url'])) {
                    $this->record->doctor->showcases()->create([
                        'title' => $showcase['title'] ?? null,
                        'description' => $showcase['description'] ?? null,
                        'media_url' => $showcase['media_url'],
                        'media_type' => 'video',
                    ]);
                }
            }
        }
    }
}
