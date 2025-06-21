<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Greeting extends Component
{
    public string $greeting;

    public function mount()
    {
        $this->setGreeting();
    }

    public function setGreeting()
    {
        $hour = now()->hour;
        $userName = Auth::user()?->name ?? 'User';

        if (str_contains($userName, ' ')) {
            $userName = explode(' ', $userName)[0];
        }

        if ($hour >= 5 && $hour < 12) {
            $this->greeting = "Good morning, {$userName}";
        } elseif ($hour >= 12 && $hour < 18) {
            $this->greeting = "Good afternoon, {$userName}";
        } else {
            $this->greeting = "Good evening, {$userName}";
        }
    }

    public function render()
    {
        return view('livewire.greeting');
    }
}
