<?php

namespace App\Filament\Resources\ManagerLogEntryResource\Pages;

use App\Filament\Resources\ManagerLogEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManagerLogEntry extends EditRecord
{
    protected static string $resource = ManagerLogEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     // Check if the value is changing from true to false
    //     if (
    //         $this->record->worked_with_team === true && // previously was team
    //         ($data['worked_with_team'] ?? false) === false // now is independent
    //     ) {
    //         $this->record->colleagues()->delete();
    //     }
    //     return $data;
    // }

    // public function afterSave()
    // {
    //     // If the record is now independent, delete all colleagues
    //     if ($this->record->worked_with_team === false) {
    //         $this->record->colleagues()->delete();
    //     }
    // }
}
