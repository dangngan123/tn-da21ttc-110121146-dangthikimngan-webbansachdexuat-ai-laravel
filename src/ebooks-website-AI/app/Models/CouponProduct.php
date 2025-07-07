<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponProduct extends Model
{
    protected $fillable = ['coupon_id', 'product_id', 'category_id'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
