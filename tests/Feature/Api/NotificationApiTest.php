<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_notifications()
    {
        $user = $this->createAuthenticatedUser();
        
        // Create some notifications
        $user->notify(new \App\Notifications\TicketCreated([
            'ticket_id' => 1,
            'message' => 'Test notification',
        ]));

        $response = $this->authenticatedJson('GET', '/api/v1/notifications', $user);

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

    public function test_authenticated_user_can_mark_notification_as_read()
    {
        $user = $this->createAuthenticatedUser();
        $notification = $user->notify(new \App\Notifications\TicketCreated([
            'ticket_id' => 1,
            'message' => 'Test notification',
        ]));

        $notificationId = $user->notifications()->first()->id;

        $response = $this->authenticatedJson('POST', "/api/v1/notifications/{$notificationId}/read", $user);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertNotNull($user->notifications()->first()->read_at);
    }

    public function test_authenticated_user_can_mark_all_notifications_as_read()
    {
        $user = $this->createAuthenticatedUser();
        $user->notify(new \App\Notifications\TicketCreated([
            'ticket_id' => 1,
            'message' => 'Test notification 1',
        ]));
        $user->notify(new \App\Notifications\TicketCreated([
            'ticket_id' => 2,
            'message' => 'Test notification 2',
        ]));

        $response = $this->authenticatedJson('POST', '/api/v1/notifications/read-all', $user);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals(0, $user->unreadNotifications->count());
    }
}

