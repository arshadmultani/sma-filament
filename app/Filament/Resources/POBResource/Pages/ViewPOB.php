<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource;
use Filament\Actions\Action;
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

        $actions[] = Action::make('edit')
            ->label('Edit')
            ->url(route('filament.admin.resources.kofol-entries.edit', $this->record))
            ->color('gray');

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
