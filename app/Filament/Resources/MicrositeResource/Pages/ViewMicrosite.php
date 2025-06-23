<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Filament\Actions\SiteUrlAction;
use App\Filament\Actions\UpdateStatusAction;
use App\Filament\Resources\MicrositeResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ViewMicrosite extends ViewRecord
{
    protected static string $resource = MicrositeResource::class;

    public function getHeaderActions(): array
    {
        
        $actions = [];
        if (Gate::allows('updateStatus', $this->getRecord())) {
            $actions[] = UpdateStatusAction::make();
        }
        $actions[] = Action::make('edit')
            ->hidden(fn () => ! (Auth::user()->hasRole(['admin', 'super_admin']) || Auth::user()->id === $this->record->user_id))
            ->label('Edit')
            ->url(route('filament.admin.resources.microsites.edit', $this->record))
            ->color('gray');

        $actions[] = SiteUrlAction::makeInfolist();

        return $actions;
    }
}
