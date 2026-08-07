<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lead_id', 'meeting_date', 'notes', 'next_follow_up_date'])]
class LeadMeeting extends Model
{
    protected function casts(): array
    {
        return [
            'meeting_date' => 'datetime',
            'next_follow_up_date' => 'date',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
