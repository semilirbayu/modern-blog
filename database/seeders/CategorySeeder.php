<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Latest trends in technology, programming, and software development.',
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Tips and insights about modern lifestyle, wellness, and personal development.',
            ],
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'description' => 'Travel guides, destinations, and adventures from around the world.',
            ],
            [
                'name' => 'Food & Cooking',
                'slug' => 'food-cooking',
                'description' => 'Delicious recipes, cooking tips, and culinary adventures.',
            ],
            [
                'name' => 'Health & Fitness',
                'slug' => 'health-fitness',
                'description' => 'Health tips, fitness routines, and wellness advice.',
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business insights, entrepreneurship, and professional development.',
            ],
            [
                'name' => 'Arts & Culture',
                'slug' => 'arts-culture',
                'description' => 'Art, music, literature, and cultural commentary.',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}