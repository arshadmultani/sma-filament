<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActivateUser
{

    public function handle(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $user->is_active = true;
            $user->save();

            return $user;
        });
    }
}