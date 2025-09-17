<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Filament\Resources\MicrositeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMicrosite extends EditRecord
{
    protected static string $resource = MicrositeResource::class;

    protected $doctorShowcases = [];
    protected $doctorReviews = [];

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->doctor) {
            $showcases = $this->record->doctor->showcases()
                ->select(['title', 'description', 'media_url'])
                ->get()
                ->toArray();

            $data['showcases_data'] = $showcases;
            $data['has_any_showcase'] = !empty($showcases) ? 'yes' : 'no';


            $reviews = $this->record->doctor->reviews()
                ->select(['reviewer_name', 'review_text', 'rating'])
                ->get()
                ->toArray();

            $data['reviews'] = $reviews;
            $data['has_any_reviews'] = !empty($reviews) ? 'yes' : 'no';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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

    protected function afterSave(): void
    {
        if (isset($this->doctorShowcases) && $this->record->doctor) {
            $this->record->doctor->showcases()->delete();
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

        if (isset($this->doctorReviews) && $this->record->doctor) {
            $this->record->doctor->reviews()->delete();
            foreach ($this->doctorReviews as $review) {
                $this->record->doctor->reviews()->create([
                    'reviewer_name' => $review['reviewer_name'],
                    'review_text' => $review['review_text'],
                ]);
            }
        }

        if (isset($this->data['profile_photo']) && $this->record->doctor) {
            $this->record->doctor->profile_photo = reset($this->data['profile_photo']);
            $this->record->doctor->save();
        }
    }
}
