<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        DB::table('email_templates')->truncate();
        $logo = URL::to('/images/logo.png');
        $html = File::get(public_path('html/email_templates/create_ticket_new_customer.html'));
        EmailTemplate::create([
            'name' => 'Create ticket by new customer',
            'slug' => 'create_ticket_new_customer',
            'details' => 'When customer create a new ticket from the landing page',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        $html = File::get(public_path('html/email_templates/create_ticket_from_dashboard.html'));
        EmailTemplate::create([
            'name' => 'Create ticket from dashboard',
            'slug' => 'create_ticket_dashboard',
            'details' => 'When a ticket created from the admin page',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        $html = File::get(public_path('html/email_templates/custom_mail.html'));
        EmailTemplate::create([
            'name' => 'Custom Mail',
            'slug' => 'custom_mail',
            'details' => 'It will use to send any custom email.',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        $html = File::get(public_path('html/email_templates/ticket_assigned_user.html'));
        EmailTemplate::create([
            'name' => 'Got assigned for a ticket',
            'slug' => 'assigned_ticket',
            'details' => 'When a user got assigned for a ticket.',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        $html = File::get(public_path('html/email_templates/ticket_updated.html'));
        EmailTemplate::create([
            'name' => 'The ticket has been updated',
            'slug' => 'ticket_updated',
            'details' => 'When a ticket has been updated.',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        $html = File::get(public_path('html/email_templates/ticket_new_comment.html'));
        EmailTemplate::create([
            'name' => 'A new comment has been added on the ticket',
            'slug' => 'ticket_new_comment',
            'details' => 'When a comment has been added on a ticket.',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        $html = File::get(public_path('html/email_templates/user_created.html'));
        EmailTemplate::create([
            'name' => 'User created',
            'slug' => 'user_created',
            'details' => 'When a new user created.',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        // Conversation notification templates
        $html = File::get(public_path('html/email_templates/conversation_created.html'));
        EmailTemplate::create([
            'name' => 'Conversation Created',
            'slug' => 'conversation_created',
            'details' => 'When a new conversation is created and user is added as participant.',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        $html = File::get(public_path('html/email_templates/conversation_new_message.html'));
        EmailTemplate::create([
            'name' => 'New Message in Conversation',
            'slug' => 'conversation_new_message',
            'details' => 'When a new message is sent in a conversation.',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);

        $html = File::get(public_path('html/email_templates/conversation_participant_added.html'));
        EmailTemplate::create([
            'name' => 'Added to Conversation',
            'slug' => 'conversation_participant_added',
            'details' => 'When a user is added to an existing conversation.',
            'language' => 'en',
            'html' => str_replace('https://res.cloudinary.com/robinbd/image/upload/v1663394454/mail-template/help-desk-logo.png', $logo, $html)
        ]);
        //
    }
}
