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

class ReturnBookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // use RefreshDatabase;
    public function test_return_book(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $this->seed(\Database\Seeders\BookSeeder::class);
        $this->seed(\Database\Seeders\BookBorrowingSeeder::class);

        DB::table('borrowings')->update(['user_id' => $user->id]);
        $bookId = DB::table('borrowings')->first()->book_id;

        $response = $this->postJson("/api/books/{$bookId}/return");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Book returned successfully']);

        $this->assertDatabaseHas('books', [
            'id' => $bookId,
            'status' => 'available'
        ]);
    }
}
