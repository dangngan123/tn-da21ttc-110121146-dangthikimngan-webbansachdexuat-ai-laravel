<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::create([
            'coupon_code' => 'OFF10',
            'coupon_type' => 'fixed',
            'coupon_value' => 10,
            'cart_value' => 100,
            'end_date' => '2024-12-31'

        ]);
        Coupon::create([
            'coupon_code' => 'OFF20',
            'coupon_type' => 'percent',
            'coupon_value'=> 20,
            'cart_value' => 100,
            'end_date' => '2024-12-31'

        ]);
            
    }
}
