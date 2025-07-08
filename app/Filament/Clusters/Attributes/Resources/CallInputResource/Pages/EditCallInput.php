<?php

namespace App\Filament\Clusters\Attributes\Resources\CallInputResource\Pages;

use App\Filament\Clusters\Attributes\Resources\CallInputResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCallInput extends EditRecord
{
    protected static string $resource = CallInputResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
