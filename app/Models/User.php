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
use Illuminate\Support\Facades\Auth;

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
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    // Location type constants
    public const TYPE_ZONE = \App\Models\Zone::class;
    public const TYPE_REGION = \App\Models\Region::class;
    public const TYPE_AREA = \App\Models\Area::class;
    public const TYPE_HEADQUARTER = \App\Models\Headquarter::class;

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
        return Role::whereIn(
            'name',
            ['admin', 'super_admin', 'PMT', 'GM', 'ZTM', 'Sales Manager']
        )->pluck('id')->toArray();
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function location()
    {
        return $this->morphTo();
    }

    public function pobs()
    {
        return $this->hasMany(POB::class);
    }

    // Helper to get user IDs by location type, location IDs, and division
    private function getUserIdsByLocation(string $locationType, $locationIds, $divisionId = null)
    {
        $query = self::where('location_type', $locationType)
            ->whereIn('location_id', $locationIds);
        if ($divisionId !== null) {
            $query->where('division_id', $divisionId);
        }
        return $query->pluck('id');
    }

    // Generic accessor for location IDs by type
    public function getLocationIdByType(string $type)
    {
        $location = $this->location;
        switch ($type) {
            case self::TYPE_ZONE:
                return $location instanceof \App\Models\Zone ? $location->id : ($location instanceof \App\Models\Region ? $location->zone_id : ($location instanceof \App\Models\Area ? $location->region?->zone_id : ($location instanceof \App\Models\Headquarter ? $location->area?->region?->zone_id : null)));
            case self::TYPE_REGION:
                return $location instanceof \App\Models\Region ? $location->id : ($location instanceof \App\Models\Area ? $location->region_id : ($location instanceof \App\Models\Headquarter ? $location->area?->region_id : null));
            case self::TYPE_AREA:
                return $location instanceof \App\Models\Area ? $location->id : ($location instanceof \App\Models\Headquarter ? $location->area_id : null);
            case self::TYPE_HEADQUARTER:
                return $location instanceof \App\Models\Headquarter ? $location->id : null;
        }
        return null;
    }

    // Accessors using the generic method
    public function getZoneIdAttribute()
    {
        return $this->getLocationIdByType(self::TYPE_ZONE);
    }

    public function getRegionIdAttribute()
    {
        return $this->getLocationIdByType(self::TYPE_REGION);
    }

    public function getAreaIdAttribute()
    {
        return $this->getLocationIdByType(self::TYPE_AREA);
    }

    public function getHeadquarterIdAttribute()
    {
        return $this->getLocationIdByType(self::TYPE_HEADQUARTER);
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

    /**
     * Get all direct team members under a manager's location and division.
     * Returns an array keyed by role (e.g., ['ASM' => [...], 'DSA' => [...]])
     *
     * @return array<string, \Illuminate\Database\Eloquent\Collection>
     */
    public function getTeam(): array
    {
        $team = [];
        $divisionId = $this->division_id;
        $zoneId = $this->getZoneIdAttribute();
        $regionId = $this->getRegionIdAttribute();
        $areaId = $this->getAreaIdAttribute();

        // If no location, return all users except self, grouped by role (for Head Office Roles)
        if (!$this->location_type || !$this->location_id) {
            $allUsers = self::where('id', '!=', $this->id)->with('roles')->get();
            foreach ($allUsers as $user) {
                foreach ($user->roles as $role) {
                    $team[$role->name][] = $user;
                }
            }
            foreach ($team as $role => $users) {
                $team[$role] = collect($users)->unique('id');
            }
            return $team;
        }

        // Role mapping: manager role => [team role => [locationType, locationIds]]
        $roleMap = [
            'ZSM' => [
                'RSM' => ['App\\Models\\Region', $zoneId ? $this->getRegionIdsByZone($zoneId) : []],
                'ASM' => ['App\\Models\\Area', $zoneId ? $this->getAreaIdsByZone($zoneId) : []],
                'DSA' => ['App\\Models\\Headquarter', $zoneId ? $this->getHeadquarterIdsByZone($zoneId) : []],
            ],
            'RSM' => [
                'ZSM' => ['App\\Models\\Zone', $regionId ? $this->getZoneIdsByRegion($regionId) : []],
                'ASM' => ['App\\Models\\Area', $regionId ? $this->getAreaIdsByRegion($regionId) : []],
                'DSA' => ['App\\Models\\Headquarter', $regionId ? $this->getHeadquarterIdsByRegion($regionId) : []],
            ],
            'ASM' => [
                'ZSM' => ['App\\Models\\Zone', $areaId ? $this->getZoneIdsByArea($areaId) : []],
                'RSM' => ['App\\Models\\Region', $areaId ? $this->getRegionIdsByArea($areaId) : []],
                'DSA' => ['App\\Models\\Headquarter', $areaId ? $this->getHeadquarterIdsByArea($areaId) : []],
            ],
            'DSA' => [
                'ZSM' => ['App\\Models\\Zone', $this->getZoneIdAttribute() ? [$this->getZoneIdAttribute()] : []],
                'RSM' => ['App\\Models\\Region', $this->getRegionIdAttribute() ? [$this->getRegionIdAttribute()] : []],
                'ASM' => ['App\\Models\\Area', $this->getAreaIdAttribute() ? [$this->getAreaIdAttribute()] : []],
                'DSA' => [$this->location_type, $this->location_id ? [$this->location_id] : []],
            ],
        ];

        foreach ($roleMap as $managerRole => $teamRoles) {
            if ($this->hasRole($managerRole)) {
                foreach ($teamRoles as $teamRole => [$locationType, $locationIds]) {
                    if (!empty($locationIds)) {
                        $members = $this->findTeamMembers($teamRole, $locationType, $locationIds, $divisionId);
                        // For DSA, exclude self
                        if ($managerRole === 'DSA') {
                            $members = $members->where('id', '!=', $this->id);
                        }
                        if ($members->isNotEmpty()) {
                            $team[$teamRole] = $members;
                        }
                    }
                }
                break; // Only one manager role applies
            }
        }
        return $team;
    }

    /**
     * Helper to find team members by role, location type, location IDs, and division.
     */
    private function findTeamMembers(string $role, string $locationType, array $locationIds, $divisionId)
    {
        return self::whereHas('roles', fn($q) => $q->where('name', $role))
            ->where('location_type', $locationType)
            ->whereIn('location_id', $locationIds)
            ->where('division_id', $divisionId)
            ->get();
    }

    // Helper methods to get IDs for location scoping
    private function getZoneIdsByRegion($regionId): array
    {
        return [\App\Models\Region::find($regionId)?->zone_id];
    }

    private function getZoneIdsByArea($areaId): array
    {
        $regionIds = \App\Models\Area::whereIn('id', (array) $areaId)->pluck('region_id')->toArray();
        return \App\Models\Region::whereIn('id', $regionIds)->pluck('zone_id')->unique()->toArray();
    }

    private function getRegionIdsByArea($areaId): array
    {
        return \App\Models\Area::whereIn('id', (array) $areaId)->pluck('region_id')->unique()->toArray();
    }

    private function getRegionIdsByZone($zoneId): array
    {
        return \App\Models\Region::where('zone_id', $zoneId)->pluck('id')->toArray();
    }

    private function getAreaIdsByZone($zoneId): array
    {
        return \App\Models\Area::whereIn('region_id', $this->getRegionIdsByZone($zoneId))->pluck('id')->toArray();
    }

    private function getHeadquarterIdsByZone($zoneId): array
    {
        return \App\Models\Headquarter::whereIn('area_id', $this->getAreaIdsByZone($zoneId))->pluck('id')->toArray();
    }

    private function getAreaIdsByRegion($regionId): array
    {
        return \App\Models\Area::where('region_id', $regionId)->pluck('id')->toArray();
    }

    private function getHeadquarterIdsByRegion($regionId): array
    {
        return \App\Models\Headquarter::whereIn(
            'area_id',
            $this->getAreaIdsByRegion($regionId)
        )->pluck('id')->toArray();
    }

    private function getHeadquarterIdsByArea($areaId): array
    {
        return \App\Models\Headquarter::where('area_id', $areaId)->pluck('id')->toArray();
    }

    /**
     * Get all doctors visible to this user based on their location.
     * If location_type or location_id is null (admin/super_admin), return all doctors.
     */
    public function getDoctors()
    {
        if (!$this->location_type || !$this->location_id) {
            // For admin/super_admin, return all doctors
            return \App\Models\Doctor::all();
        }

        // If user is at headquarter level
        if ($this->location_type === 'App\\Models\\Headquarter') {
            return \App\Models\Doctor::where('headquarter_id', $this->location_id)->get();
        }

        // If user is at area, region, or zone, get all headquarters in that location, then all doctors in those HQs
        $hqIds = [];

        if ($this->location_type === 'App\\Models\\Area') {
            $hqIds = \App\Models\Headquarter::where('area_id', $this->location_id)->pluck('id');
        } elseif ($this->location_type === 'App\\Models\\Region') {
            $areaIds = \App\Models\Area::where('region_id', $this->location_id)->pluck('id');
            $hqIds = \App\Models\Headquarter::whereIn('area_id', $areaIds)->pluck('id');
        } elseif ($this->location_type === 'App\\Models\\Zone') {
            $regionIds = \App\Models\Region::where('zone_id', $this->location_id)->pluck('id');
            $areaIds = \App\Models\Area::whereIn('region_id', $regionIds)->pluck('id');
            $hqIds = \App\Models\Headquarter::whereIn('area_id', $areaIds)->pluck('id');
        }

        if (!empty($hqIds)) {
            return \App\Models\Doctor::whereIn('headquarter_id', $hqIds)->get();
        }

        return collect();
    }

    public function getSubordinates(): \Illuminate\Support\Collection
    {
        // Admins and Super-Admins see all
        if ($this->hasAnyRole(['admin', 'super_admin'])) {
            return self::pluck('id');
        }

        // DSA: only themselves
        if ($this->hasRole('DSA')) {
            return collect([$this->id]);
        }

        // ASM: users in headquarters under their area and same division, plus themselves
        if ($this->hasRole('ASM')) {
            $hqIds = \App\Models\Headquarter::where('area_id', $this->location_id)->pluck('id');
            $userIds = $this->getUserIdsByLocation(self::TYPE_HEADQUARTER, $hqIds, $this->division_id);
            return $userIds->push($this->id);
        }

        // RSM: users in headquarters, areas, and regions under their region and same division, plus themselves
        if ($this->hasRole('RSM')) {
            $areaIds = \App\Models\Area::where('region_id', $this->location_id)->pluck('id');
            $hqIds = \App\Models\Headquarter::whereIn('area_id', $areaIds)->pluck('id');
            $dsaIds = $this->getUserIdsByLocation(self::TYPE_HEADQUARTER, $hqIds, $this->division_id);
            $asmIds = $this->getUserIdsByLocation(self::TYPE_AREA, $areaIds, $this->division_id);
            $rsmIds = $this->getUserIdsByLocation(self::TYPE_REGION, [$this->location_id], $this->division_id);
            return $dsaIds->merge($asmIds)->merge($rsmIds)->push($this->id);
        }

        // ZSM: users in headquarters, areas, regions under their zone and same division, plus themselves
        if ($this->hasRole('ZSM')) {
            $regionIds = \App\Models\Region::where('zone_id', $this->location_id)->pluck('id');
            $areaIds = \App\Models\Area::whereIn('region_id', $regionIds)->pluck('id');
            $hqIds = \App\Models\Headquarter::whereIn('area_id', $areaIds)->pluck('id');
            $dsaIds = $this->getUserIdsByLocation(self::TYPE_HEADQUARTER, $hqIds, $this->division_id);
            $asmIds = $this->getUserIdsByLocation(self::TYPE_AREA, $areaIds, $this->division_id);
            $rsmIds = $this->getUserIdsByLocation(self::TYPE_REGION, $regionIds, $this->division_id);
            $zsmIds = $this->getUserIdsByLocation(self::TYPE_ZONE, [$this->location_id], $this->division_id);
            return $dsaIds->merge($asmIds)->merge($rsmIds)->merge($zsmIds)->push($this->id);
        }

        // PMT/GM: all DSA, ASM, RSM, ZSM in their division
        if ($this->hasRole(['PMT', 'GM'])) {
            return self::where('division_id', $this->division_id)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['DSA', 'ASM', 'RSM', 'ZSM']);
                })
                ->pluck('id');
        }

        // Default: only themselves
        return collect([$this->id]);
    }
}
