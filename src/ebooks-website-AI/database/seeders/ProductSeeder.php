<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();
        $images = [
            'https://cdn0.fahasa.com/media/catalog/product/2/0/2030-nhung-xu-huong-lon-se-dinh-hinh-the-gioi-tuong-lai-tb-2023.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/b/u/bup-sen-xanh_bia_phien-ban-ky-niem-2020.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/8/9/8935270703554.jpg',


            'https://cdn0.fahasa.com/media/catalog/product/0/0/00_2.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/9/7/9781108430425.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/i/m/image_195509_1_19743.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/i/m/image_240282.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/9/7/9786044009674.jpg',
            'https://cdn0.fahasa.com/media/catalog/product/9/7/9786043440287.jpg',
        ];

        foreach (range(1, 50) as $key => $value) {
            $name = $faker->unique()->name;
            Product::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'short_description' => $faker->text(100),
                'long_description' => $faker->text(300),
                'reguler_price' => $faker->numberBetween(80, 500),
                'sale_price' => $faker->numberBetween(50, 300),
                'image' => $images[rand(0, 8)],
                'images' => 'https://via.placeholder.com/150',
                'category_id' => $faker->numberBetween(1, 10),
                'publisher' => $faker->company, // Added publisher
                'author' => $faker->name,       // Added author
                'age' => $faker->numberBetween(1, 18) . '+' // Added age rating
            ]);
        }
    }
}
