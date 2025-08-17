<?php

namespace App\Filament\Clusters\ActivityStatus\Resources\StateResource\Pages;

use Filament\Actions;
use App\Traits\HandlesDeleteExceptions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Clusters\ActivityStatus\Resources\StateResource;
use Filament\Actions\DeleteAction as ActionsDeleteAction;

class EditState extends EditRecord
{
    use HandlesDeleteExceptions;
    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(fn($action, $record) => (new static())->tryDeleteRecord($record, $action)),
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
