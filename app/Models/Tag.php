<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TagVisibilityScope;

class Tag extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new TagVisibilityScope);
    }
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class);
    }

    public function chemists()
    {
        return $this->belongsToMany(Chemist::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }
    public function divisions()
    {
        return $this->belongsToMany(Division::class, 'division_tag', 'tag_id', 'division_id');
    }
}
