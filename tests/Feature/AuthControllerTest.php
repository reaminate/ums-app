<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_a_user_can_log_in_with_valid_credentials(): void
    {
        $user = $this->admin(['email' => 'admin@example.com', 'password' => 'password123']);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()->assertJsonStructure(['message', 'user', 'access_token', 'token_type']);
    }

    public function test_login_fails_with_an_incorrect_password(): void
    {
        $this->admin(['email' => 'admin@example.com', 'password' => 'password123']);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
    }

    public function test_login_requires_an_existing_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'missing@example.com',
            'password' => 'password123',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = $this->studentUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/logout');

        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
