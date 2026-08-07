<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['employee_id', 'date', 'status', 'remarks'])]
class Attendance extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function dayValue(): float
    {
        return match ($this->status) {
            'present' => 1.0,
            'half_day' => 0.5,
            default => 0.0,
        };
    }
}
