<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Division;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        for ($i = 0; $i < 10; $i++) {
            Product::create([
                'name' => fake()->word(),
                'description' => fake()->sentence(),
                'price' => fake()->randomFloat(2, 10, 1000),
                'division_id' => fake()->randomElement(Division::whereIn('name', ['Pharma', 'Phytonova'])->pluck('id')->toArray()),
                'image' => 'https://picsum.photos/200/300?random=' . $i,
            ]);
        }
    }
}
