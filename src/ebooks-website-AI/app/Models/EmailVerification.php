<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = ['email', 'token', 'otp', 'password', 'name', 'phone', 'expires_at'];
}
