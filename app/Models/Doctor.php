<?php

namespace App\Models;

use App\Observers\DoctorObserver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property int|null $qualification_id
 * @property string|null $profile_photo
 * @property int|null $user_id
 * @property int $headquarter_id
 * @property array<array-key, mixed>|null $attachment
 * @property string|null $town
 * @property string|null $type
 * @property string|null $support_type
 * @property string|null $address
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Headquarter $headquarter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KofolEntry> $kofolEntries
 * @property-read int|null $kofol_entries_count
 * @property-read \App\Models\Qualification|null $qualification
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereHeadquarterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereSupportType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereTown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereUserId($value)
 *
 * @mixin \Eloquent
 */

#[ObservedBy(DoctorObserver::class)]
class Doctor extends BaseModel
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'qualification_id', 'profile_photo', 'user_id', 'headquarter_id', 'attachment', 'address', 'type', 'support_type', 'town', 'specialty_id'];

    protected $casts = [
        'attachment' => 'array',
        'practice_since' => 'date',
    ];

    protected $appends = ['profile_photo_url'];

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? Storage::temporaryUrl($this->profile_photo, now()->addMinutes(5))
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function loginAccount(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function headquarter()
    {
        return $this->belongsTo(Headquarter::class);
    }

    public function kofolEntries()
    {
        return $this->morphMany(KofolEntry::class, 'customer');
    }

    public function microsite()
    {
        return $this->hasOne(Microsite::class);
    }

    public function merits()
    {
        return $this->hasMany(Merit::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function showcases()
    {
        return $this->hasMany(Showcase::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function getHeadquarterNameAttribute()
    {
        return $this->headquarter?->name;
    }

    public function campaignEntries()
    {
        return $this->morphMany(CampaignEntry::class, 'customer');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'doctor_tag')->withTimestamps()->withPivot('user_id');
    }

    public function getRelationsToCheckForDelete()
    {
        return ['microsites', 'kofolEntries'];
    }

    public function panelAccessRequest(): HasOne
    {
        return $this->hasOne(PanelAccessRequest::class)->latestOfMany();
    }

    public function hasPanelAccessRequest(): bool
    {
        return $this->panelAccessRequest()->exists();
    }

    public function panelRequestApproved(): bool
    {
        return $this->panelAccessRequest && $this->panelAccessRequest->state_id === State::where('category', 'Finalized')->value('id');
    }

    public function hasLoginAccount(): bool
    {
        return $this->loginAccount()->exists();
    }

    public function userAccount(): User|null
    {
        return User::where('userable_type', 'doctor')
            ->where('userable_id', $this->id)
            ->first();
    }
    public function getHasProfilePhotoAttribute(): bool
    {
        return filled($this->profile_photo);
    }

    public function getHasMicrositeAttribute(): bool
    {
        return $this->microsite()->exists();
    }
}
