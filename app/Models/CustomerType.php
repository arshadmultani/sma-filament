<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $fillable = ['type'];

    public function typeable()
    {
        return $this->morphTo();
    }
}
