<?php

namespace App\Services\AI;

use App\Models\Ticket;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AISentimentAnalysisService
{
    private $openaiClient;
    private $enabled;

    public function __construct()
    {
        $this->enabled = config('ai.enabled', false) && config('ai.sentiment_analysis.enabled', true);
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
            Log::error('Failed to initialize OpenAI client for sentiment analysis: ' . $e->getMessage());
            $this->enabled = false;
        }
    }

    /**
     * Analyze sentiment of a ticket
     */
    public function analyzeSentiment(Ticket $ticket): array
    {
        if (!$this->enabled) {
            return $this->getDefaultSentiment();
        }

        try {
            $cacheKey = "ai_sentiment_" . md5($ticket->id . $ticket->updated_at);
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                return $cached;
            }

            $sentiment = $this->performSentimentAnalysis($ticket);
            
            // Cache the result for 1 hour
            Cache::put($cacheKey, $sentiment, 3600);
            
            return $sentiment;
        } catch (\Exception $e) {
            Log::error('AI Sentiment Analysis failed: ' . $e->getMessage());
            return $this->getDefaultSentiment();
        }
    }

    /**
     * Perform the actual sentiment analysis
     */
    private function performSentimentAnalysis(Ticket $ticket): array
    {
        $prompt = $this->buildSentimentPrompt($ticket);
        
        $response = $this->openaiClient->chat()->create([
            'model' => config('ai.openai.model', 'gpt-3.5-turbo'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert sentiment analysis system. Analyze the emotional tone and sentiment of customer support tickets. Provide detailed analysis including emotional state, urgency level, and escalation recommendations.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 400,
            'temperature' => 0.3
        ]);

        $content = $response->choices[0]->message->content;
        return $this->parseSentimentResponse($content);
    }

    /**
     * Build the sentiment analysis prompt
     */
    private function buildSentimentPrompt(Ticket $ticket): string
    {
        $prompt = "Analyze the sentiment and emotional tone of this customer support ticket:\n\n";
        $prompt .= "Subject: {$ticket->subject}\n";
        $prompt .= "Description: {$ticket->description}\n";
        
        // Add recent comments for context
        $recentComments = Comment::where('ticket_id', $ticket->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($recentComments->count() > 0) {
            $prompt .= "\nRecent Comments:\n";
            foreach ($recentComments as $comment) {
                $prompt .= "- {$comment->user?->first_name}: {$comment->comment}\n";
            }
        }

        $prompt .= "\nProvide a comprehensive sentiment analysis including:\n";
        $prompt .= "1. Overall sentiment (positive, neutral, negative, very negative)\n";
        $prompt .= "2. Emotional indicators (frustrated, angry, confused, satisfied, etc.)\n";
        $prompt .= "3. Urgency level (low, medium, high, critical)\n";
        $prompt .= "4. Escalation recommendation (yes/no with reason)\n";
        $prompt .= "5. Suggested response approach\n\n";
        
        $prompt .= "Return a JSON response with the following structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"overall_sentiment\": \"negative\",\n";
        $prompt .= "  \"sentiment_score\": -0.7,\n";
        $prompt .= "  \"emotional_indicators\": [\"frustrated\", \"impatient\"],\n";
        $prompt .= "  \"urgency_level\": \"high\",\n";
        $prompt .= "  \"escalation_recommended\": true,\n";
        $prompt .= "  \"escalation_reason\": \"Customer shows signs of frustration and impatience\",\n";
        $prompt .= "  \"response_approach\": \"empathetic\",\n";
        $prompt .= "  \"confidence\": 0.85,\n";
        $prompt .= "  \"key_phrases\": [\"urgent\", \"frustrated\", \"not working\"],\n";
        $prompt .= "  \"analysis_summary\": \"Customer is experiencing technical issues and showing frustration\"\n";
        $prompt .= "}\n\n";
        
        $prompt .= "Guidelines:\n";
        $prompt .= "- Sentiment score: -1.0 (very negative) to +1.0 (very positive)\n";
        $prompt .= "- Consider both explicit and implicit emotional cues\n";
        $prompt .= "- Look for urgency indicators (urgent, ASAP, critical, etc.)\n";
        $prompt .= "- Recommend escalation for negative sentiment below -0.3\n";
        $prompt .= "- Be objective and professional in analysis\n";

        return $prompt;
    }

    /**
     * Parse the sentiment analysis response
     */
    private function parseSentimentResponse(string $content): array
    {
        try {
            // Extract JSON from response
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}') + 1;
            
            if ($jsonStart === false || $jsonEnd === false) {
                throw new \Exception('No JSON found in response');
            }
            
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart);
            $sentiment = json_decode($jsonString, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            return $this->validateAndNormalizeSentiment($sentiment);
        } catch (\Exception $e) {
            Log::error('Failed to parse AI sentiment response: ' . $e->getMessage());
            return $this->getDefaultSentiment();
        }
    }

    /**
     * Validate and normalize the sentiment analysis
     */
    private function validateAndNormalizeSentiment(array $sentiment): array
    {
        $default = $this->getDefaultSentiment();
        
        return [
            'overall_sentiment' => $sentiment['overall_sentiment'] ?? 'neutral',
            'sentiment_score' => min(max($sentiment['sentiment_score'] ?? 0.0, -1.0), 1.0),
            'emotional_indicators' => is_array($sentiment['emotional_indicators'] ?? null) ? $sentiment['emotional_indicators'] : [],
            'urgency_level' => $sentiment['urgency_level'] ?? 'medium',
            'escalation_recommended' => $sentiment['escalation_recommended'] ?? false,
            'escalation_reason' => $sentiment['escalation_reason'] ?? '',
            'response_approach' => $sentiment['response_approach'] ?? 'professional',
            'confidence' => min(max($sentiment['confidence'] ?? 0.5, 0.0), 1.0),
            'key_phrases' => is_array($sentiment['key_phrases'] ?? null) ? $sentiment['key_phrases'] : [],
            'analysis_summary' => $sentiment['analysis_summary'] ?? 'Sentiment analysis completed',
            'ai_generated' => true,
            'timestamp' => now()
        ];
    }

    /**
     * Get default sentiment when AI is not available
     */
    private function getDefaultSentiment(): array
    {
        return [
            'overall_sentiment' => 'neutral',
            'sentiment_score' => 0.0,
            'emotional_indicators' => [],
            'urgency_level' => 'medium',
            'escalation_recommended' => false,
            'escalation_reason' => '',
            'response_approach' => 'professional',
            'confidence' => 0.0,
            'key_phrases' => [],
            'analysis_summary' => 'Default sentiment analysis - AI not available',
            'ai_generated' => false,
            'timestamp' => now()
        ];
    }

    /**
     * Check if ticket should be escalated based on sentiment
     */
    public function shouldEscalate(array $sentiment): bool
    {
        if (!$sentiment['ai_generated']) {
            return false;
        }

        $threshold = config('ai.sentiment_analysis.sentiment_threshold', -0.3);
        $autoEscalate = config('ai.sentiment_analysis.auto_escalate_negative', true);

        return $autoEscalate && 
               $sentiment['sentiment_score'] <= $threshold && 
               $sentiment['escalation_recommended'];
    }

    /**
     * Get sentiment color for UI display
     */
    public function getSentimentColor(array $sentiment): string
    {
        $score = $sentiment['sentiment_score'];
        
        if ($score >= 0.3) {
            return 'green'; // Positive
        } elseif ($score >= -0.1) {
            return 'yellow'; // Neutral
        } elseif ($score >= -0.5) {
            return 'orange'; // Negative
        } else {
            return 'red'; // Very negative
        }
    }

    /**
     * Get urgency color for UI display
     */
    public function getUrgencyColor(string $urgencyLevel): string
    {
        switch (strtolower($urgencyLevel)) {
            case 'critical':
                return 'red';
            case 'high':
                return 'orange';
            case 'medium':
                return 'yellow';
            case 'low':
                return 'green';
            default:
                return 'gray';
        }
    }

    /**
     * Analyze sentiment trends over time
     */
    public function analyzeSentimentTrends(int $days = 7): array
    {
        try {
            // This would typically analyze sentiment data over time
            // For now, return placeholder data
            return [
                'trend' => 'stable',
                'average_sentiment' => 0.1,
                'positive_tickets' => 45,
                'negative_tickets' => 15,
                'neutral_tickets' => 40,
                'escalation_rate' => 0.12,
                'period' => $days . ' days'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to analyze sentiment trends: ' . $e->getMessage());
            return [];
        }
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
            'auto_escalate' => config('ai.sentiment_analysis.auto_escalate_negative', true),
            'threshold' => config('ai.sentiment_analysis.sentiment_threshold', -0.3),
            'last_check' => now()
        ];
    }
}
