<?php

namespace App\Filament\Resources\DoctorWebsiteResource\Pages;

use App\Filament\Resources\DoctorWebsiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDoctorWebsites extends ListRecords
{
    protected static string $resource = DoctorWebsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Website';
    }
}
