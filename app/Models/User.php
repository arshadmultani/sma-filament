<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $phone_number
 * @property int|null $division_id
 * @property string|null $location_type
 * @property int|null $location_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Division|null $division
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $location
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'division_id',
        'location_type',
        'location_id',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
    return $this->roles->isNotEmpty();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // Add head office role names here for DRY purposes
    public static function headOfficeRoleIds(): array
    {
        return Role::whereIn('name', ['admin', 'super_admin','PMT','GM','ZTM','Sales Manager'])->pluck('id')->toArray();
    }
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function location()
    {
        return $this->morphTo();
    }

    // Accessor for Zone ID
    public function getZoneIdAttribute()
    {
        $location = $this->location;
        if ($location instanceof \App\Models\Zone) {
            return $location->id;
        }
        if ($location instanceof \App\Models\Region) {
            return $location->zone_id;
        }
        if ($location instanceof \App\Models\Area) {
            return $location->region?->zone_id;
        }
        if ($location instanceof \App\Models\Headquarter) {
            return $location->area?->region?->zone_id;
        }
        return null;
    }

    // Accessor for Region ID
    public function getRegionIdAttribute()
    {
        $location = $this->location;
        if ($location instanceof \App\Models\Region) {
            return $location->id;
        }
        if ($location instanceof \App\Models\Area) {
            return $location->region_id;
        }
        if ($location instanceof \App\Models\Headquarter) {
            return $location->area?->region_id;
        }
        return null;
    }

    // Accessor for Area ID
    public function getAreaIdAttribute()
    {
        $location = $this->location;
        if ($location instanceof \App\Models\Area) {
            return $location->id;
        }
        if ($location instanceof \App\Models\Headquarter) {
            return $location->area_id;
        }
        return null;
    }

    // Accessor for Headquarter ID
    public function getHeadquarterIdAttribute()
    {
        $location = $this->location;
        if ($location instanceof \App\Models\Headquarter) {
            return $location->id;
        }
        return null;
    }

    // Accessor for Zone Name
    public function getZoneNameAttribute()
    {
        if ($this->location instanceof \App\Models\Zone) {
            return $this->location->name;
        } elseif ($this->location instanceof \App\Models\Region) {
            return $this->location->zone?->name;
        } elseif ($this->location instanceof \App\Models\Area) {
            return $this->location->region?->zone?->name;
        } elseif ($this->location instanceof \App\Models\Headquarter) {
            return $this->location->area?->region?->zone?->name;
        }
        return null;
    }

    // Accessor for Region Name
    public function getRegionNameAttribute()
    {
        if ($this->location instanceof \App\Models\Region) {
            return $this->location->name;
        } elseif ($this->location instanceof \App\Models\Area) {
            return $this->location->region?->name;
        } elseif ($this->location instanceof \App\Models\Headquarter) {
            return $this->location->area?->region?->name;
        }
        return null;
    }

    // Accessor for Area Name
    public function getAreaNameAttribute()
    {
        if ($this->location instanceof \App\Models\Area) {
            return $this->location->name;
        } elseif ($this->location instanceof \App\Models\Headquarter) {
            return $this->location->area?->name;
        }
        return null;
    }

    // Accessor for Headquarter Name
    public function getHeadquarterNameAttribute()
    {
        if ($this->location instanceof \App\Models\Headquarter) {
            return $this->location->name;
        }
        return null;
    }

    // Get managers (ASM, RSM, ZSM) for a user based on their location and role
    public function getManagers()
    {
        $managers = [];
        $divisionId = $this->division_id;
        $headquarterId = $this->getHeadquarterIdAttribute();
        $areaId = $this->getAreaIdAttribute();
        $regionId = $this->getRegionIdAttribute();
        $zoneId = $this->getZoneIdAttribute();

        if ($this->hasRole('DSA')) {
            // ASM: assigned to the same area and division
            $asm = self::whereHas('roles', fn($q) => $q->where('name', 'ASM'))
                ->where('location_type', 'App\\Models\\Area')
                ->where('location_id', $areaId)
                ->where('division_id', $divisionId)
                ->first();
            if ($asm) {
                $managers['ASM'] = $asm;
            }
            // RSM: assigned to the same region and division
            $rsm = self::whereHas('roles', fn($q) => $q->where('name', 'RSM'))
                ->where('location_type', 'App\\Models\\Region')
                ->where('location_id', $regionId)
                ->where('division_id', $divisionId)
                ->first();
            if ($rsm) {
                $managers['RSM'] = $rsm;
            }
            // ZSM: assigned to the same zone and division
            $zsm = self::whereHas('roles', fn($q) => $q->where('name', 'ZSM'))
                ->where('location_type', 'App\\Models\\Zone')
                ->where('location_id', $zoneId)
                ->where('division_id', $divisionId)
                ->first();
            if ($zsm) {
                $managers['ZSM'] = $zsm;
            }
        } elseif ($this->hasRole('ASM')) {
            // RSM: assigned to the same region and division
            $rsm = self::whereHas('roles', fn($q) => $q->where('name', 'RSM'))
                ->where('location_type', 'App\\Models\\Region')
                ->where('location_id', $regionId)
                ->where('division_id', $divisionId)
                ->first();
            if ($rsm) {
                $managers['RSM'] = $rsm;
            }
            // ZSM: assigned to the same zone and division
            $zsm = self::whereHas('roles', fn($q) => $q->where('name', 'ZSM'))
                ->where('location_type', 'App\\Models\\Zone')
                ->where('location_id', $zoneId)
                ->where('division_id', $divisionId)
                ->first();
            if ($zsm) {
                $managers['ZSM'] = $zsm;
            }
        } elseif ($this->hasRole('RSM')) {
            // ZSM: assigned to the same zone and division
            $zsm = self::whereHas('roles', fn($q) => $q->where('name', 'ZSM'))
                ->where('location_type', 'App\\Models\\Zone')
                ->where('location_id', $zoneId)
                ->where('division_id', $divisionId)
                ->first();
            if ($zsm) {
                $managers['ZSM'] = $zsm;
            }
        }
        return $managers;
    }

}
