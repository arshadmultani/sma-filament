<?php

namespace Tests\Support;

use App\Models\User;
use Filament\Models\Contracts\FilamentUser;

trait AuthenticatesFilamentUsers
{
    public function loginAsAdmin(): User
    {
        $user = User::factory()->create([
            'email' => 'arshadrmultani@gmail.com',
        ]);

        // If you're using policies or roles, ensure the user passes them
        $this->actingAs($user);

        return $user;
    }
}
