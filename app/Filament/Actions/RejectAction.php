<?php

namespace App\Filament\Actions;

use App\Models\User;
use App\Models\State;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class RejectAction
{
    public static function make(string $title, User|callable|null $recipient = null): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->form([
                TextInput::make('reason')
                    ->label('Reason for Rejection')
                    ->required()
                    ->maxLength(50)
            ])
            ->action(function (Model $record, array $data) use ($title, $recipient) {
                try {
                    $cancelledState = State::cancelled()->first();

                    if (!$cancelledState) {
                        \Log::error('No cancelled state found in database for rejection action');

                        return;
                    }

                    $record->state_id = $cancelledState->id;
                    $record->rejection_reason = $data['reason'];
                    $record->reviewed_by = auth()->id();
                    $record->reviewed_at = now();
                    $record->save();

                    // Resolve recipient
                    $user = null;
                    if ($recipient instanceof \Closure) {
                        $user = $recipient($record);
                    } elseif ($recipient instanceof User) {
                        $user = $recipient;
                    }

                    // Send notification
                    $notification = Notification::make()
                        ->title($title)
                        ->color('danger')
                        ->danger()
                        ->send();

                    if ($user instanceof User) {
                        $notification->sendToDatabase($user);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error rejecting panel access request: ' . $e->getMessage());
                }
            });
    }
}
