<?php

namespace App\Filament\Clusters\ActivityStatus\Resources\StatusResource\Pages;

use App\Filament\Clusters\ActivityStatus\Resources\StatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatus extends EditRecord
{
    protected static string $resource = StatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
