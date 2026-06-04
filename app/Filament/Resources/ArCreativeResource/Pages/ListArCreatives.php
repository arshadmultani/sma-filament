<?php

namespace App\Filament\Resources\ArCreativeResource\Pages;

use App\Filament\Resources\ArCreativeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArCreatives extends ListRecords
{
    protected static string $resource = ArCreativeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
