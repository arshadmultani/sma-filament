<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $area_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Area $area
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chemist> $chemists
 * @property-read int|null $chemists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Doctor> $doctors
 * @property-read int|null $doctors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Headquarter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Headquarter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Headquarter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Headquarter whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Headquarter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Headquarter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Headquarter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Headquarter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Headquarter extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'area_id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function users()
    {
        return $this->morphMany(User::class, 'location');
    }

    public function chemists()
    {
        return $this->hasMany(Chemist::class);
    }
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
