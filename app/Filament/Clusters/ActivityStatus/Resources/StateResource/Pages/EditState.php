<?php

namespace App\Filament\Clusters\ActivityStatus\Resources\StateResource\Pages;

use App\Filament\Clusters\ActivityStatus\Resources\StateResource;
use Filament\Resources\Pages\EditRecord;

class EditState extends EditRecord
{
    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //            Actions\DeleteAction::make(),
        ];

    }

    //    protected function getFormActions(): array
    //    {
    //        return [
    //            EditAction::make(),
    //            DeleteAction::make(),
    //        ];
    //    }
}
