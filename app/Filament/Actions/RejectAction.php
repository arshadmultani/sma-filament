<?php

namespace App\Filament\Actions;

use App\Models\State;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class RejectAction
{
    public static function make(string $title, User|callable|null $recipient = null): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->action(function (Model $record) use ($title, $recipient) {
                try {
                    $cancelledState = State::cancelled()->first();

                    if (! $cancelledState) {
                        \Log::error('No cancelled state found in database for rejection action');

                        return;
                    }

                    $record->state_id = $cancelledState->id;
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
                    \Log::error('Error rejecting panel access request: '.$e->getMessage());
                }
            });
    }
}
