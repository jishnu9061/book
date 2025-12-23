<?php

namespace Tests\Feature\Api;

use App\Models\Contact;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_contacts()
    {
        $user = $this->createAuthenticatedUser();
        Contact::factory()->count(5)->create();

        $response = $this->authenticatedJson('GET', '/api/v1/contacts', $user);

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

    public function test_authenticated_user_can_create_contact()
    {
        $user = $this->createAuthenticatedUser();
        $organization = Organization::factory()->create();

        $response = $this->authenticatedJson('POST', '/api/v1/contacts', $user, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'organization_id' => $organization->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                ],
                'meta',
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('contacts', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_authenticated_user_can_view_contact()
    {
        $user = $this->createAuthenticatedUser();
        $contact = Contact::factory()->create();

        $response = $this->authenticatedJson('GET', "/api/v1/contacts/{$contact->id}", $user);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', $contact->id);
    }

    public function test_authenticated_user_can_update_contact()
    {
        $user = $this->createAuthenticatedUser();
        $contact = Contact::factory()->create();

        $response = $this->authenticatedJson('PUT', "/api/v1/contacts/{$contact->id}", $user, [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
    }

    public function test_authenticated_user_can_delete_contact()
    {
        $user = $this->createAuthenticatedUser();
        $contact = Contact::factory()->create();

        $response = $this->authenticatedJson('DELETE', "/api/v1/contacts/{$contact->id}", $user);

        $response->assertStatus(204);

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }
}

