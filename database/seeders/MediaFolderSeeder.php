<?php

namespace Database\Seeders;

use App\Models\MediaFolder;
use Illuminate\Database\Seeder;

class MediaFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a root-level folder to act as the default owner for media.
        // We use firstOrCreate to ensure it's only created once and is safe to run multiple times.
        MediaFolder::firstOrCreate(
            ['id' => 1], // We hard-code the ID to 1 so we can always find it.
            [
                'name' => 'System Media Holder',
                'parent_id' => null
            ]
        );

        MediaFolder::firstOrCreate(
            [
                'name' => 'Images',
                'parent_id' => null
            ]
        );
    }
}
