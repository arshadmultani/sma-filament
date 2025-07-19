<?php

namespace App\Models;

use App\Contracts\HeadquarterFilterable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Contracts\IsCampaignEntry;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Doctor;
use App\Models\Chemist;

/**
 * @property int $id
 * @property int $kofol_campaign_id
 * @property int $user_id
 * @property string $invoice_image
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property string $customer_type
 * @property int $customer_id
 * @property string $status
 * @property int|null $coupon_code
 * @property int $invoice_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $customer
 * @property-read \App\Models\KofolCampaign $kofolCampaign
 * @property-read int|null $products_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\KofolEntryCoupon[] $coupons
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereCouponCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereCustomerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereInvoiceAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereInvoiceImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereKofolCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolEntry whereUserId($value)
 *
 * @mixin \Eloquent
 */

#[ScopedBy(TeamHierarchyScope::class)]
class KofolEntry extends Model implements IsCampaignEntry, HeadquarterFilterable
{
    protected $guarded = [];
    protected $casts = [
        'products' => 'array',
        'invoice_image' => 'array',
    ];

    public function kofolCampaign()
    {
        return $this->belongsTo(KofolCampaign::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function customer()
    {
        return $this->morphTo();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function coupons()
    {
        return $this->hasMany(KofolEntryCoupon::class);
    }

    public function campaignEntry()
    {
        return $this->morphOne(CampaignEntry::class, 'entryable');
    }

    protected static function booted()
    {
        static::deleting(function ($kofolEntry) {
            $kofolEntry->campaignEntry()->delete();
        });
    }
    public function scopeWhereHeadquarterIn(Builder $query, array $headquarterIds): Builder
    {
        return $query->whereHasMorph(
            'customer',
            [Doctor::class, Chemist::class],
            fn (Builder $q) => $q->whereIn('headquarter_id', $headquarterIds)
        );
    }

    public function scopeWhereLocationIn(Builder $query, string $locationType, array $locationIds): Builder
    {
        return $query->whereHasMorph(
            'customer',
            [Doctor::class, Chemist::class],
            fn (Builder $q) => $q->whereHas('headquarter', function ($q2) use ($locationType, $locationIds) {
                switch ($locationType) {
                    case 'headquarter':
                        $q2->whereIn('id', $locationIds);
                        break;
                    case 'area':
                        $q2->whereIn('area_id', $locationIds);
                        break;
                    case 'region':
                        $q2->whereHas('area', function ($q3) use ($locationIds) {
                            $q3->whereIn('region_id', $locationIds);
                        });
                        break;
                    case 'zone':
                        $q2->whereHas('area.region', function ($q3) use ($locationIds) {
                            $q3->whereIn('zone_id', $locationIds);
                        });
                        break;
                }
            })
        );
    }
}
