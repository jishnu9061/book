<?php

namespace App\Services\AI;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Department;
use App\Models\Priority;
use App\Models\Type;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AITicketClassificationService
{
    private $openaiClient;
    private $enabled;

    public function __construct()
    {
        $this->enabled = config('ai.enabled', false);
        $this->initializeOpenAI();
    }

    private function initializeOpenAI()
    {
        if (!$this->enabled) {
            return;
        }

        try {
            $this->openaiClient = \OpenAI::client(config('ai.openai.api_key'));
        } catch (\Exception $e) {
            Log::error('Failed to initialize OpenAI client: ' . $e->getMessage());
            $this->enabled = false;
        }
    }

    /**
     * Classify a ticket using AI
     */
    public function classifyTicket(Ticket $ticket): array
    {
        if (!$this->enabled) {
            return $this->getDefaultClassification();
        }

        // Check rate limiting
        if (!$this->checkRateLimit()) {
            Log::warning('AI Classification rate limit exceeded');
            return $this->getDefaultClassification('Rate limit exceeded - please try again later');
        }

        try {
            $cacheKey = "ai_classification_" . md5($ticket->subject . $ticket->description);
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                return $cached;
            }

            $classification = $this->performClassification($ticket);
            
            // Cache the result for 1 hour
            Cache::put($cacheKey, $classification, 3600);
            
            return $classification;
        } catch (\Exception $e) {
            Log::error('AI Classification failed: ' . $e->getMessage());
            
            // Check if it's a rate limit error
            if (strpos($e->getMessage(), 'rate limit') !== false) {
                return $this->getDefaultClassification('OpenAI rate limit exceeded - please try again later');
            }
            
            return $this->getDefaultClassification('AI service temporarily unavailable');
        }
    }

    /**
     * Perform the actual AI classification
     */
    private function performClassification(Ticket $ticket): array
    {
        $prompt = $this->buildClassificationPrompt($ticket);
        
        $response = $this->openaiClient->chat()->create([
            'model' => config('ai.openai.model', 'gpt-3.5-turbo'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert helpdesk ticket classifier. Analyze the ticket content and provide accurate classifications.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 500,
            'temperature' => 0.3
        ]);

        $content = $response->choices[0]->message->content;
        return $this->parseClassificationResponse($content);
    }

    /**
     * Build the classification prompt
     */
    private function buildClassificationPrompt(Ticket $ticket): string
    {
        $availableCategories = Category::pluck('name')->implode(', ');
        $availableDepartments = Department::pluck('name')->implode(', ');
        $availablePriorities = Priority::pluck('name')->implode(', ');
        $availableTypes = Type::pluck('name')->implode(', ');

        $prompt = "Analyze this helpdesk ticket and classify it accurately:\n\n";
        $prompt .= "Subject: {$ticket->subject}\n";
        $prompt .= "Description: {$ticket->description}\n\n";
        
        $prompt .= "Available Categories: {$availableCategories}\n";
        $prompt .= "Available Departments: {$availableDepartments}\n";
        $prompt .= "Available Priorities: {$availablePriorities}\n";
        $prompt .= "Available Types: {$availableTypes}\n\n";
        
        $prompt .= "Return a JSON response with the following structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"priority\": \"exact priority name from available list\",\n";
        $prompt .= "  \"category\": \"exact category name from available list\",\n";
        $prompt .= "  \"department\": \"exact department name from available list\",\n";
        $prompt .= "  \"type\": \"exact type name from available list\",\n";
        $prompt .= "  \"confidence\": 0.85,\n";
        $prompt .= "  \"reasoning\": \"brief explanation of classification decision\"\n";
        $prompt .= "}\n\n";
        
        $prompt .= "Guidelines:\n";
        $prompt .= "- Priority: Consider urgency and impact (urgent > high > medium > low)\n";
        $prompt .= "- Category: Match the most specific category\n";
        $prompt .= "- Department: Assign to the most appropriate department\n";
        $prompt .= "- Type: Choose the most relevant ticket type\n";
        $prompt .= "- Confidence: Rate your confidence (0.0 to 1.0)\n";
        $prompt .= "- Only use exact names from the available lists\n";

        return $prompt;
    }

    /**
     * Parse the AI response
     */
    private function parseClassificationResponse(string $content): array
    {
        try {
            // Extract JSON from response
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}') + 1;
            
            if ($jsonStart === false || $jsonEnd === false) {
                throw new \Exception('No JSON found in response');
            }
            
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart);
            $classification = json_decode($jsonString, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            return $this->validateAndNormalizeClassification($classification);
        } catch (\Exception $e) {
            Log::error('Failed to parse AI classification response: ' . $e->getMessage());
            return $this->getDefaultClassification();
        }
    }

    /**
     * Validate and normalize the classification
     */
    private function validateAndNormalizeClassification(array $classification): array
    {
        $default = $this->getDefaultClassification();
        
        return [
            'priority' => $this->findMatchingRecord(Priority::class, $classification['priority'] ?? '') ?: $default['priority'],
            'category' => $this->findMatchingRecord(Category::class, $classification['category'] ?? '') ?: $default['category'],
            'department' => $this->findMatchingRecord(Department::class, $classification['department'] ?? '') ?: $default['department'],
            'type' => $this->findMatchingRecord(Type::class, $classification['type'] ?? '') ?: $default['type'],
            'confidence' => min(max($classification['confidence'] ?? 0.5, 0.0), 1.0),
            'reasoning' => $classification['reasoning'] ?? 'AI classification completed',
            'ai_generated' => true,
            'timestamp' => now()
        ];
    }

    /**
     * Find matching record by name
     */
    private function findMatchingRecord(string $model, string $name): ?int
    {
        if (empty($name)) {
            return null;
        }
        
        $record = $model::where('name', 'like', '%' . $name . '%')->first();
        return $record ? $record->id : null;
    }

    /**
     * Check rate limiting
     */
    private function checkRateLimit(): bool
    {
        $minuteKey = 'ai_requests_minute_' . now()->format('Y-m-d-H-i');
        $hourKey = 'ai_requests_hour_' . now()->format('Y-m-d-H');
        
        $minuteCount = Cache::get($minuteKey, 0);
        $hourCount = Cache::get($hourKey, 0);
        
        $minuteLimit = config('ai.performance.rate_limit_per_minute', 60);
        $hourLimit = config('ai.performance.rate_limit_per_hour', 1000);
        
        if ($minuteCount >= $minuteLimit || $hourCount >= $hourLimit) {
            return false;
        }
        
        // Increment counters
        Cache::put($minuteKey, $minuteCount + 1, 60);
        Cache::put($hourKey, $hourCount + 1, 3600);
        
        return true;
    }

    /**
     * Get default classification when AI is not available
     */
    private function getDefaultClassification(string $reason = 'Default classification - AI not available'): array
    {
        return [
            'priority' => Priority::first()?->id,
            'category' => Category::first()?->id,
            'department' => Department::first()?->id,
            'type' => Type::first()?->id,
            'confidence' => 0.0,
            'reasoning' => $reason,
            'ai_generated' => false,
            'timestamp' => now()
        ];
    }

    /**
     * Apply AI classification to a ticket
     */
    public function applyClassification(Ticket $ticket, array $classification): bool
    {
        try {
            $ticket->update([
                'priority_id' => $classification['priority'],
                'category_id' => $classification['category'],
                'department_id' => $classification['department'],
                'type_id' => $classification['type'],
            ]);

            // Store AI classification data
            $ticket->aiClassifications()->create([
                'priority_id' => $classification['priority'],
                'category_id' => $classification['category'],
                'department_id' => $classification['department'],
                'type_id' => $classification['type'],
                'confidence_score' => $classification['confidence'],
                'reasoning' => $classification['reasoning'],
                'ai_generated' => $classification['ai_generated'],
                'classification_data' => $classification
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to apply AI classification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get classification suggestions for a ticket
     */
    public function getSuggestions(Ticket $ticket): array
    {
        $classification = $this->classifyTicket($ticket);
        
        return [
            'suggestions' => [
                'priority' => [
                    'id' => $classification['priority'],
                    'name' => Priority::find($classification['priority'])?->name,
                    'confidence' => $classification['confidence']
                ],
                'category' => [
                    'id' => $classification['category'],
                    'name' => Category::find($classification['category'])?->name,
                    'confidence' => $classification['confidence']
                ],
                'department' => [
                    'id' => $classification['department'],
                    'name' => Department::find($classification['department'])?->name,
                    'confidence' => $classification['confidence']
                ],
                'type' => [
                    'id' => $classification['type'],
                    'name' => Type::find($classification['type'])?->name,
                    'confidence' => $classification['confidence']
                ]
            ],
            'overall_confidence' => $classification['confidence'],
            'reasoning' => $classification['reasoning'],
            'ai_enabled' => $this->enabled
        ];
    }

    /**
     * Check if AI is enabled and available
     */
    public function isAvailable(): bool
    {
        return $this->enabled && $this->openaiClient !== null;
    }

    /**
     * Get AI service status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->enabled,
            'available' => $this->isAvailable(),
            'model' => config('ai.openai.model', 'gpt-3.5-turbo'),
            'last_check' => now()
        ];
    }
}
