<?php

namespace App\Filament\Actions;

use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;

class ActivateUser
{

    public static function make(): Action
    {
        return Action::make('activate_user')
            ->label('Activate Account')
            ->color('primary')
            ->outlined()
            ->icon('heroicon-o-user-circle')
            ->requiresConfirmation()
            ->modalHeading('Activate Account')
            ->modalSubheading("Are you sure you want to activate this account? This will allow them to log in to the portal.")
            ->modalButton('Activate')
            ->action(function ($record, \App\Actions\User\ActivateUser $activateUser) {
                try {
                    $user = $record->doctor->userAccount();
                    if (!$user) {
                        throw new \Exception('No associated user account found.');
                    }
                    $activateUser->handle($user);
                    Notification::make()
                        ->title('User Activated')
                        ->body("The user's account has been activated successfully.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    \Log::error('Error activating user account: ' . $e->getMessage());

                    Notification::make()
                        ->title('Error')
                        ->body('There was an error activating the user account. Please try again later.')
                        ->danger()
                        ->send();
                }
            });
    }
}