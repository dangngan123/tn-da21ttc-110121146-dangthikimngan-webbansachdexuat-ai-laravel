<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subtotal',
        'discount',
        'shipping_cost',
        'total',
        'name',
        'email',
        'phone',
        'province',
        'district',
        'ward',
        'address',
        'additional_info',
        'status',
        'order_code',
        'coupon_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getProductsAttribute()
    {
        return $this->orderItems->map(function ($item) {
            return $item->product;
        })->filter();
    }

    protected static function boot()
    {
        parent::boot();

        // Tạo mã đơn hàng tự động khi tạo mới
        static::creating(function ($order) {
            if (!$order->order_code) {
                $order->order_code = static::generateOrderCode();
            }
        });

        // Cập nhật sold_count khi trạng thái đơn hàng thay đổi
        static::updated(function ($order) {
            if ($order->isDirty('status')) {
                $originalStatus = $order->getOriginal('status');
                $newStatus = $order->status;

                foreach ($order->orderItems as $orderItem) {
                    $product = Product::find($orderItem->product_id);
                    if ($product) {
                        if ($originalStatus !== 'ordered' && $newStatus === 'ordered') {
                            // Nếu trạng thái đổi thành ordered', tăng sold_count
                            $product->increment('sold_count', $orderItem->quantity);
                        } elseif ($originalStatus === 'ordered' && $newStatus !== 'canceled') {
                            // Nếu trạng thái đổi từ ordered' sang trạng thái khác, giảm sold_count
                            $product->decrement('sold_count', $orderItem->quantity);
                        }
                    }
                }
            }
        });
    }

    public static function generateOrderCode()
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        return "ORD-{$date}-{$random}";
    }
}
