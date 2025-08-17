<?php

namespace App\Filament\Clusters\ActivityStatus\Resources\StateResource\Pages;

use App\Filament\Clusters\ActivityStatus\Resources\StateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListState extends ListRecords
{
    protected static string $resource = StateResource::class;

    protected static ?string $title = 'Activity State';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
