<?php

namespace App\Models;

use App\Contracts\IsCampaignEntry;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;


#[ScopedBy(TeamHierarchyScope::class)]
class POB extends Model implements IsCampaignEntry
{
   
    protected $guarded=[];
    protected $table = 'pobs';

    public function campaignEntry()
    {
        return $this->morphOne(CampaignEntry::class, 'entryable');
    }

    public function customer()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function headquarter()
    {
        return $this->belongsTo(Headquarter::class, 'headquarter_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'p_o_b_product')->withPivot('quantity');
    }

}
