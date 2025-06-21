<?php

namespace App\Livewire;

use App\Models\KofolCampaign;
use Livewire\Component;

class Heading extends Component
{
    public string $title;
    public int $campaignCount;

    public function mount(string $title = 'Your Campaigns')
    {
        $this->title = $title;
        $this->campaignCount = KofolCampaign::count();
    }

    public function render()
    {
        return view('livewire.heading');
    }
}
