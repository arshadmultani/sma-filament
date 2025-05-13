<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chemist extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function customerType()
    {
        return $this->morphOne(CustomerType::class, 'typeable');
    }

    
}
