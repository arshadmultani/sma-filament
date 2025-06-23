<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Doctor;
use App\Models\Chemist;

class CustomerCard extends Component
{
    public $doctorCount;
    public $chemistCount;
    public $pendingDoctors;
    public $pendingChemists;
    
    public function mount()
    {
        $this->doctorCount = Doctor::count();
        $this->chemistCount = Chemist::count();
        $this->pendingDoctors = Doctor::where('status', 'pending')->count();
        $this->pendingChemists = Chemist::where('status', 'pending')->count();
    }

    public function render()
    {
        return view('livewire.customer-card');
    }
}
