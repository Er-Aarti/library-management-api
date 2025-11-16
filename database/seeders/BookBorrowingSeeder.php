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
        $users = User::all();
        $books = Books::where('status', 'available')->get();

        foreach ($users as $user) {
            $booksToBorrow = $books->random(rand(1, 7));
            foreach ($booksToBorrow as $book) {
                Borrowing::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'borrowed_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    'returned_at' => null,
                ]);
                $book->status = 'borrowed';
                $book->save();
            }
        }
    }
}
