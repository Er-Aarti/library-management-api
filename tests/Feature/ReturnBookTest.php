<?php

namespace Tests\Feature;

use App\Models\Books;
use App\Models\Borrowing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReturnBookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // use RefreshDatabase;
    use DatabaseTransactions;

    public function test_return_book(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
        Sanctum::actingAs($user);

        $book = Books::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'date' => now()->subDays(10)->toDateString(),
            'status' => 'borrowed',
        ]);
        $borrowing = Borrowing::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'borrowed_at' => now()->subDays(5),
            'returned_at' => null,
        ]);
        $response = $this->postJson("/api/books/{$book->id}/return");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Book returned successfully']);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'returned_at' => now(),
        ]);
    }
}
