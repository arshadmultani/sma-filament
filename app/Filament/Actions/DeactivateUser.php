<?php

namespace App\Filament\Actions;

use App\Models\User;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;

class DeactivateUser
{

    public static function make(): Action
    {

        return Action::make('deactivate_user')
            ->label('Deactivate User')
            ->color('danger')
            ->outlined()
            ->requiresConfirmation()
            ->visible(fn($record) => $record->doctor->userAccount()->is_active)
            ->icon('heroicon-o-user-minus')
            ->modalIcon('heroicon-o-user-minus')
            ->modalHeading('Deactivate User Account')
            ->modalSubmitActionLabel('Deactivate Account')
            ->modalDescription("This action will deactivate the user's login account temporarily and will prevent access to the portal. You can reactivate it later if needed.")
            ->action(function ($record, \App\Actions\User\DeactivateUser $deactivateUser) {
                try {

                    $user = $record->doctor->userAccount();
                    if (!$user) {
                        throw new \Exception('No associated user account found.');
                    }
                    $deactivateUser->handle($user);

                    Notification::make()
                        ->title('User Deactivated')
                        ->body("The user's account has been deactivated successfully.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    \Log::error('Error deactivating user account: ' . $e->getMessage());

                    Notification::make()
                        ->title('Error')
                        ->body('There was an error deactivating the user account. Please try again later.')
                        ->danger()
                        ->send();
                }
            });
    }
}