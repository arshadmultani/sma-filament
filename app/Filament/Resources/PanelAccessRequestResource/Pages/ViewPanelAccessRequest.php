<?php

namespace App\Filament\Resources\PanelAccessRequestResource\Pages;

use App\Models\User;
use App\Models\State;
use Filament\Actions;
use App\Enums\StateCategory;
use Filament\Actions\Action;
use App\Filament\Actions\NextAction;
use Filament\Forms\Components\Textarea;
use App\Filament\Actions\PreviousAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Pages\Concerns\CanPaginateViewRecord;
use App\Filament\Resources\PanelAccessRequestResource;
use Illuminate\Support\Facades\Log;

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
            ->hidden($this->record->state->isFinalized)
            ->color('primary')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->action(function () {
                $this->record->state_id = State::where('category', StateCategory::FINALIZED)->value('id');
                $this->record->reviewed_at = now();
                $this->record->reviewed_by = auth()->id();
                $this->record->save();

                Notification::make()
                    ->title('Portal Access Request Approved')
                    ->color('success')
                    ->success()
                    ->send();
                $requester = User::find($this->record->requester);
                if ($requester) {
                    Notification::make()
                        ->title('Portal Access Request Approved! 🎉')
                        ->body($this->record->doctor->name . 'now has portal login.')
                        ->color('success')
                        ->success()
                        ->icon('heroicon-o-check-circle')
                        ->sendToDatabase($requester);
                }


            });

        $actions[] = Action::make('reject')
            ->label('Reject')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()

            ->hidden($this->record->state->isCancelled)
            ->form([
                Textarea::make('rejection_reason')
                    ->label('Reason for Rejection')
                    ->required()
                    ->maxLength(200)
                    ->placeholder('Provide reason for rejection'),
            ])
            ->action(function ($record, $data) {
                $record->state_id = State::where('category', StateCategory::CANCELLED)->value('id');
                $record->rejection_reason = $data['rejection_reason'];
                $record->reviewed_at = now();
                $record->save();

                Notification::make()
                    ->title('Portal Access Request Rejected')
                    ->color('danger')
                    ->danger()
                    ->send();
                $requester = User::find($this->record->requester);
                if ($requester) {
                    Notification::make()
                        ->title('Portal Access Request Rejected')
                        ->body('Your portal access request for ' . $this->record->doctor->name . ' has been rejected with reason: ' . $data['rejection_reason'])
                        ->color('danger')
                        ->danger()
                        ->icon('heroicon-o-x-circle')
                        ->sendToDatabase($requester);
                }
            });

        return $actions;
    }
}
