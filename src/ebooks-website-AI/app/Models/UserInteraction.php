<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInteraction extends Model
{
    protected $table = 'user_interaction';
    protected $primaryKey = 'interaction_id';
    public $timestamps = false; // Tắt timestamps vì chỉ có created_at

    protected $fillable = [
        'user_id',
        'product_id',
        'interaction_type',
        'interaction_value',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}