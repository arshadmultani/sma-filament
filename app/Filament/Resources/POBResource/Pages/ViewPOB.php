<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPOB extends ViewRecord
{
    protected static string $resource = POBResource::class;

    public function getTitle(): string
    {
        return 'POB-' . $this->record->id . '-' . $this->record->created_at->day;
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        $actions[] = EditAction::make();

        return $actions;
    }
}
