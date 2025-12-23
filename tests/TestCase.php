<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        $this->artisan('migrate', ['--database' => 'sqlite'])->run();
    }

    /**
     * Create an authenticated user for testing
     */
    protected function createAuthenticatedUser($role = 'customer')
    {
        $user = \App\Models\User::factory()->create();
        
        if ($role !== 'customer') {
            $roleModel = \App\Models\Role::where('slug', $role)->first();
            if ($roleModel) {
                $user->update(['role_id' => $roleModel->id]);
                $user->load('role');
            }
        }
        
        return $user;
    }

    /**
     * Get authentication token for a user
     */
    protected function getAuthToken($user)
    {
        return $user->createToken('test-token')->plainTextToken;
    }

    /**
     * Make an authenticated API request
     */
    protected function authenticatedJson($method, $uri, $user = null, array $data = [], array $headers = [])
    {
        if (!$user) {
            $user = $this->createAuthenticatedUser();
        }
        
        $token = $this->getAuthToken($user);
        
        return $this->json($method, $uri, $data, array_merge([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ], $headers));
    }
}
