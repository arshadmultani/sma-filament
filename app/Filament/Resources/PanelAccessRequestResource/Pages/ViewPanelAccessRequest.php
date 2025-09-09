<?php

namespace App\Filament\Resources\PanelAccessRequestResource\Pages;

use App\Models\State;
use Filament\Actions;
use App\Enums\StateCategory;
use Filament\Actions\Action;
use App\Filament\Actions\NextAction;
use App\Filament\Actions\PreviousAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Pages\Concerns\CanPaginateViewRecord;
use App\Filament\Resources\PanelAccessRequestResource;

class ViewPanelAccessRequest extends ViewRecord
{
    use CanPaginateViewRecord;

    protected static string $resource = PanelAccessRequestResource::class;

    public function getTitle(): string
    {
        return 'PR-' . $this->record->id;
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        $actions[] = PreviousAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);
        $actions[] = NextAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);

        $actions[] = Action::make('approve')
            ->label('Approve')
            ->color('primary')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->action(function () {
                $this->record->state_id = State::where('category', StateCategory::FINALIZED)->value('id');
                $this->record->save();
            });

        $actions[] = Action::make('reject')
            ->label('Reject')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->action(function () {
                $this->record->state_id = State::where('category', StateCategory::CANCELLED)->value('id');
                $this->record->save();
            });

        return $actions;
    }
}
