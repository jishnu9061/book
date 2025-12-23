<?php

namespace Database\Seeders;

use App\Models\FrontPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FrontPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('front_pages')->truncate();
        $jsonPath = public_path('json/front_page.json');
        if (!file_exists($jsonPath)) {
            $this->command->error("JSON file not found at: {$jsonPath}");
            return;
        }
        $json = file_get_contents($jsonPath);
        $frontPages = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("JSON decode error: " . json_last_error_msg());
            return;
        }
        if (!is_array($frontPages)) {
            $this->command->error("Invalid JSON structure. Expected array.");
            return;
        }
        foreach ($frontPages as $frontPage){
            FrontPage::create([
                'title' => $frontPage['title'],
                'slug' => $frontPage['slug'],
                'is_active' => $frontPage['is_active'],
                'html' => json_encode($frontPage['html']),
            ]);
        }
    }
}
