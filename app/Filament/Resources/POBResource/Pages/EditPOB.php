<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPOB extends EditRecord
{
    protected static string $resource = POBResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
