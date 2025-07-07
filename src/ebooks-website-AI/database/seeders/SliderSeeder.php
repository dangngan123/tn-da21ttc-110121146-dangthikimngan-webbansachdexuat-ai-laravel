<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker;
use Carbon\Carbon;


class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();

        $images = [
            'https://cdn0.fahasa.com/media/catalog/product/9/7/9780194798488-dd.jpg"',
            'https://cdn0.fahasa.com/media/catalog/product/8/9/8935244868999.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/i/m/image_195509_1_28788.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/d/a/dat-rung-phuong-nam.jpg',
        ];

        foreach (range(1, 5) as $key => $value) {
            $name = $faker->unique()->name;
            Slider::create([
                'top_title' => $name,
                'slug' => Str::slug($name,),
                'title' => $faker->text(20),
                'sub_title' => $faker->text(10),
                'link' => 'link.com',
                'offer' => $faker->numberBetween(50, 300),
                'image' => $images[rand(0, 3)],
                'start_date' => Carbon::create(2024, 12, 5),
                'end_date' => Carbon::create(2024, 01, 10),
                'type' => 'slider',
            ]);
        }
    }
}
