<?php

namespace App\Models;

use App\Contracts\IsCampaignEntry;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(TeamHierarchyScope::class)]
class POB extends Model implements IsCampaignEntry
{
    protected $guarded = [];

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

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, 'pob_product', 'pob_id', 'product_id')->withPivot('quantity');
    // }

    public function pobProducts(): HasMany
    {
        return $this->hasMany(POBProduct::class, 'pob_product', 'pob_id', 'product_id');
    }
}
