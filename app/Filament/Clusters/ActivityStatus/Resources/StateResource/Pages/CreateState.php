<?php

namespace App\Filament\Clusters\ActivityStatus\Resources\StateResource\Pages;

use App\Filament\Clusters\ActivityStatus\Resources\StateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateState extends CreateRecord
{
    protected static string $resource = StateResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
