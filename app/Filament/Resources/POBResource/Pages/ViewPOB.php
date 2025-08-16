<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;


class ViewPOB extends ViewRecord
{
    protected static string $resource = POBResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if ($this->record && method_exists($this->record, 'getAvailableTransitions')) {
            $transitions = $this->record->getAvailableTransitions();
            foreach ($transitions as $transition) {
                $actions[] = Action::make($transition->action)
                    ->label($transition->action)
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function () use ($transition) {
                        $this->record->transitionTo($transition->toStatus);
                        Notification::make()->title('Status updated successfully')->success()->send();
                        $this->refreshFormData([]);
                    });
            }
        }
        return $actions;
    }
}
