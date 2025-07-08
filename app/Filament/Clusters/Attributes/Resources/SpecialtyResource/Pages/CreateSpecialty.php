<?php

namespace App\Filament\Clusters\Attributes\Resources\SpecialtyResource\Pages;

use App\Filament\Clusters\Attributes\Resources\SpecialtyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialty extends CreateRecord
{
    protected static string $resource = SpecialtyResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
