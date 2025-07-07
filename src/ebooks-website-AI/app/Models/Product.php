<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'long_description',
        'publisher',
        'published_year',
        'author',
        'age',
        'reguler_price',
        'sale_price',
        'quantity',
        'sold_count', // Đảm bảo sold_count có trong fillable
        'image',
        'images',
        'category_id',
        'status',
        'discount_type',
        'discount_value',
    ];

    public function getImage()
    {
        if (!$this->image) {
            return asset('images/default-product.jpg');
        }

        $isUrl = Str::isUrl($this->image);
        return $isUrl ? $this->image : asset('admin/product/' . $this->image);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')
            ->withPivot('quantity', 'price', 'subtotal');
    }

    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            OrderItem::class,
            'product_id',
            'order_item_id',
            'id',
            'id'
        );
    }
}
