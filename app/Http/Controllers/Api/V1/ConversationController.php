<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Events\ConversationCreated;
use App\Events\NewChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConversationController extends BaseApiController
{
    /**
     * Display a listing of conversations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Conversation::query()
            ->with(['creator', 'contact', 'ticket', 'participants.user']);

        // Apply filters
        $filters = $request->only(['search', 'ticket_id', 'type', 'status']);
        if (!empty($filters['ticket_id'])) {
            $query->where('ticket_id', $filters['ticket_id']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        $query = $query->filter($filters);

        // Apply sorting
        $allowedSorts = ['id', 'created_at', 'last_message_at'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'last_message_at');

        $conversations = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($conversations, [
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created conversation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'conversation_type' => 'required|in:internal,customer',
            'participants' => 'required|array|min:1',
            'participants.*.user_id' => 'required|exists:users,id',
            'participants.*.role' => 'required|in:customer,agent,participant',
            'initial_message' => 'required|string|max:1000',
            'context' => 'nullable|array',
        ]);

        $user = Auth::user();
        $user->load('role');

        // Create the conversation
        $conversation = Conversation::create([
            'ticket_id' => $request->ticket_id,
            'type' => $request->conversation_type,
            'created_by' => $user->id,
            'context' => $request->context ?? [],
        ]);

        // Add participants
        $creatorRole = $user->role->slug ?? 'customer';
        $isAdmin = in_array($creatorRole, ['admin', 'manager', 'agent']);
        $creatorParticipantRole = $isAdmin ? 'agent' : 'customer';

        foreach ($request->participants as $participant) {
            if ($participant['user_id'] == $user->id) {
                $conversation->participants()->create([
                    'user_id' => $user->id,
                    'role' => $creatorParticipantRole,
                    'joined_at' => now(),
                ]);
            } else {
                $conversation->participants()->create([
                    'user_id' => $participant['user_id'],
                    'role' => $participant['role'],
                    'joined_at' => now(),
                ]);
            }
        }

        // Create initial message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => $request->initial_message,
            'user_id' => $user->id,
        ]);

        $message->load('user');

        // Fire events
        event(new ConversationCreated($conversation, $request->participants, $user));

        try {
            broadcast(new NewChatMessage($message));
        } catch (\Exception $e) {
            Log::warning('Failed to broadcast message', ['error' => $e->getMessage()]);
        }

        return $this->successResponse([
            'conversation' => $conversation->load('participants.user'),
            'initial_message' => $message,
        ], 201);
    }

    /**
     * Display the specified conversation.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $conversation = Conversation::with(['creator', 'contact', 'ticket', 'participants.user', 'messages.user', 'messages.contact'])
            ->find($id);

        if (empty($conversation)) {
            return $this->notFoundResponse('Conversation not found');
        }

        return $this->successResponse($conversation);
    }

    /**
     * Update the specified conversation.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $conversation = Conversation::find($id);

        if (empty($conversation)) {
            return $this->notFoundResponse('Conversation not found');
        }

        $requestData = $request->validate([
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'nullable', 'string'],
            'priority' => ['sometimes', 'nullable', 'string'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ]);

        $conversation->update($requestData);

        return $this->successResponse($conversation->load(['creator', 'contact', 'ticket', 'participants.user']));
    }

    /**
     * Remove the specified conversation.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $conversation = Conversation::find($id);

        if (empty($conversation)) {
            return $this->notFoundResponse('Conversation not found');
        }

        $conversation->delete();

        return $this->successResponse(null, 204);
    }

    /**
     * Restore a soft-deleted conversation.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $conversation = Conversation::withTrashed()->find($id);

        if (empty($conversation)) {
            return $this->notFoundResponse('Conversation not found');
        }

        $conversation->restore();

        return $this->successResponse($conversation->load(['creator', 'contact', 'ticket', 'participants.user']));
    }

    /**
     * Send a message in a conversation.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::find($id);

        if (empty($conversation)) {
            return $this->notFoundResponse('Conversation not found');
        }

        $requestData = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'is_internal' => ['sometimes', 'boolean'],
        ]);

        $user = Auth::user();
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => $requestData['message'],
            'user_id' => $user->id,
            'is_internal' => $requestData['is_internal'] ?? false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $message->load('user');

        try {
            broadcast(new NewChatMessage($message));
        } catch (\Exception $e) {
            Log::warning('Failed to broadcast message', ['error' => $e->getMessage()]);
        }

        return $this->successResponse($message, 201);
    }

    /**
     * Get messages for a conversation.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages(Request $request, $id)
    {
        $conversation = Conversation::find($id);

        if (empty($conversation)) {
            return $this->notFoundResponse('Conversation not found');
        }

        [$perPage, $page] = $this->getPaginationParams($request);

        $messages = Message::where('conversation_id', $conversation->id)
            ->with(['user', 'contact'])
            ->orderBy('created_at', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($messages);
    }

    /**
     * Mark conversation as read.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markRead(Request $request, $id)
    {
        $conversation = Conversation::find($id);

        if (empty($conversation)) {
            return $this->notFoundResponse('Conversation not found');
        }

        $user = Auth::user();
        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', $user->id)
            ->update(['is_read' => true, 'read_at' => now()]);

        return $this->successResponse(['message' => 'Conversation marked as read']);
    }
}

