<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $type
 * @property string $typeable_type
 * @property int $typeable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $typeable
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType whereTypeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType whereTypeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CustomerType extends Model
{
    protected $fillable = ['type'];

    public function typeable()
    {
        return $this->morphTo();
    }
}
