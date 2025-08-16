<?php

namespace App\Filament\Clusters\ActivityStatus\Resources\StatusResource\Pages;

use App\Filament\Clusters\ActivityStatus\Resources\StatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateStatus extends CreateRecord
{
    protected static string $resource = StatusResource::class;


    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function beforeCreate(): void
    // {
    //     $status = $this->record;
    //     $status->slug = Str::slug($status->name);
    // }
}
