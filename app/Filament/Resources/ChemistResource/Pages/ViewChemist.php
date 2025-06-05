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
        $actions[] = UpdateStatusAction::make()
            ->visible(fn() => Gate::allows('updateStatus', $this->getRecord()));
        $actions[] = Action::make('edit')
            ->label('Edit')
            ->url(route('filament.admin.resources.chemists.edit', $this->record))
            ->visible(fn() => Gate::allows('update', $this->getRecord()))
            ->color('gray');

        return $actions;
    }
}
