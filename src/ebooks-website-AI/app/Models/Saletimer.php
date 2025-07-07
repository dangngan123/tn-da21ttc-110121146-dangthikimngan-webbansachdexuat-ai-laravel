<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saletimer extends Model
{
    protected $table = 'saletimers';

    protected $fillable = [
        'start_date',
        'sale_timer',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'sale_timer' => 'datetime',
        'status' => 'boolean',
    ];
}