<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;

class KofolEntryCoupon extends Model
{
    protected $fillable = [
        'kofol_entry_id',
        'coupon_code',
    ];

    public function kofolEntry()
    {
        return $this->belongsTo(KofolEntry::class,'kofol_entry_id');
    }

    protected static function booted()
    {
        static::addGlobalScope(new class implements Scope {
            public function apply(Builder $builder, Model $model): void
            {
                /** @var \App\Models\User|null $user */
                $user = \Illuminate\Support\Facades\Auth::user();

                if (! $user) {
                    return;
                }

                // Use the new getSubordinates() method for all role logic
                $userIds = $user->getSubordinates();

                // If the user can see all (admins), don't apply any restriction
                if ($userIds->count() === \App\Models\User::count()) {
                    return;
                }

                // Filter through the kofolEntry relationship
                $builder->whereHas('kofolEntry', function (Builder $query) use ($userIds) {
                    $query->whereIn('user_id', $userIds);
                });
            }
        });
    }
} 