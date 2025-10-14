<?php

namespace App\Filament\Resources\PanelAccessRequestResource\Pages;

use App\Filament\Resources\PanelAccessRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPanelAccessRequest extends EditRecord
{
    protected static string $resource = PanelAccessRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
