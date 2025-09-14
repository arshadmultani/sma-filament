<?php

namespace App\Filament\Actions;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Notifications\DoctorWelcomeNotification;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Support\Facades\DB;

class SendDoctorWelcomeEmailAction
{
    public static function make(string $label = null): Action
    {
        return Action::make('welcomeEmail')
            ->label($label ?? 'Send Welcome Email')
            ->color('info')
            ->icon('heroicon-o-envelope')
            ->requiresConfirmation()
            ->modalHeading('Send Welcome Email')
            ->modalDescription('This will send a welcome email to the doctor with login instructions.')
            ->modalSubmitActionLabel('Yes, send email')
            ->action(function (Action $action, Model $record) {
                try {
                    if (!$record->doctor) {
                        Notification::make()
                            ->title('Error')
                            ->body('Doctor not found for this record')
                            ->danger()
                            ->send();

                        throw new \Exception('Doctor not found for this record');
                    }
                    $doctor = $record->doctor;
                    $userAccount = $doctor->userAccount();

                    if (!$userAccount || !$userAccount->email) {
                        Notification::make()
                            ->title('Error')
                            ->body('Doctor does not have a valid user account or email')
                            ->danger()
                            ->send();
                        return;
                    }

                    if ($record->email_sent_at) {
                        Notification::make()
                            ->title('Info')
                            ->body('Welcome email was already sent')
                            ->warning()
                            ->send();
                        return;
                    }


                    DB::transaction(function () use ($record, $doctor, $userAccount) {

                        $doctor->notify(new DoctorWelcomeNotification(
                            $userAccount->email,
                            $doctor->phone
                        ));


                        $record->updateOrFail([
                            'email_sent_at' => now(),
                            'email_sent_by' => auth()->user()->id,
                        ]);

                    });

                    Notification::make()
                        ->title('Success')
                        ->body('Welcome email sent successfully to ' . $doctor->name)
                        ->success()
                        ->send();
                } catch (\Exception $e) {

                    \Log::error('Error sending welcome email: ' . $e->getMessage());

                    Notification::make()
                        ->title('Error')
                        ->body('Failed to send email. Please try again.')
                        ->danger()
                        ->send();
                }
            });
    }
}
