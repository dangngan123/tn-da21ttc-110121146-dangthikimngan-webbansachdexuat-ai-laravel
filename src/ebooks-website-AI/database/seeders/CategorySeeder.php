<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            'https://cdn0.fahasa.com/media/wysiwyg/Thang-11-2024/catehomepage_vanhoc.jpg',
            'https://cdn0.fahasa.com/media/wysiwyg/Thang-11-2024/catehomepage_kinhte.jpg',
            'https://cdn0.fahasa.com/media/wysiwyg/Thang-11-2024/catehomepage_kynang.jpg',
         
            'https://cdn0.fahasa.com/media/wysiwyg/Thang-11-2024/catehomepage_ngoaingu.jpg',
        ];
        $categories = [
            'VĂN HỌC',
            'KINH TẾ',
            'TÂM LÝ - KỸ NĂNG SỐNG',
            'NUÔI DẠY CON',
            'SÁCH THIẾU NHI',
            'TIỂU SỬ - HỒI KÝ',
            'GIÁO KHOA - THAM KHẢO',
            'SÁCH HỌC NGOẠI NGỮ',

        ];
        foreach ($categories as $key => $value) {
            Category::create([
                'name' => $value,
                'slug' => Str::slug($value,),
                'image' => $images[rand(0, 3)],
                'status' => rand(0, 1),
            ]);
        }
    }
}
