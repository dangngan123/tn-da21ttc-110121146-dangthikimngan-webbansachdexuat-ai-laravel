<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatInteraction extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'support_option',
        'session_key',
        'question',
        'answer',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:H:i d/m/Y',
        'updated_at' => 'datetime:H:i d/m/Y',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
