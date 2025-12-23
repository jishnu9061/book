<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
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
        DB::table('contacts')->truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $organizations = Organization::limit(15)->get();
        Contact::factory(12)->create()->each(function ($contact) use ($organizations) {
            $contact->update(['organization_id' => $organizations->random()->id]);
        });
    }
}
