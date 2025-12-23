<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'token',
                    'token_type',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_user_can_register()
    {
        $customerRole = Role::factory()->create(['slug' => 'customer']);

        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'token',
                    'token_type',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_authenticated_user_can_get_their_profile()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->authenticatedJson('GET', '/api/v1/auth/me', $user);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'email',
                    'first_name',
                    'last_name',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->authenticatedJson('POST', '/api/v1/auth/logout', $user);

        $response->assertStatus(204);
    }

    public function test_unauthenticated_user_cannot_access_protected_endpoints()
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
}

