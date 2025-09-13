<?php

namespace App\Filament\Actions;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Notifications\DoctorWelcomeNotification;
use Filament\Infolists\Components\Actions\Action;

class SendDoctorWelcomeEmailAction
{
    public static function make(string $label = null): Action
    {
        return Action::make('welcomeEmail')
            ->label($label ?? 'Send Welcome Email')
            ->color('info')
            ->icon('heroicon-o-envelope')
            // ->outlined()
            ->requiresConfirmation()
            ->modalHeading('Send Welcome Email')
            ->modalDescription('This will send a welcome email to the doctor with login instructions.')
            ->modalSubmitActionLabel('Yes, send email')
            ->action(function (Action $action, Model $record) {
                try {
                    $doctor = $record->doctor;
                    $userAccount = $doctor->userAccount();

                    if (!$userAccount) {
                        Notification::make()
                            ->title('Error')
                            ->body('Doctor does not have a user account')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Send welcome notification
                    $doctor->notify(new DoctorWelcomeNotification(
                        $userAccount->email,
                        $doctor->phone
                    ));

                    Notification::make()
                        ->title('Success')
                        ->body('Welcome email sent successfully to ' . $doctor->name)
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    \Log::error('Error sending welcome email: ' . $e->getMessage());

                    Notification::make()
                        ->title('Error')
                        ->body('Failed to send welcome email: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
