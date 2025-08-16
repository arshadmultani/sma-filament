<?php

namespace App\Filament\Clusters\ActivityStatus\Resources\StatusResource\Pages;

use App\Filament\Clusters\ActivityStatus\Resources\StatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatuses extends ListRecords
{
    protected static string $resource = StatusResource::class;

    protected static ?string $title = 'Activity Status';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
