<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Filament\Actions\SiteUrlAction;
use App\Filament\Actions\UpdateStatusAction;
use App\Filament\Actions\DownloadQrAction;
use App\Filament\Actions\UpdateStateAction;
use App\Filament\Resources\MicrositeResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ViewMicrosite extends ViewRecord
{
    protected static string $resource = MicrositeResource::class;

    public function getTitle(): string
    {
        return 'DW-' . $this->record->id;
    }

    public function getHeaderActions(): array
    {

        $actions = [];
        // if (Gate::allows('updateStatus', $this->getRecord())) {
        //     $actions[] = UpdateStateAction::make();
        // }
        $actions[] = ActionGroup::make([
            Action::make('edit')
                ->icon('heroicon-o-pencil')
                ->color('primary')
                ->hidden(fn() => !Gate::allows('update', $this->record))
                ->label('Edit')
                ->url(route('filament.admin.resources.microsites.edit', $this->record))
                ->color('primary'),

            SiteUrlAction::makeInfolist()->color('primary'),

            DownloadQrAction::makeInfolist()
                ->visible(fn($record) => $record->is_active && $record->state->isFinalized)
                ->color('primary'),

            Action::make('activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                ->visible(fn() => Gate::allows('activeStatus', $this->getRecord()))
                ->outlined()
                ->label(fn() => $this->record->is_active ? 'Deactivate Website' : 'Activate Website')
                ->color($this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalSubmitActionLabel('Yes, ' . ($this->record->is_active ? 'Deactivate' : 'Activate') . ' Website')
                ->modalHeading(fn() => $this->record->is_active ? 'Deactivate Website' : 'Activate Website')
                ->modalDescription(fn() => $this->record->is_active ? 'Are you sure you want to deactivate this website? It will no longer be accessible to users.' : 'Are you sure you want to activate this website? It will become accessible to users.')
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();
                    Notification::make()
                        ->success()
                        ->title($this->record->is_active ? 'Microsite Activated' : 'Microsite Deactivated')
                        ->body($this->record->is_active ? 'The website is now active.' : 'The website has been deactivated.')
                        ->send();
                })


        ])->icon('heroicon-m-bars-3-bottom-right');

        return $actions;
    }
}
