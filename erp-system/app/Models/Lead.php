<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'company_name', 'mobile', 'email', 'source',
    'status', 'assigned_to', 'notes',
])]
class Lead extends Model
{
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(LeadMeeting::class)->latest('meeting_date');
    }
}
