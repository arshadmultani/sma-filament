<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $zone_id
 * @property-read Collection<int, Area> $areas
 * @property-read int|null $areas_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @property-read Zone|null $zone
 *
 * @method static Builder<static>|Region newModelQuery()
 * @method static Builder<static>|Region newQuery()
 * @method static Builder<static>|Region query()
 * @method static Builder<static>|Region whereCreatedAt($value)
 * @method static Builder<static>|Region whereId($value)
 * @method static Builder<static>|Region whereName($value)
 * @method static Builder<static>|Region whereUpdatedAt($value)
 * @method static Builder<static>|Region whereZoneId($value)
 *
 * @mixin Eloquent
 */
class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'zone_id', 'division_id'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function users()
    {
        return $this->morphMany(User::class, 'location');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
