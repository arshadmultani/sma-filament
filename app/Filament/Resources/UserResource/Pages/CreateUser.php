<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Mail\SendUserCredentials;
use Illuminate\Support\Facades\Mail;
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;


    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
{
    $plainPassword = $this->plainPassword;

    $user = static::getModel()::create($data);

    // Assign roles if present
    if (isset($data['roles'])) {
        $user->roles()->sync($data['roles']);
    }

    // Send notification with the plain password
    Mail::to($user->email)->send(mailable: new SendUserCredentials($user->email, $data['password']));

    return $user;
}

}
