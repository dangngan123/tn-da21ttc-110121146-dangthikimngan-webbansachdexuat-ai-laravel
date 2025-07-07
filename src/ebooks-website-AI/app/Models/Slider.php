<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'top_title',
        'slug',
        'title',
        'sub_title',
        'link',
        'offer',
        'image',
        'start_date',
        'end_date',
        'status',
        'type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
    ];

    public function setTopTitleAttribute($value)
    {
        $this->attributes['top_title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getImage()
    {
        $isUrl = Str::isUrl($this->image);
        return $isUrl ? $this->image : asset('admin/slider/' . $this->image);
    }
}
