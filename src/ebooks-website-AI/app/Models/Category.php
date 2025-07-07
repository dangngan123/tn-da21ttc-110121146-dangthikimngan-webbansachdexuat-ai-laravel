<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories'; // Tên bảng

    protected $fillable = ['name', 'slug']; // Các cột có thể gán
    public function products()
    {
        return $this->hasMany(Product::class, 'id', 'category_id');
    }
    

    public function getImage()
    {


        $isUrl = Str::isUrl($this->image);
        return $isUrl ? $this->image : asset('/admin/category/' . $this->image);
    }
}
