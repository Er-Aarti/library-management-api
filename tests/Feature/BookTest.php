<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;


class BookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // use RefreshDatabase;

    public function test_create_book_feature(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        Sanctum::actingAs($user);

        $this->seed(\Database\Seeders\BookSeeder::class);

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'author', 'date', 'status']
                ]
            ]);
    }
}
