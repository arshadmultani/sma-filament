<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

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
class Doctor extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'qualification_id', 'profile_photo', 'user_id', 'headquarter_id', 'attachment', 'address', 'type', 'support_type', 'town', 'specialty_id'];

    
    protected $casts = [
        'attachment' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function headquarter()
    {
        return $this->belongsTo(Headquarter::class);
    }

    public function kofolEntries()
    {
        return $this->morphMany(KofolEntry::class, 'customer');
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function microsites()
    {
        return $this->hasOne(Microsite::class);
    }
}
