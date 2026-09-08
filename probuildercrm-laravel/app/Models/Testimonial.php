<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = ['quote', 'author_role', 'author_city', 'rating', 'sort_order'];

    protected $casts = [
        'rating' => 'integer',
        'sort_order' => 'integer',
    ];
}
