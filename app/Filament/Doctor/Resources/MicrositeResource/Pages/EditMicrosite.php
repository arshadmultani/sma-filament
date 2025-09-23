<?php

namespace App\Filament\Doctor\Resources\MicrositeResource\Pages;

use App\Filament\Doctor\Resources\MicrositeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMicrosite extends EditRecord
{
    protected static string $resource = MicrositeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
