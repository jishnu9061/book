<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Ticket;
use App\Services\AI\AITicketClassificationService;
use App\Services\AI\AIResponseSuggestionsService;
use App\Services\AI\AISentimentAnalysisService;
use App\Services\AI\AIPredictiveAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIController extends BaseApiController
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

    public function status()
    {
        try {
            $status = $this->aiService->getStatus();
            return $this->successResponse($status);
        } catch (\Exception $e) {
            Log::error('Failed to get AI status: ' . $e->getMessage());
            return $this->errorResponse('Failed to get AI status', 500);
        }
    }

    public function analytics()
    {
        try {
            $analytics = $this->analyticsService->getAnalytics();
            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            Log::error('Failed to get AI analytics: ' . $e->getMessage());
            return $this->errorResponse('Failed to get AI analytics', 500);
        }
    }

    public function batchClassify(Request $request)
    {
        $requestData = $request->validate([
            'ticket_ids' => ['required', 'array'],
            'ticket_ids.*' => ['exists:tickets,id'],
        ]);

        try {
            $results = [];
            foreach ($requestData['ticket_ids'] as $ticketId) {
                $ticket = Ticket::find($ticketId);
                if ($ticket) {
                    $classification = $this->aiService->classifyTicket($ticket);
                    $results[] = [
                        'ticket_id' => $ticketId,
                        'classification' => $classification,
                    ];
                }
            }
            return $this->successResponse($results);
        } catch (\Exception $e) {
            Log::error('Failed to batch classify: ' . $e->getMessage());
            return $this->errorResponse('Failed to batch classify tickets', 500);
        }
    }

    public function getSettings()
    {
        // Return AI settings from config
        return $this->successResponse([
            'ai_enabled' => config('ai.enabled', false),
            'model' => config('ai.model', 'gpt-3.5-turbo'),
            'auto_classify' => config('ai.auto_classify_new_tickets', false),
        ]);
    }

    public function updateSettings(Request $request)
    {
        // Placeholder - would update settings
        return $this->successResponse(['message' => 'Settings updated']);
    }

    public function responseSuggestions(Request $request)
    {
        $requestData = $request->validate([
            'ticket_id' => ['required', 'exists:tickets,id'],
            'context' => ['nullable', 'string'],
        ]);

        try {
            $ticket = Ticket::find($requestData['ticket_id']);
            $suggestions = $this->responseService->getSuggestions($ticket, $requestData['context'] ?? '');
            return $this->successResponse($suggestions);
        } catch (\Exception $e) {
            Log::error('Failed to get response suggestions: ' . $e->getMessage());
            return $this->errorResponse('Failed to get response suggestions', 500);
        }
    }

    public function sentimentAnalysis(Request $request)
    {
        $requestData = $request->validate([
            'text' => ['required', 'string'],
        ]);

        try {
            $analysis = $this->sentimentService->analyze($requestData['text']);
            return $this->successResponse($analysis);
        } catch (\Exception $e) {
            Log::error('Failed to analyze sentiment: ' . $e->getMessage());
            return $this->errorResponse('Failed to analyze sentiment', 500);
        }
    }

    public function knowledgeBaseSuggestions(Request $request)
    {
        $requestData = $request->validate([
            'query' => ['required', 'string'],
        ]);

        // Placeholder
        return $this->successResponse(['suggestions' => []]);
    }

    public function getTicketSuggestions($ticket)
    {
        try {
            $ticket = Ticket::find($ticket);
            if (empty($ticket)) {
                return $this->notFoundResponse('Ticket not found');
            }

            $suggestions = $this->aiService->getSuggestions($ticket);
            return $this->successResponse($suggestions);
        } catch (\Exception $e) {
            Log::error('Failed to get ticket suggestions: ' . $e->getMessage());
            return $this->errorResponse('Failed to get ticket suggestions', 500);
        }
    }

    public function classifyTicket(Request $request, $ticket)
    {
        try {
            $ticket = Ticket::find($ticket);
            if (empty($ticket)) {
                return $this->notFoundResponse('Ticket not found');
            }

            $classification = $this->aiService->classifyTicket($ticket);
            return $this->successResponse($classification);
        } catch (\Exception $e) {
            Log::error('Failed to classify ticket: ' . $e->getMessage());
            return $this->errorResponse('Failed to classify ticket', 500);
        }
    }

    public function applyClassification(Request $request, $ticket)
    {
        $requestData = $request->validate([
            'priority_id' => ['nullable', 'exists:priorities,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'type_id' => ['nullable', 'exists:types,id'],
            'confidence_score' => ['nullable', 'numeric', 'between:0,1'],
            'reasoning' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $ticket = Ticket::find($ticket);
            if (empty($ticket)) {
                return $this->notFoundResponse('Ticket not found');
            }

            $classification = [
                'priority' => $requestData['priority_id'] ?? null,
                'category' => $requestData['category_id'] ?? null,
                'department' => $requestData['department_id'] ?? null,
                'type' => $requestData['type_id'] ?? null,
                'confidence' => $requestData['confidence_score'] ?? 0.0,
                'reasoning' => $requestData['reasoning'] ?? 'Manual application',
                'ai_generated' => false,
                'timestamp' => now(),
            ];

            $success = $this->aiService->applyClassification($ticket, $classification);

            if ($success) {
                return $this->successResponse(['message' => 'Classification applied successfully']);
            } else {
                return $this->errorResponse('Failed to apply classification', 500);
            }
        } catch (\Exception $e) {
            Log::error('Failed to apply classification: ' . $e->getMessage());
            return $this->errorResponse('Failed to apply classification', 500);
        }
    }

    public function classificationHistory($ticket)
    {
        try {
            $ticket = Ticket::find($ticket);
            if (empty($ticket)) {
                return $this->notFoundResponse('Ticket not found');
            }

            $history = $ticket->aiClassifications()->orderBy('created_at', 'desc')->get();
            return $this->successResponse($history);
        } catch (\Exception $e) {
            Log::error('Failed to get classification history: ' . $e->getMessage());
            return $this->errorResponse('Failed to get classification history', 500);
        }
    }
}

