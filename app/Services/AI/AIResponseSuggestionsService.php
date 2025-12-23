<?php

namespace App\Services\AI;

use App\Models\Ticket;
use App\Models\Comment;
use App\Models\Faq;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIResponseSuggestionsService
{
    private $openaiClient;
    private $enabled;

    public function __construct()
    {
        $this->enabled = config('ai.enabled', false) && config('ai.response_suggestions.enabled', true);
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
            Log::error('Failed to initialize OpenAI client for response suggestions: ' . $e->getMessage());
            $this->enabled = false;
        }
    }

    /**
     * Generate response suggestions for a ticket
     */
    public function generateResponseSuggestions(Ticket $ticket, string $context = ''): array
    {
        if (!$this->enabled) {
            return $this->getDefaultSuggestions();
        }

        // Check rate limiting
        if (!$this->checkRateLimit()) {
            Log::warning('AI Response Suggestions rate limit exceeded');
            return $this->getDefaultSuggestions('Rate limit exceeded - please try again later');
        }

        try {
            $cacheKey = "ai_response_suggestions_" . md5($ticket->id . $context);
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                return $cached;
            }

            $suggestions = $this->performResponseGeneration($ticket, $context);
            
            // Cache the result for 30 minutes
            Cache::put($cacheKey, $suggestions, 1800);
            
            return $suggestions;
        } catch (\Exception $e) {
            Log::error('AI Response Suggestions failed: ' . $e->getMessage());
            
            // Check if it's a rate limit error
            if (strpos($e->getMessage(), 'rate limit') !== false) {
                return $this->getDefaultSuggestions('OpenAI rate limit exceeded - please try again later');
            }
            
            return $this->getDefaultSuggestions('AI service temporarily unavailable');
        }
    }

    /**
     * Perform the actual AI response generation
     */
    private function performResponseGeneration(Ticket $ticket, string $context): array
    {
        $prompt = $this->buildResponsePrompt($ticket, $context);
        
        $response = $this->openaiClient->chat()->create([
            'model' => config('ai.openai.model', 'gpt-3.5-turbo'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert customer support agent. Generate professional, helpful, and empathetic response suggestions for helpdesk tickets. Consider the ticket context, customer situation, and provide multiple response options with different tones.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => config('ai.response_suggestions.max_tokens', 800),
            'temperature' => 0.7
        ]);

        $content = $response->choices[0]->message->content;
        return $this->parseResponseSuggestions($content);
    }

    /**
     * Build the response generation prompt
     */
    private function buildResponsePrompt(Ticket $ticket, string $context): string
    {
        $prompt = "Generate 3 professional response suggestions for this helpdesk ticket:\n\n";
        $prompt .= "Ticket Subject: {$ticket->subject}\n";
        $prompt .= "Ticket Description: {$ticket->description}\n";
        $prompt .= "Priority: {$ticket->priority?->name}\n";
        $prompt .= "Category: {$ticket->category?->name}\n";
        $prompt .= "Department: {$ticket->department?->name}\n";
        
        if ($context) {
            $prompt .= "Additional Context: {$context}\n";
        }

        // Add recent comments for context
        $recentComments = Comment::where('ticket_id', $ticket->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        if ($recentComments->count() > 0) {
            $prompt .= "\nRecent Comments:\n";
            foreach ($recentComments as $comment) {
                $prompt .= "- {$comment->user?->first_name}: {$comment->comment}\n";
            }
        }

        $prompt .= "\nGenerate 3 response suggestions with different approaches:\n";
        $prompt .= "1. Professional and formal tone\n";
        $prompt .= "2. Friendly and empathetic tone\n";
        $prompt .= "3. Technical and solution-focused tone\n\n";
        
        $prompt .= "Return a JSON response with the following structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"suggestions\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"tone\": \"professional\",\n";
        $prompt .= "      \"content\": \"Response text here\",\n";
        $prompt .= "      \"confidence\": 0.85,\n";
        $prompt .= "      \"tags\": [\"acknowledgment\", \"next-steps\"]\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"overall_confidence\": 0.82,\n";
        $prompt .= "  \"reasoning\": \"Brief explanation of the suggestions\"\n";
        $prompt .= "}\n\n";
        
        $prompt .= "Guidelines:\n";
        $prompt .= "- Be empathetic and understanding\n";
        $prompt .= "- Provide clear next steps when possible\n";
        $prompt .= "- Acknowledge the customer's concern\n";
        $prompt .= "- Use appropriate tone for the situation\n";
        $prompt .= "- Keep responses concise but complete\n";
        $prompt .= "- Include relevant tags for categorization\n";

        return $prompt;
    }

    /**
     * Parse the AI response suggestions
     */
    private function parseResponseSuggestions(string $content): array
    {
        try {
            // Extract JSON from response
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}') + 1;
            
            if ($jsonStart === false || $jsonEnd === false) {
                throw new \Exception('No JSON found in response');
            }
            
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart);
            $suggestions = json_decode($jsonString, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            return $this->validateAndNormalizeSuggestions($suggestions);
        } catch (\Exception $e) {
            Log::error('Failed to parse AI response suggestions: ' . $e->getMessage());
            return $this->getDefaultSuggestions();
        }
    }

    /**
     * Validate and normalize the suggestions
     */
    private function validateAndNormalizeSuggestions(array $suggestions): array
    {
        $default = $this->getDefaultSuggestions();
        
        if (!isset($suggestions['suggestions']) || !is_array($suggestions['suggestions'])) {
            return $default;
        }

        $validatedSuggestions = [];
        foreach ($suggestions['suggestions'] as $suggestion) {
            if (isset($suggestion['content']) && !empty($suggestion['content'])) {
                $validatedSuggestions[] = [
                    'tone' => $suggestion['tone'] ?? 'professional',
                    'content' => $suggestion['content'],
                    'confidence' => min(max($suggestion['confidence'] ?? 0.5, 0.0), 1.0),
                    'tags' => $suggestion['tags'] ?? [],
                    'ai_generated' => true,
                    'timestamp' => now()
                ];
            }
        }

        if (empty($validatedSuggestions)) {
            return $default;
        }

        return [
            'suggestions' => $validatedSuggestions,
            'overall_confidence' => min(max($suggestions['overall_confidence'] ?? 0.5, 0.0), 1.0),
            'reasoning' => $suggestions['reasoning'] ?? 'AI-generated response suggestions',
            'ai_enabled' => true
        ];
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
     * Get default suggestions when AI is not available
     */
    private function getDefaultSuggestions(string $reason = 'Default suggestions - AI not available'): array
    {
        return [
            'suggestions' => [
                [
                    'tone' => 'professional',
                    'content' => 'Thank you for contacting us. We have received your request and will review it shortly. Our team will get back to you as soon as possible.',
                    'confidence' => 0.0,
                    'tags' => ['acknowledgment'],
                    'ai_generated' => false,
                    'timestamp' => now()
                ],
                [
                    'tone' => 'friendly',
                    'content' => 'Hi there! Thanks for reaching out to us. I understand your concern and I\'m here to help. Let me look into this for you right away.',
                    'confidence' => 0.0,
                    'tags' => ['acknowledgment', 'friendly'],
                    'ai_generated' => false,
                    'timestamp' => now()
                ],
                [
                    'tone' => 'technical',
                    'content' => 'I\'ve reviewed your request and can provide the following solution. Please follow these steps to resolve the issue.',
                    'confidence' => 0.0,
                    'tags' => ['solution', 'technical'],
                    'ai_generated' => false,
                    'timestamp' => now()
                ]
            ],
            'overall_confidence' => 0.0,
            'reasoning' => $reason,
            'ai_enabled' => false
        ];
    }

    /**
     * Get knowledge base suggestions for a ticket
     */
    public function getKnowledgeBaseSuggestions(Ticket $ticket): array
    {
        try {
            // Search FAQs and Knowledge Base articles
            $faqs = Faq::where('is_active', true)
                ->where(function($query) use ($ticket) {
                    $query->where('question', 'like', '%' . $ticket->subject . '%')
                          ->orWhere('answer', 'like', '%' . $ticket->subject . '%')
                          ->orWhere('question', 'like', '%' . $ticket->description . '%')
                          ->orWhere('answer', 'like', '%' . $ticket->description . '%');
                })
                ->limit(3)
                ->get();

            $kbArticles = KnowledgeBase::where('is_active', true)
                ->where(function($query) use ($ticket) {
                    $query->where('title', 'like', '%' . $ticket->subject . '%')
                          ->orWhere('content', 'like', '%' . $ticket->subject . '%')
                          ->orWhere('title', 'like', '%' . $ticket->description . '%')
                          ->orWhere('content', 'like', '%' . $ticket->description . '%');
                })
                ->limit(3)
                ->get();

            $suggestions = [];
            
            foreach ($faqs as $faq) {
                $suggestions[] = [
                    'type' => 'faq',
                    'id' => $faq->id,
                    'title' => $faq->question,
                    'content' => $faq->answer,
                    'relevance_score' => $this->calculateRelevanceScore($ticket, $faq->question . ' ' . $faq->answer),
                    'url' => route('faqs.show', $faq->id)
                ];
            }

            foreach ($kbArticles as $article) {
                $suggestions[] = [
                    'type' => 'knowledge_base',
                    'id' => $article->id,
                    'title' => $article->title,
                    'content' => $article->content,
                    'relevance_score' => $this->calculateRelevanceScore($ticket, $article->title . ' ' . $article->content),
                    'url' => route('kb.details', $article->id)
                ];
            }

            // Sort by relevance score
            usort($suggestions, function($a, $b) {
                return $b['relevance_score'] <=> $a['relevance_score'];
            });

            return array_slice($suggestions, 0, config('ai.knowledge_base.max_suggestions', 5));

        } catch (\Exception $e) {
            Log::error('Failed to get knowledge base suggestions: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate relevance score for knowledge base suggestions
     */
    private function calculateRelevanceScore(Ticket $ticket, string $content): float
    {
        $ticketText = strtolower($ticket->subject . ' ' . $ticket->description);
        $contentText = strtolower($content);
        
        $ticketWords = array_filter(explode(' ', $ticketText));
        $contentWords = array_filter(explode(' ', $contentText));
        
        $commonWords = array_intersect($ticketWords, $contentWords);
        $totalWords = count($ticketWords);
        
        if ($totalWords === 0) {
            return 0.0;
        }
        
        return min(count($commonWords) / $totalWords, 1.0);
    }

    /**
     * Check if AI is enabled and available
     */
    public function isAvailable(): bool
    {
        return $this->enabled && $this->openaiClient !== null;
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->enabled,
            'available' => $this->isAvailable(),
            'model' => config('ai.openai.model', 'gpt-3.5-turbo'),
            'max_suggestions' => config('ai.response_suggestions.max_suggestions', 3),
            'last_check' => now()
        ];
    }
}
