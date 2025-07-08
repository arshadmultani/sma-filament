<?php

namespace App\Filament\Clusters\Attributes\Resources\CallInputResource\Pages;

use App\Filament\Clusters\Attributes\Resources\CallInputResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCallInputs extends ListRecords
{
    protected static string $resource = CallInputResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
