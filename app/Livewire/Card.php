<?php

namespace App\Livewire;

use App\Models\KofolCampaign;
use App\Models\KofolEntry;
use Livewire\Component;

class Card extends Component
{
    public $campaign;
    public $participants;
    public $entriesToday;

    public function mount()
    {
        $this->campaign = KofolCampaign::where('is_active', true)->latest()->first();
        if ($this->campaign) {
            $this->participants = KofolEntry::where('kofol_campaign_id', $this->campaign->id)->distinct('user_id')->count();
            $this->entriesToday = KofolEntry::where('kofol_campaign_id', $this->campaign->id)->whereDate('created_at', today())->count();
        }
    }

    public function render()
    {
        return view('livewire.card');
    }
}
