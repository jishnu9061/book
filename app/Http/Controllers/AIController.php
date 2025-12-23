<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\AITicketClassification;
use App\Services\AI\AITicketClassificationService;
use App\Services\AI\AIResponseSuggestionsService;
use App\Services\AI\AISentimentAnalysisService;
use App\Services\AI\AIPredictiveAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class AIController extends Controller
{
    private $aiService;
    private $responseService;
    private $sentimentService;
    private $analyticsService;

    public function __construct(
        AITicketClassificationService $aiService,
        AIResponseSuggestionsService $responseService,
        AISentimentAnalysisService $sentimentService,
        AIPredictiveAnalyticsService $analyticsService
    ) {
        $this->aiService = $aiService;
        $this->responseService = $responseService;
        $this->sentimentService = $sentimentService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get AI classification suggestions for a ticket
     */
    public function getClassificationSuggestions(Ticket $ticket): JsonResponse
    {
        try {
            $suggestions = $this->aiService->getSuggestions($ticket);
            
            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get AI suggestions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get AI suggestions',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Apply AI classification to a ticket
     */
    public function applyClassification(Request $request, Ticket $ticket): JsonResponse
    {
        $request->validate([
            'priority_id' => 'nullable|exists:priorities,id',
            'category_id' => 'nullable|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'type_id' => 'nullable|exists:types,id',
            'confidence_score' => 'nullable|numeric|between:0,1',
            'reasoning' => 'nullable|string|max:1000'
        ]);

        try {
            $classification = [
                'priority' => $request->priority_id,
                'category' => $request->category_id,
                'department' => $request->department_id,
                'type' => $request->type_id,
                'confidence' => $request->confidence_score ?? 0.0,
                'reasoning' => $request->reasoning ?? 'Manual application',
                'ai_generated' => false,
                'timestamp' => now()
            ];

            $success = $this->aiService->applyClassification($ticket, $classification);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Classification applied successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to apply classification'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Failed to apply AI classification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply classification',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Auto-classify a ticket using AI
     */
    public function autoClassify(Ticket $ticket): JsonResponse
    {
        try {
            $classification = $this->aiService->classifyTicket($ticket);
            $success = $this->aiService->applyClassification($ticket, $classification);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket classified successfully',
                    'data' => $classification
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to classify ticket'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Failed to auto-classify ticket: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to classify ticket',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get AI service status
     */
    public function getStatus(): JsonResponse
    {
        try {
            $status = $this->aiService->getStatus();
            
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get AI status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get AI status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get AI classification history for a ticket
     */
    public function getClassificationHistory(Ticket $ticket): JsonResponse
    {
        try {
            $classifications = $ticket->aiClassifications()
                ->with(['priority', 'category', 'department', 'type'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $classifications
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get classification history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get classification history',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Batch classify multiple tickets
     */
    public function batchClassify(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_ids' => 'required|array|max:10',
            'ticket_ids.*' => 'exists:tickets,id'
        ]);

        try {
            $results = [];
            $tickets = Ticket::whereIn('id', $request->ticket_ids)->get();

            foreach ($tickets as $ticket) {
                $classification = $this->aiService->classifyTicket($ticket);
                $success = $this->aiService->applyClassification($ticket, $classification);
                
                $results[] = [
                    'ticket_id' => $ticket->id,
                    'ticket_uid' => $ticket->uid,
                    'success' => $success,
                    'classification' => $classification
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Batch classification completed',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to batch classify tickets: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to batch classify tickets',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get AI analytics and insights
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 7);
            
            // Get predictive analytics
            $predictiveAnalytics = $this->analyticsService->getPredictiveAnalytics($days);
            
            // Get classification analytics
            $classificationAnalytics = [
                'total_classifications' => AITicketClassification::count(),
                'high_confidence_classifications' => AITicketClassification::highConfidence()->count(),
                'applied_classifications' => AITicketClassification::applied()->count(),
                'ai_generated_classifications' => AITicketClassification::aiGenerated()->count(),
                'average_confidence' => AITicketClassification::avg('confidence_score'),
                'classification_accuracy' => $this->calculateAccuracy(),
                'recent_activity' => AITicketClassification::with(['ticket', 'priority', 'category', 'department', 'type'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
            ];

            $analytics = array_merge($predictiveAnalytics, [
                'classification' => $classificationAnalytics
            ]);

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get AI analytics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get AI analytics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get AI settings
     */
    public function getSettings(): JsonResponse
    {
        try {
            $settings = [
                'ai_enabled' => config('ai.enabled', false),
                'openai_api_key' => config('ai.openai.api_key') ? '***' . substr(config('ai.openai.api_key'), -4) : '',
                'openai_model' => config('ai.openai.model', 'gpt-3.5-turbo'),
                'openai_max_tokens' => config('ai.openai.max_tokens', 500),
                'auto_classify_new_tickets' => config('ai.classification.auto_classify_new_tickets', true),
                'confidence_threshold' => config('ai.classification.confidence_threshold', 0.7),
                'cache_duration' => config('ai.classification.cache_duration', 3600),
                'rate_limit_per_minute' => config('ai.performance.rate_limit_per_minute', 60),
                'rate_limit_per_hour' => config('ai.performance.rate_limit_per_hour', 1000),
                'batch_size' => config('ai.classification.batch_size', 10),
                'timeout' => config('ai.openai.timeout', 30)
            ];

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get AI settings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get AI settings',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Save AI settings
     */
    public function saveSettings(Request $request): JsonResponse
    {
        $request->validate([
            'ai_enabled' => 'boolean',
            'openai_api_key' => 'nullable|string',
            'openai_model' => 'string|in:gpt-3.5-turbo,gpt-4,gpt-4-turbo',
            'openai_max_tokens' => 'integer|min:100|max:4000',
            'auto_classify_new_tickets' => 'boolean',
            'confidence_threshold' => 'numeric|min:0.1|max:1.0',
            'cache_duration' => 'integer|min:300|max:86400',
            'rate_limit_per_minute' => 'integer|min:1|max:100',
            'rate_limit_per_hour' => 'integer|min:10|max:2000',
            'batch_size' => 'integer|min:1|max:50',
            'timeout' => 'integer|min:5|max:120'
        ]);

        try {
            // Update .env file with new settings
            $envEditor = DotenvEditor::load();

            // Map form fields to .env variables
            $envMappings = [
                'ai_enabled' => 'AI_ENABLED',
                'openai_api_key' => 'OPENAI_API_KEY',
                'openai_model' => 'OPENAI_MODEL',
                'openai_max_tokens' => 'OPENAI_MAX_TOKENS',
                'auto_classify_new_tickets' => 'AI_AUTO_CLASSIFY_NEW_TICKETS',
                'confidence_threshold' => 'AI_CONFIDENCE_THRESHOLD',
                'cache_duration' => 'AI_CACHE_DURATION',
                'rate_limit_per_minute' => 'AI_RATE_LIMIT_PER_MINUTE',
                'rate_limit_per_hour' => 'AI_RATE_LIMIT_PER_HOUR',
                'batch_size' => 'AI_BATCH_SIZE',
                'timeout' => 'OPENAI_TIMEOUT'
            ];

            foreach ($envMappings as $formField => $envVar) {
                if ($request->has($formField)) {
                    $value = $request->input($formField);
                    
                    // Convert boolean to string
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }
                    
                    $envEditor->setKey($envVar, $value);
                }
            }

            $envEditor->save();

            // Clear configuration cache
            \Artisan::call('config:clear');
            \Artisan::call('cache:clear');

            return response()->json([
                'success' => true,
                'message' => 'AI settings saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save AI settings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save AI settings',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get AI response suggestions for a ticket
     */
    public function getResponseSuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'context' => 'nullable|string|max:1000'
        ]);

        try {
            $ticket = Ticket::findOrFail($request->ticket_id);
            $suggestions = $this->responseService->generateResponseSuggestions($ticket, $request->context);
            
            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get response suggestions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get response suggestions',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Analyze sentiment of a ticket
     */
    public function analyzeSentiment(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id'
        ]);

        try {
            $ticket = Ticket::findOrFail($request->ticket_id);
            $sentiment = $this->sentimentService->analyzeSentiment($ticket);
            
            // Check if escalation is recommended
            $shouldEscalate = $this->sentimentService->shouldEscalate($sentiment);
            
            return response()->json([
                'success' => true,
                'data' => array_merge($sentiment, [
                    'should_escalate' => $shouldEscalate,
                    'sentiment_color' => $this->sentimentService->getSentimentColor($sentiment),
                    'urgency_color' => $this->sentimentService->getUrgencyColor($sentiment['urgency_level'])
                ])
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to analyze sentiment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze sentiment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get knowledge base suggestions for a ticket
     */
    public function getKnowledgeBaseSuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id'
        ]);

        try {
            $ticket = Ticket::findOrFail($request->ticket_id);
            $suggestions = $this->responseService->getKnowledgeBaseSuggestions($ticket);
            
            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get knowledge base suggestions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get knowledge base suggestions',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get comprehensive AI analysis for a ticket
     */
    public function getComprehensiveAnalysis(Ticket $ticket): JsonResponse
    {
        try {
            $analysis = [
                'classification' => $this->aiService->getSuggestions($ticket),
                'sentiment' => $this->sentimentService->analyzeSentiment($ticket),
                'response_suggestions' => $this->responseService->generateResponseSuggestions($ticket),
                'knowledge_base' => $this->responseService->getKnowledgeBaseSuggestions($ticket)
            ];

            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get comprehensive analysis: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get comprehensive analysis',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Calculate classification accuracy (placeholder implementation)
     */
    private function calculateAccuracy(): float
    {
        // This would typically involve comparing AI classifications with manual corrections
        // For now, return a placeholder value
        return 0.85; // 85% accuracy
    }
}
