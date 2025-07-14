<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    public function getTitle(): string
    {
        return 'Campaign: '.$this->record->name;

    }
    public function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->label('Edit')
                ->url(route('filament.admin.resources.campaigns.edit', $this->record))
                ->visible(fn(): bool => Auth::user()->can('update', $this->record)),
        ];
    }
}
