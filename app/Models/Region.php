<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $zone_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Area> $areas
 * @property-read int|null $areas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \App\Models\Zone|null $zone
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereZoneId($value)
 * @mixin \Eloquent
 */
class Region extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'zone_id'];

    public function areas(){
        return $this->hasMany(Area::class);
    }
    public function users(){
        return $this->morphMany(User::class, 'location');
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
