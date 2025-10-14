<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeactivateUser
{

    public function handle(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $user->is_active = false;
            $user->save();

            return $user;
        });
    }
}