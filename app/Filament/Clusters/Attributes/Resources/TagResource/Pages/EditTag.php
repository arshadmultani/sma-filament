<?php

namespace App\Filament\Clusters\Attributes\Resources\TagResource\Pages;

use App\Filament\Clusters\Attributes\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\HandlesDeleteExceptions;


class EditTag extends EditRecord
{
    use HandlesDeleteExceptions;
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->before(fn($action, $record) => (new static())->tryDeleteRecord($record, $action)),
        ];
    }
}
