<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    //

    use HasFactory;
    protected $fillable = [
        'user_id',
        'order_item_id',
        'rating',
        'comment',
        'images',
        'status',
        'admin_reply',
        'admin_reply_at'
    ];

    protected $casts = [
        'admin_reply_at' => 'datetime',
    ];


    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   // Thêm mối quan hệ gián tiếp với Product (nếu cần)
    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            OrderItem::class,
            'id', // Khóa chính trên bảng order_items
            'id', // Khóa chính trên bảng products
            'order_item_id', // Khóa ngoại trên bảng reviews
            'product_id' // Khóa ngoại trên bảng order_items
        );
    }
    
}
