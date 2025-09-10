<?php

namespace App\Filament\Actions;

use App\Models\State;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ApproveAction
{
    public static function make(string $title, User|callable|null $recipient = null): Action
    {
        return Action::make('approve')
            ->label('Approve')
            ->color('primary')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->action(function (Model $record) use ($title, $recipient) {
                try {
                    $approvedState = State::finalized()->first();

                    if (! $approvedState) {
                        \Log::error('No finalized state found in database for approval action');

                        return;
                    }

                    $record->state_id = $approvedState->id;
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
                        ->color('success')
                        ->success()
                        ->send();

                    if ($user instanceof User) {
                        $notification->sendToDatabase($user);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error approving panel access request: '.$e->getMessage());
                }
            });
    }
}
