<?php

namespace App\Filament\Clusters\Attributes\Resources\QualificationResource\Pages;

use App\Filament\Clusters\Attributes\Resources\QualificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQualification extends EditRecord
{
    protected static string $resource = QualificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
