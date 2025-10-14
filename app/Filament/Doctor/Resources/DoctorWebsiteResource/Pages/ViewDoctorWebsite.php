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

    public function getTitle(): string
    {
        return '';
    }
}
