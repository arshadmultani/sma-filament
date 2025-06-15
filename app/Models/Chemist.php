<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
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
 * @mixin \Eloquent
 */
class Chemist extends BaseModel
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'headquarter_id','type'];

    public function headquarter(){
        return $this->belongsTo(Headquarter::class);
    }

 public function kofolEntries()
    {
        return $this->morphMany(KofolEntry::class, 'customer');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
