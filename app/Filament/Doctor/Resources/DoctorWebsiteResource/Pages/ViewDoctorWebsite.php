<?php

namespace App\Filament\Doctor\Resources\DoctorWebsiteResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\FileUpload;
use App\Filament\Doctor\Resources\DoctorWebsiteResource;

class ViewDoctorWebsite extends ViewRecord
{
    protected static string $resource = DoctorWebsiteResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        $actions[] = Action::make('add_review')
            ->label('Patient Review')
            ->color('primary')
            ->outlined()
            ->icon('heroicon-o-plus')
            ->action(function (array $data) {
                $doctor = DoctorWebsiteResource::currentDoctor();

                if (!$this->record->is_active) {
                    Notification::make()
                        ->title('Cannot add review')
                        ->body('Your website is not active. Please contact support to activate it.')
                        ->danger()
                        ->send();
                } else {
                    // submit the form and add the review
                    $doctor->reviews()->create([
                        'is_verified' => false,
                        ...$data
                    ]);

                    Notification::make()
                        ->title('Review added successfully')
                        ->success()
                        ->send();
                }
            })
            ->form([
                TextInput::make('reviewer_name')
                    ->label('Patient Name')
                    ->placeholder('Name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('review_text')
                    ->label('Patient Text Review (optional)')
                    ->placeholder('Write your review here...')
                    ->maxLength(1000),
                FileUpload::make('media_url')
                    ->label('Patient Video Review (optional)')
                    ->distinct()
                    ->maxSize(10240) //TODO - make it configurable
                    ->disk('s3')
                    ->visibility('private')
                    ->directory('microsite/review_images')
                    ->maxFiles(1)
                    ->acceptedFileTypes(['video/mp4', 'video/x-m4v'])
                    ->helperText('If the patient has provided a video, please upload it here.'),
            ]);
        return $actions;
    }
}
