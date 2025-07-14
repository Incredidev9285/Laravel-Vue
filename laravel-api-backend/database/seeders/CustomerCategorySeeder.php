<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerCategory;

class CustomerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Gold'],
            ['name' => 'Silver'],
            ['name' => 'Bronze'],
        ];

        foreach ($categories as $category) {
            CustomerCategory::create($category);
        }
    }
}
