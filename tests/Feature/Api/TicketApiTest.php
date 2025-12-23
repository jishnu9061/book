<?php

namespace Tests\Feature\Api;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Category;
use App\Models\Department;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_tickets()
    {
        $user = $this->createAuthenticatedUser();
        Ticket::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->authenticatedJson('GET', '/api/v1/tickets', $user);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'pagination',
                    'timestamp',
                    'version',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    public function test_authenticated_user_can_create_ticket()
    {
        $user = $this->createAuthenticatedUser();
        $priority = Priority::factory()->create();
        $status = Status::factory()->create();
        $category = Category::factory()->create();
        $department = Department::factory()->create();

        $response = $this->authenticatedJson('POST', '/api/v1/tickets', $user, [
            'subject' => 'Test Ticket',
            'details' => 'This is a test ticket',
            'priority_id' => $priority->id,
            'status_id' => $status->id,
            'category_id' => $category->id,
            'department_id' => $department->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'subject',
                    'details',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Test Ticket',
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_view_ticket()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->authenticatedJson('GET', "/api/v1/tickets/{$ticket->id}", $user);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'subject',
                    'details',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);
    }

    public function test_authenticated_user_can_update_ticket()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->authenticatedJson('PUT', "/api/v1/tickets/{$ticket->id}", $user, [
            'subject' => 'Updated Ticket Subject',
            'details' => 'Updated details',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'subject' => 'Updated Ticket Subject',
        ]);
    }

    public function test_authenticated_user_can_delete_ticket()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->authenticatedJson('DELETE', "/api/v1/tickets/{$ticket->id}", $user);

        $response->assertStatus(204);

        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    public function test_authenticated_user_can_add_comment_to_ticket()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->authenticatedJson('POST', "/api/v1/tickets/{$ticket->id}/comments", $user, [
            'comment' => 'This is a test comment',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'comment',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_get_ticket_comments()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);
        Comment::factory()->count(3)->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
        ]);

        $response = $this->authenticatedJson('GET', "/api/v1/tickets/{$ticket->id}/comments", $user);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'pagination',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    public function test_ticket_list_supports_pagination()
    {
        $user = $this->createAuthenticatedUser();
        Ticket::factory()->count(25)->create(['user_id' => $user->id]);

        $response = $this->authenticatedJson('GET', '/api/v1/tickets?per_page=10&page=1', $user);

        $response->assertStatus(200)
            ->assertJsonPath('meta.pagination.per_page', 10)
            ->assertJsonPath('meta.pagination.current_page', 1);
    }

    public function test_ticket_list_supports_filtering()
    {
        $user = $this->createAuthenticatedUser();
        $priority = Priority::factory()->create();
        Ticket::factory()->count(3)->create([
            'user_id' => $user->id,
            'priority_id' => $priority->id,
        ]);
        Ticket::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->authenticatedJson('GET', "/api/v1/tickets?filter[priority_id]={$priority->id}", $user);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}

