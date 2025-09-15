<?php

namespace App\Filament\Resources\DoctorWebsiteResource\Pages;

use App\Filament\Resources\DoctorWebsiteResource;
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
}
