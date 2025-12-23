<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TicketSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder runs all ticket-related seeders in the correct order.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Starting Ticket System Seeding...');
        
        // Run seeders in order
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PrioritySeeder::class,
            StatusSeeder::class,
            DepartmentSeeder::class,
            TypeSeeder::class,
            CategorySeeder::class,
        ]);

        $this->command->info('✅ Basic data seeded successfully');

        // Run ticket-related seeders
        $this->call([
            ComprehensiveTicketSeeder::class,
            TicketCommentsSeeder::class,
            AITicketClassificationSeeder::class,
        ]);

        $this->command->info('🎉 Ticket System seeding completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Users: ' . \App\Models\User::count());
        $this->command->info('   - Tickets: ' . \App\Models\Ticket::count());
        $this->command->info('   - Comments: ' . \App\Models\Comment::count());
        $this->command->info('   - AI Classifications: ' . \App\Models\AITicketClassification::count());
        $this->command->info('   - Activities: ' . \App\Models\TicketActivity::count());
    }
}
