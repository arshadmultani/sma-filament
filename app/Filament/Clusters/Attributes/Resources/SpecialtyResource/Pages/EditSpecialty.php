<?php

namespace App\Filament\Clusters\Attributes\Resources\SpecialtyResource\Pages;

use App\Filament\Clusters\Attributes\Resources\SpecialtyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpecialty extends EditRecord
{
    protected static string $resource = SpecialtyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
