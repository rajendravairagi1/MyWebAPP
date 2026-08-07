<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'employee_id', 'month', 'year', 'present_days', 'per_day_salary',
    'gross_salary', 'advance_deduction', 'other_deduction', 'net_salary',
    'status', 'paid_at',
])]
class SalarySlip extends Model
{
    protected function casts(): array
    {
        return [
            'paid_at' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function monthLabel(): string
    {
        return \Carbon\Carbon::create($this->year, $this->month, 1)->format('F Y');
    }
}
