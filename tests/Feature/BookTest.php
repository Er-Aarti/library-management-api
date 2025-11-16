<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;


class BookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // use RefreshDatabase;
    use DatabaseTransactions;

    public function test_create_book_feature(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/books', [
            'title' => 'New Book',
            'author' => 'Author Name',
            'date' => '2023-11-01',
            'status' => 'available',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'author',
                'date',
                'status'
            ]);
    }
}
