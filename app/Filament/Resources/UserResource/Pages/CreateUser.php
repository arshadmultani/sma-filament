<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\SendUserCredentials;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $plainPassword = $this->plainPassword;
        $roleId = $data['roles'] ?? null;

        if ($roleId) {
            $roleName = Role::find($roleId)->name;

            if ($roleName === 'RSM') {
                $data['location_type'] = \App\Models\Region::class;
                $data['location_id'] = $data['region_id'] ?? null;
            } elseif ($roleName === 'ASM') {
                $data['location_type'] = \App\Models\Area::class;
                $data['location_id'] = $data['area_id'] ?? null;
            } elseif ($roleName === 'DSA') {
                $data['location_type'] = \App\Models\Headquarter::class;
                $data['location_id'] = $data['headquarter_id'] ?? null;
            }else if ($roleName === 'ZSM') {
                $data['location_type'] = \App\Models\Zone::class;
                $data['location_id'] = $data['zone_id'] ?? null;
            }
            else {
                $data['location_type'] = null;
                $data['location_id'] = null;
            }
        }

        // Remove these keys so they are not inserted as columns
        unset($data['roles'], $data['zone_id'], $data['region_id'], $data['area_id'], $data['headquarter_id']);


        $user = static::getModel()::create($data);

        // Assign roles if present
        if ($roleId) {
            $user->roles()->sync($roleId);
        }

        // Send notification with the plain password
        // Mail::to($user->email)->send(mailable: new SendUserCredentials($user->email, $data['password']));

        return $user;
    }

    public function saved()
    {
        if ($this->data['roles']) {
            $this->record->syncRoles([$this->data['roles']]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
