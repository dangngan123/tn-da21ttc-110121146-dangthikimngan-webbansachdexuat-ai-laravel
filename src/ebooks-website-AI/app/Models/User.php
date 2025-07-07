<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'google_id',
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'utype',
        'status',
        'rank',
        'avatar',
        'gender', // Thêm gender
        'date_of_birth', // Thêm date_of_birth
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date', // Định dạng date_of_birth thành kiểu date
        ];
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id'); // Giả sử cột khóa ngoại là 'user_id'
    }
}
