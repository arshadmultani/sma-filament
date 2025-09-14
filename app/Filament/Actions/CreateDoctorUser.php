<?php

namespace App\Filament\Actions;

use App\Models\Headquarter;
use App\Models\User;
use App\Models\Doctor;
use PhpParser\Comment\Doc;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Permission\Models\Role;

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
                    $data = ([
                        'name' => $record->doctor->name,
                        'email' => $record->doctor->email,
                        'phone_number' => $record->doctor->phone,
                        'password' => Hash::make(substr($record->doctor->phone, -5)),
                        'userable_type' => Relation::getMorphAlias(Doctor::class),
                        'userable_id' => $record->doctor->id,
                        'division_id' => $record->doctor->headquarter->division_id,
                        'location_type' => Relation::getMorphedModel('headquarter'),
                        'location_id' => $record->doctor->headquarter->id,
                    ]);

                    Validator::make($data, [
                        'email' => 'required|email|unique:users,email',
                    ], [
                        'email.unique' => 'A user with this email ' . $record->doctor->email . ' already exists. Please change email before creating the account.'
                    ])->validate();

                    $user = User::firstOrCreate($data);
                    $role = Role::firstOrCreate(['name' => 'doctor']);

                    $user->assignRole($role);


                    Notification::make()
                        ->title('Doctor user created successfully.')
                        ->success()
                        ->send();
                } catch (ValidationException $e) {
                    // Handle validation failure nicely
                    Notification::make()
                        ->title('Failed to create doctor user.')
                        ->body($e->validator->errors()->first('email'))
                        ->danger()
                        ->send();

                } catch (\Exception $e) {
                    Log::error('Error creating doctor user: ' . $e->getMessage());

                    Notification::make()
                        ->title('Failed to create doctor user.')
                        // ->body('An error occurred while creating the user.')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }


            });

    }
}
