<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'employee_code', 'name', 'mobile', 'aadhaar_number', 'pan_number',
    'joining_date', 'department', 'designation', 'per_day_salary',
    'bank_account_number', 'bank_ifsc', 'status',
])]
class Employee extends Model
{
    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
        ];
    }
}
