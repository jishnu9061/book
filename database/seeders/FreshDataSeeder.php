<?php

namespace Database\Seeders;

use App\Models\TicketEntry;
use App\Models\TicketField;
use Illuminate\Database\Seeder;

class FreshDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->call([
            LanguageSeeder::class,
            CategorySeeder::class,
            PrioritySeeder::class,
            StatusSeeder::class,
            DepartmentSeeder::class,
            TypeSeeder::class,
            MediaFolderSeeder::class,


            UserSeeder::class,
            OrganizationSeeder::class,
            ContactSeeder::class,
            KnowledgeBaseSeeder::class,
            FaqSeeder::class,

            BlogSeeder::class,
            ConversationSeeder::class,
            MessageSeeder::class,
            NoteSeeder::class,
            NotificationSeeder::class,

            ComprehensiveTicketSeeder::class,
            TicketCommentsSeeder::class,
            AITicketClassificationSeeder::class,
        ]);
    }
}
