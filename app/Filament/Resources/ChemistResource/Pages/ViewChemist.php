<?php

namespace App\Filament\Resources\ChemistResource\Pages;

use App\Filament\Resources\ChemistResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Actions\UpdateStatusAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

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
            ->hidden(fn() => !(Auth::user()->hasRole(['admin', 'super_admin']) || Auth::user()->id === $this->record->user_id))
            ->label('Edit')
            ->url(route('filament.admin.resources.chemists.edit', $this->record))
            // ->visible(fn() => Gate::allows('update', $this->getRecord()))
            ->color('gray');

        return $actions;
    }
}
