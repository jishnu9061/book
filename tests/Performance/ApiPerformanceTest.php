<?php

namespace Tests\Performance;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that ticket listing endpoint performs well with large datasets
     */
    public function test_ticket_list_performance()
    {
        $user = $this->createAuthenticatedUser();
        
        // Create a large dataset
        Ticket::factory()->count(1000)->create(['user_id' => $user->id]);

        $startTime = microtime(true);

        $response = $this->authenticatedJson('GET', '/api/v1/tickets?per_page=50', $user);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);
        
        // Assert that the request completes in under 500ms
        $this->assertLessThan(500, $executionTime, "Ticket list took {$executionTime}ms, expected < 500ms");
    }

    /**
     * Test that pagination works efficiently
     */
    public function test_pagination_performance()
    {
        $user = $this->createAuthenticatedUser();
        Ticket::factory()->count(500)->create(['user_id' => $user->id]);

        $startTime = microtime(true);

        $response = $this->authenticatedJson('GET', '/api/v1/tickets?page=10&per_page=15', $user);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(300, $executionTime, "Pagination took {$executionTime}ms, expected < 300ms");
    }

    /**
     * Test concurrent requests handling
     */
    public function test_concurrent_requests()
    {
        $user = $this->createAuthenticatedUser();
        Ticket::factory()->count(100)->create(['user_id' => $user->id]);

        $startTime = microtime(true);

        // Simulate concurrent requests
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->authenticatedJson('GET', '/api/v1/tickets', $user);
        }

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        // All 10 requests should complete in under 2 seconds
        $this->assertLessThan(2000, $executionTime, "Concurrent requests took {$executionTime}ms");
    }
}

