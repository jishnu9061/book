<?php

namespace Tests\Feature\Api;

use App\Models\Conversation;
use App\Models\Ticket;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_conversations()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create();
        Conversation::factory()->count(3)->create(['ticket_id' => $ticket->id]);

        $response = $this->authenticatedJson('GET', '/api/v1/conversations', $user);

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

    public function test_authenticated_user_can_create_conversation()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create();
        $participant = User::factory()->create();

        $response = $this->authenticatedJson('POST', '/api/v1/conversations', $user, [
            'ticket_id' => $ticket->id,
            'conversation_type' => 'internal',
            'participants' => [
                ['user_id' => $participant->id, 'role' => 'agent'],
            ],
            'initial_message' => 'Hello, this is a test conversation',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'conversation',
                    'initial_message',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);
    }

    public function test_authenticated_user_can_send_message()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create();
        $conversation = Conversation::factory()->create(['ticket_id' => $ticket->id]);

        $response = $this->authenticatedJson('POST', "/api/v1/conversations/{$conversation->id}/messages", $user, [
            'message' => 'This is a test message',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'message',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_get_messages()
    {
        $user = $this->createAuthenticatedUser();
        $ticket = Ticket::factory()->create();
        $conversation = Conversation::factory()->create(['ticket_id' => $ticket->id]);
        Message::factory()->count(5)->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
        ]);

        $response = $this->authenticatedJson('GET', "/api/v1/conversations/{$conversation->id}/messages", $user);

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
}

