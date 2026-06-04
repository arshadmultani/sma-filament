<?php

namespace App\Filament\Resources\ArCreativeResource\Pages;

use App\Filament\Resources\ArCreativeResource;
use App\Models\ArCreative;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditArCreative extends EditRecord
{
    protected static string $resource = ArCreativeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Refuse to publish a creative that isn't compiled or won't track. The
     * tracking file + score are written by the in-browser compiler, so read the
     * freshest record from the database.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) === 'published') {
            $record = $this->getRecord()->fresh();
            $hasMarker = filled($data['marker_image_path'] ?? $record->marker_image_path);
            $hasVideo = filled($data['video_path'] ?? $record->video_path);

            if (! $hasMarker || ! $hasVideo || ! $record->mind_file_path) {
                $this->blockPublish('Add a marker image and a video, then compile the tracking file, before publishing.');
            } elseif (! $record->isTrackable()) {
                $this->blockPublish('This marker image isn’t trackable enough to publish (score below '.ArCreative::MIN_TRACKABLE_SCORE.'/100). Replace it with a more detailed, high-contrast image and re-compile.');
            }
        }

        return $data;
    }

    private function blockPublish(string $message): never
    {
        Notification::make()
            ->danger()
            ->title('Can’t publish yet')
            ->body($message)
            ->persistent()
            ->send();

        throw new Halt;
    }
}
