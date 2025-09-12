<?php

namespace App\Filament\Actions;

use App\Models\User;
use App\Models\Doctor;
use Filament\Notifications\Notification;
use PhpParser\Comment\Doc;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\Relation;

class CreateDoctorUser
{
    public static function make(): Action
    {
        return Action::make('create_doctor_user')
            ->label('Create Dr.\'s Login')
            ->color('info')
            ->requiresConfirmation()
            ->icon('heroicon-o-plus')
            ->modalIcon('heroicon-o-user-plus')
            ->modalHeading('Create Login Account for Doctor')
            ->modalSubmitActionLabel('Create Account')
            ->modalDescription("This action will create a login account for the doctor with last 5 digits of phone number as password.Kindly review the Portal Request and confirm.")
            ->action(function ($record) {
                try {
                    User::create([
                        'name' => $record->doctor->name,
                        'email' => $record->doctor->email,
                        'phone_number' => $record->doctor->phone,
                        'password' => Hash::make(substr($record->doctor->phone, -5)),
                        'userable_type' => Relation::getMorphAlias(Doctor::class),
                        'userable_id' => $record->doctor->id,
                        'division_id' => $record->doctor->headquarter->division_id
                    ]);
                    Notification::make()
                        ->title('Doctor user created successfully.')
                        ->body('A login has been created for Dr.' .
                            $record->doctor->name . ' with email ' . $record->doctor->email .
                            ' and password ' . $record->doctor->phone)
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Log::error('Error creating doctor user: ' . $e->getMessage());

                    Notification::make()
                        ->title('Failed to create doctor user.')
                        ->body('An error occurred while creating the user.')
                        ->danger()
                        ->send();
                }


            });

    }
}
