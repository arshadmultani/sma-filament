<?php

namespace App\Models;

use App\Contracts\IsCampaignEntry;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ScopedBy(TeamHierarchyScope::class)]
class POB extends Model implements IsCampaignEntry
{
    protected $guarded = [];

    //    protected $table = 'pobs';

    public function campaignEntry(): MorphOne
    {
        return $this->morphOne(CampaignEntry::class, 'entryable');
    }

    public function customer(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function headquarter(): BelongsTo
    {
        return $this->belongsTo(Headquarter::class, 'headquarter_id');
    }

    public function pobProducts(): HasMany
    {
        return $this->hasMany(POBProduct::class);
    }
}
