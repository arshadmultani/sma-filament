<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KofolEntry> $kofolEntries
 * @property-read int|null $kofol_entries_count
 *
 * @method static \Database\Factories\KofolCampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KofolCampaign whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class KofolCampaign extends Model
{
    /** @use HasFactory<\Database\Factories\KofolCampaignFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function kofolEntries()
    {
        return $this->hasMany(KofolEntry::class);
    }
}
