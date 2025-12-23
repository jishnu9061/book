<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        User::factory()->createMany([
            ['email' => 'john.due.helo@mail.com', 'password' => 'w3bd.com', 'role_id' => 1, 'first_name' => 'John', 'last_name' => 'Doe'],
            ['email' => 'robert.slaughter@mail.com', 'password' => 'w3bd.com', 'role_id' => 4, 'first_name' => 'Robert', 'last_name' => 'Slaughter'],
            ['email' => 'john.ali@mail.com', 'password' => 'w3bd.com', 'role_id' => 6, 'first_name' => 'John', 'last_name' => 'Ali'],
            ['email' => 'mmarks@example.com', 'password' => 'w3bd.com', 'role_id' => 2, 'first_name' => 'Mike', 'last_name' => 'Marks'],
            ['email' => 'pat@example.com', 'password' => 'w3bd.com', 'role_id' => 3, 'first_name' => 'Patricia', 'last_name' => 'Johnson'],
            ['email' => 'taylor@example.com', 'password' => 'w3bd.com', 'role_id' => 5, 'first_name' => 'Taylor', 'last_name' => 'Swift']
        ]);
        User::factory(100)->create();
    }
}
