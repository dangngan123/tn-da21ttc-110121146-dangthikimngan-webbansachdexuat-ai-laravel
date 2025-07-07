<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'order_item_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Tăng sold_count khi OrderItem được tạo và đơn hàng có trạng thái ''
        static::created(function ($orderItem) {
            $order = $orderItem->order;
            if ($order && $order->status === 'ordered') {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->increment('sold_count', $orderItem->quantity);
                }
            }
        });

        // Giảm sold_count khi OrderItem bị xóa và đơn hàng có trạng thái ''
        static::deleted(function ($orderItem) {
            $order = $orderItem->order;
            if ($order && $order->status === 'ordered') {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->decrement('sold_count', $orderItem->quantity);
                }
            }
        });

        // Cập nhật sold_count khi số lượng OrderItem thay đổi
        static::updated(function ($orderItem) {
            if ($orderItem->isDirty('quantity')) {
                $order = $orderItem->order;
                if ($order && $order->status === '') {
                    $product = Product::find($orderItem->product_id);
                    if ($product) {
                        $originalQuantity = $orderItem->getOriginal('quantity');
                        $newQuantity = $orderItem->quantity;
                        $difference = $newQuantity - $originalQuantity;
                        if ($difference != 0) {
                            $product->increment('sold_count', $difference);
                        }
                    }
                }
            }
        });
    }
}
