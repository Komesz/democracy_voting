<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'choices', 'results', 'active'];

    protected $casts = [
        'choices' => 'array',
        'results' => 'array',
    ];
}
