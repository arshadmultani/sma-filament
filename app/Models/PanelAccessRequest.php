<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanelAccessRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PanelAccessRequestFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
