<?php

namespace App\Filament\Resources\ChemistResource\Pages;

use App\Filament\Resources\ChemistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChemist extends EditRecord
{
    protected static string $resource = ChemistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
