<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Filament\Actions\SiteUrlAction;
use App\Filament\Actions\UpdateStatusAction;
use App\Filament\Actions\DownloadQrAction;
use App\Filament\Resources\MicrositeResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
        $actions[] = ActionGroup::make([
            Action::make('edit')
                ->icon('heroicon-o-pencil')
                ->color('primary')
                ->hidden(fn () => ! Gate::allows('update', $this->record))
                ->label('Edit')
                ->url(route('filament.admin.resources.microsites.edit', $this->record))
                ->color('primary'),
            SiteUrlAction::makeInfolist()->color('primary'),
            DownloadQrAction::makeInfolist()
            ->visible(fn ($record) => $record->is_active && $record->status === 'Approved')
            ->color('primary'),
        ])->icon('heroicon-m-bars-3-bottom-right');

        return $actions;
    }
}
