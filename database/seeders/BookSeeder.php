<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Books;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 1; $i <= 20; $i++) {
            Books::create([
                'title' => $faker->sentence(3),
                'author' => $faker->name,
                'date' => $faker->optional()->date('Y-m-d'),
                'status' => $faker->randomElement(['available', 'borrowed']),
            ]);
        }
    }
}
