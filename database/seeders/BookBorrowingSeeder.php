<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Borrowing;
use App\Models\Books;
use App\Models\User;
use Faker\Factory as Faker;

class BookBorrowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $borrowedBooks = Books::where('status', 'borrowed')->get();
        $userIds = User::pluck('id')->toArray();

        foreach ($borrowedBooks as $book) {
            Borrowing::create([
                'book_id' => $book->id,
                'user_id' => $faker->randomElement($userIds),
                'borrowed_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'returned_at' => $faker->dateTimeBetween('now', '+1 month'),
            ]);
        }
    }
}
