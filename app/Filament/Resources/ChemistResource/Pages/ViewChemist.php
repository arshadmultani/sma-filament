<?php

namespace App\Filament\Resources\ChemistResource\Pages;

use App\Filament\Actions\UpdateStatusAction;
use App\Filament\Resources\ChemistResource;
use App\Filament\Actions\AddTagAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ViewChemist extends ViewRecord
{
    protected static string $resource = ChemistResource::class;

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getHeaderActions(): array
    {
        $actions = [];
        if (Gate::allows('updateStatus', $this->getRecord())) {
            $actions[] = UpdateStatusAction::make();
        }
        $actions[] = ActionGroup::make([
            AddTagAction::make(null, 'chemist', 'tags', 'Add Tag', 'Add Tag to Chemist')
            ->visible(fn()=>$this->record->status=='Approved'),
            Action::make('edit')
                ->icon('heroicon-m-pencil')
                ->hidden(fn() => !(Auth::user()->hasRole(['admin', 'super_admin']) || Auth::user()->id === $this->record->user_id))
                ->label('Edit')
                ->url(route('filament.admin.resources.chemists.edit', $this->record))
                // ->visible(fn() => Gate::allows('update', $this->getRecord()))
                ->color('primary'),
        ])->icon('heroicon-m-bars-3-bottom-right');

        return $actions;
    }
}
