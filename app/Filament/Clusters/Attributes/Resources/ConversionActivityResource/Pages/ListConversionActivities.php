<?php

namespace App\Filament\Clusters\Attributes\Resources\ConversionActivityResource\Pages;

use App\Filament\Clusters\Attributes\Resources\ConversionActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConversionActivities extends ListRecords
{
    protected static string $resource = ConversionActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
