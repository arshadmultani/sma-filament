<?php

namespace App\Filament\Resources\ChemistResource\Pages;

use App\Filament\Resources\ChemistResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Actions\UpdateStatusAction;
use Illuminate\Support\Facades\Gate;


class ViewChemist extends ViewRecord
{
    protected static string $resource = ChemistResource::class;


    public function getHeaderActions(): array
    {
        $actions = [];
        if (Gate::allows('updateStatus', $this->getRecord())) {
            $actions[] = UpdateStatusAction::make();
        }
        $actions[] = Action::make('edit')
            ->label('Edit')
            ->url(route('filament.admin.resources.chemists.edit', $this->record))
            ->color('gray');

        return $actions;
    }
}
