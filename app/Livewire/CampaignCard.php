<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class CampaignCard extends Component
{
    public $campaigns;

    public function mount()
    {
        $this->campaigns = Campaign::where('is_active', true)
            ->where('allowed_entry_type', 'kofol_entry')
            ->with(['entries.entryable.coupons'])
            ->withCount(['entries' => function ($query) {
                $query->whereHas('entryable');
            }])
            ->withCount(['entries as approved_entries_count' => function ($query) {
                $query->whereHas('entryable', function ($q) {
                    $q->where('status', 'Approved');
                });
            }])
            ->get();

        $this->campaigns->each(function ($campaign) {
            $campaign->total_amount = $campaign->entries->pluck('entryable')->whereNotNull()->where('status', 'Approved')->pluck('invoice_amount')->sum();
            $campaign->coupon_count = $campaign->entries->pluck('entryable')->whereNotNull()->where('status', 'Approved')->pluck('coupons')->flatten()->count();
        });
    }
    public function getDaysLeft($campaign)
    {
        $daysLeft = (int) now()->diffInDays($campaign->end_date, false);

        if ($daysLeft < 0) {
            return 'Ended';
        }

        if ($daysLeft === 0) {
            return 'Last Day';
        }

        return "{$daysLeft} " . str('day')->plural($daysLeft) . ' left';
    }
    public function render()
    {
        return view('livewire.campaign-card');
    }
}
