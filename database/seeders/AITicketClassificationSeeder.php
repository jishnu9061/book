<?php

namespace Database\Seeders;

use App\Models\AITicketClassification;
use App\Models\Ticket;
use App\Models\Priority;
use App\Models\Category;
use App\Models\Department;
use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AITicketClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing AI classifications
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('ai_ticket_classifications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tickets = Ticket::all();
        $priorities = Priority::all();
        $categories = Category::all();
        $departments = Department::all();
        $types = Type::all();

        if ($tickets->isEmpty()) {
            $this->command->error('No tickets found. Please run ticket seeder first.');
            return;
        }

        $classificationScenarios = [
            [
                'confidence_score' => 0.95,
                'reasoning' => 'High confidence classification based on technical keywords and urgency indicators',
                'ai_generated' => true,
                'applied' => true,
                'applied_at' => Carbon::now()->subHours(2)
            ],
            [
                'confidence_score' => 0.87,
                'reasoning' => 'Good confidence classification based on customer inquiry patterns',
                'ai_generated' => true,
                'applied' => true,
                'applied_at' => Carbon::now()->subHours(5)
            ],
            [
                'confidence_score' => 0.72,
                'reasoning' => 'Moderate confidence classification - some ambiguity in ticket content',
                'ai_generated' => true,
                'applied' => false,
                'applied_at' => null
            ],
            [
                'confidence_score' => 0.65,
                'reasoning' => 'Lower confidence classification - manual review recommended',
                'ai_generated' => true,
                'applied' => false,
                'applied_at' => null
            ],
            [
                'confidence_score' => 0.0,
                'reasoning' => 'Default classification - AI service was unavailable',
                'ai_generated' => false,
                'applied' => false,
                'applied_at' => null
            ]
        ];

        foreach ($tickets as $ticket) {
            $scenario = $classificationScenarios[array_rand($classificationScenarios)];
            
            // Create AI classification
            AITicketClassification::create([
                'ticket_id' => $ticket->id,
                'priority_id' => $priorities->random()->id,
                'category_id' => $categories->random()->id,
                'department_id' => $departments->random()->id,
                'type_id' => $types->random()->id,
                'confidence_score' => $scenario['confidence_score'],
                'reasoning' => $scenario['reasoning'],
                'ai_generated' => $scenario['ai_generated'],
                'classification_data' => [
                    'model_used' => 'gpt-3.5-turbo',
                    'processing_time' => rand(100, 2000) . 'ms',
                    'keywords_detected' => fake()->words(5),
                    'sentiment_score' => rand(-10, 10) / 10,
                    'complexity_score' => rand(1, 10) / 10,
                    'language_detected' => 'en',
                    'category_confidence' => rand(60, 95) / 100,
                    'priority_confidence' => rand(70, 95) / 100
                ],
                'applied' => $scenario['applied'],
                'applied_at' => $scenario['applied_at'],
                'created_at' => $ticket->created_at->addMinutes(rand(5, 30)),
                'updated_at' => $ticket->created_at->addMinutes(rand(5, 30))
            ]);
        }

        $this->command->info('Created ' . AITicketClassification::count() . ' AI classifications for tickets');
    }
}
