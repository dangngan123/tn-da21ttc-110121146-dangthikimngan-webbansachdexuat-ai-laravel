<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'coupon_code', 'coupon_type', 'coupon_value', 'cart_value',
        'start_date', 'end_date', 'max_uses', 'used', 'is_active',
        'description', 'user_id'
    ];

    public function couponProducts()
    {
        return $this->hasMany(CouponProduct::class, 'coupon_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    /**
     * Belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    }
}