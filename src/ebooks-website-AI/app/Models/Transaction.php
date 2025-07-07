<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = ['user_id', 'order_id', 'payment_type', 'status', 'amount', 'transaction_id'];
    protected $casts = [
        'payment_type' => 'string',
        'status' => 'string',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}