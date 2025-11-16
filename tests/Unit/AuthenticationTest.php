<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    // use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_auth_user(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'token']);
    }
}
