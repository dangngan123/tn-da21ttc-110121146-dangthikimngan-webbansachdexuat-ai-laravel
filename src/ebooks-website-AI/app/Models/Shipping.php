<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipping extends Model
{
    use HasFactory;

    protected $table = 'shippings'; // Tên bảng hiện tại

    protected $fillable = [
        'user_id',
        'address_type',
        'name',
        'phone',
        'province',
        'district',
        'ward',
        'address',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
