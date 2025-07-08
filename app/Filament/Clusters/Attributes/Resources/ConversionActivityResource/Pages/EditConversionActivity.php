<?php

namespace App\Filament\Clusters\Attributes\Resources\ConversionActivityResource\Pages;

use App\Filament\Clusters\Attributes\Resources\ConversionActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConversionActivity extends EditRecord
{
    protected static string $resource = ConversionActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
