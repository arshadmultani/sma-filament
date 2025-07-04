<?php

namespace App\Filament\Resources\HeadquarterResource\Pages;

use App\Filament\Resources\HeadquarterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHeadquarter extends CreateRecord
{
    protected static string $resource = HeadquarterResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
