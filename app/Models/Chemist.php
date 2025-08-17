<?php

namespace App\Models;

use App\Events\CustomerHeadquarterUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string|null $town
 * @property int $headquarter_id
 * @property int|null $user_id
 * @property string|null $type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Headquarter $headquarter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KofolEntry> $kofolEntries
 * @property-read int|null $kofol_entries_count
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereHeadquarterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereTown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chemist whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Chemist extends BaseModel
{
    use HasFactory, Notifiable;
    protected $fillable = ['name', 'phone', 'email', 'town', 'user_id', 'address', 'headquarter_id', 'type'];

    public function headquarter()
    {
        return $this->belongsTo(Headquarter::class);
    }

    public function kofolEntries()
    {
        return $this->morphMany(KofolEntry::class, 'customer');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function campaignEntries()
    {
        return $this->morphMany(CampaignEntry::class, 'customer');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'chemist_tag')->withTimestamps()->withPivot('user_id');
    }

    public function getRelationsToCheckForDelete()
    {
        return ['kofolEntries'];
    }
    protected static function booted()
    {
        parent::booted();
        static::deleting(function ($chemist) {
            $chemist->products()->detach();
            $chemist->tags()->detach();
        });
        static::updated(function ($chemist) {
            if ($chemist->isDirty('headquarter_id')) {
                event(new CustomerHeadquarterUpdated($chemist, $chemist->headquarter_id));
            }
        });
    }
}
