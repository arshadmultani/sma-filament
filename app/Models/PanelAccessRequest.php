<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;


#[ScopedBy(TeamHierarchyScope::class),]

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
        return $this->belongsTo(User::class, 'user_id');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function emailSender()
    {
        return $this->belongsTo(User::class, 'email_sent_by');
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
