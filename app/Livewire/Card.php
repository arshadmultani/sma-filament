<?php

namespace App\Livewire;

use App\Models\KofolCampaign;
use App\Models\KofolEntry;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;


class Card extends Component
{
    public $campaign;
    public $amount;
    public $entries;

    public function mount()
    {
        $this->campaign = KofolCampaign::where('is_active', true)->latest()->first();
        if ($this->campaign) {
            $this->amount = KofolEntry::where('kofol_campaign_id', $this->campaign->id)->sum('invoice_amount');
            $this->entries = KofolEntry::where('kofol_campaign_id', $this->campaign->id)->count();
        }
    }
    public function getCountdownMessageProperty(): string
    {
        $end = Carbon::parse($this->campaign->end_date);
        if ($end->isPast()) {
            return 'Campaign ended';
        }

        $days = (int) $end->diffInDays(now(), true); // int, drops decimals :contentReference[oaicite:1]{index=1}

        return $days === 0
            ? 'Last day'
            : "{$days} days left";
    }
    public function getCampaignStatusProperty(): \Illuminate\Support\HtmlString
    {
        $isOver = Carbon::parse($this->campaign->end_date)->isPast();
        $text = $isOver ? 'Over' : 'Live';
        $dotClass = $this->campaign->is_active ? 'bg-primary' : 'bg-danger';

        $html = <<<HTML
<span class="inline-flex items-center space-x-2">
  <span class="h-2.5 w-2.5 rounded-full {$dotClass}"></span>
  <span>{$text}</span>
</span>
HTML;

        return new \Illuminate\Support\HtmlString($html);
    }

    public function getAmount(): string
    {
        return Number::currency($this->amount, in: 'INR',locale:'en_IN');
    }

    public function render()
    {
        return view('livewire.card');
    }
}
