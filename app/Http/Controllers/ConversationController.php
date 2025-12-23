<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use App\Events\NewChatMessage;
use App\Events\ConversationCreated;
use Inertia\Inertia;

class ConversationController extends Controller
{
    /**
     * Store a newly created conversation
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ticket_id' => 'required|exists:tickets,id',
                'conversation_type' => 'required|in:internal,customer',
                'participants' => 'required|array|min:1',
                'participants.*.user_id' => 'required|exists:users,id',
                'participants.*.role' => 'required|in:customer,agent,participant',
                'initial_message' => 'required|string|max:1000',
                'context' => 'nullable|array'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors in a consistent format
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $user->load('role'); // Ensure role is loaded
            
            // Create the conversation
            $conversation = Conversation::create([
                'ticket_id' => $request->ticket_id,
                'type' => $request->conversation_type,
                'created_by' => $user->id,
                'context' => $request->context ?? []
            ]);

            // Get creator's role (agent for admin/manager/agent, customer otherwise)
            $creatorRole = $user->role->slug ?? 'customer';
            $isAdmin = in_array($creatorRole, ['admin', 'manager', 'agent']);
            $creatorParticipantRole = $isAdmin ? 'agent' : 'customer';

            // Add creator as a participant first (they must always be a participant)
            $creatorParticipantExists = false;
            foreach ($request->participants as $participant) {
                if ($participant['user_id'] == $user->id) {
                    $creatorParticipantExists = true;
                    break;
                }
            }

            // Add creator if not already in participants list
            if (!$creatorParticipantExists) {
                $conversation->participants()->create([
                    'user_id' => $user->id,
                    'role' => $creatorParticipantRole,
                    'joined_at' => now()
                ]);
            }

            // Add other participants
            foreach ($request->participants as $participant) {
                // Skip if this is the creator (already added above)
                if ($participant['user_id'] == $user->id) {
                    continue;
                }
                $conversation->participants()->create([
                    'user_id' => $participant['user_id'],
                    'role' => $participant['role'],
                    'joined_at' => now()
                ]);
            }

            // Create initial message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'message' => $request->initial_message,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Load relationships for broadcasting
            $message->load('user');

            // Fire conversation created event
            event(new ConversationCreated($conversation, $request->participants, $user));

            // Broadcast the message (non-blocking - don't fail if Pusher is misconfigured)
            try {
                broadcast(new NewChatMessage($message));
            } catch (\Exception $broadcastException) {
                // Log broadcast error but don't fail the conversation creation
                Log::warning('Failed to broadcast message (non-critical)', [
                    'conversation_id' => $conversation->id,
                    'message_id' => $message->id,
                    'error' => $broadcastException->getMessage()
                ]);
            }

            // Convert to arrays to avoid serialization issues
            $conversation->load('participants.user');
            $responseData = [
                'success' => true,
                'message' => 'Conversation started successfully',
                'data' => [
                    'conversation' => [
                        'id' => $conversation->id,
                        'type' => $conversation->type,
                        'title' => $conversation->title,
                        'ticket_id' => $conversation->ticket_id,
                        'created_by' => $conversation->created_by,
                        'created_at' => $conversation->created_at,
                        'updated_at' => $conversation->updated_at,
                        'participants' => $conversation->participants->map(function($participant) {
                            return [
                                'id' => $participant->id,
                                'user_id' => $participant->user_id,
                                'role' => $participant->role,
                                'user' => $participant->user ? [
                                    'id' => $participant->user->id,
                                    'first_name' => $participant->user->first_name ?? '',
                                    'last_name' => $participant->user->last_name ?? '',
                                    'email' => $participant->user->email ?? '',
                                ] : null,
                            ];
                        })->values(),
                    ],
                    'initial_message' => [
                        'id' => $message->id,
                        'conversation_id' => $message->conversation_id,
                        'message' => $message->message,
                        'user_id' => $message->user_id,
                        'created_at' => $message->created_at,
                        'updated_at' => $message->updated_at,
                        'user' => $message->user ? [
                            'id' => $message->user->id,
                            'first_name' => $message->user->first_name ?? '',
                            'last_name' => $message->user->last_name ?? '',
                            'email' => $message->user->email ?? '',
                        ] : null,
                    ]
                ]
            ];

            return response()->json($responseData, 201);

        } catch (\Exception $e) {
            Log::error('Failed to create conversation', [
                'user_id' => Auth::id(),
                'ticket_id' => $request->ticket_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to start conversation',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the conversation'
            ], 500);
        }
    }

    /**
     * Display the specified conversation
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Check if user is a participant in this conversation
        $isParticipant = $conversation->participants()
            ->where('user_id', $user->id)
            ->exists();
        
        // Check if user is admin/manager/agent
        $isAdmin = in_array($user->role->slug ?? '', ['admin', 'manager', 'agent']);
        
        // Determine access permissions
        // - Participants can always access conversations they're part of
        // - Admins can access internal conversations (but not customer conversations they're not part of)
        $canAccess = $isParticipant || ($isAdmin && $conversation->type === 'internal');
        
        if (!$canAccess) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized conversation access attempt via API', [
                'user_id' => $user->id,
                'user_role' => $user->role->slug ?? 'unknown',
                'conversation_id' => $conversation->id,
                'conversation_type' => $conversation->type,
                'is_participant' => $isParticipant,
                'is_admin' => $isAdmin,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this conversation'
            ], 403);
        }
        
        $conversation->load(['participants.user', 'messages.user', 'ticket']);

        return response()->json([
            'success' => true,
            'data' => $conversation
        ]);
    }

    /**
     * View conversation page
     */
    public function view(Conversation $conversation)
    {
        $user = Auth::user();
        $user->load('role'); // Ensure role relationship is loaded
        
        // Check if user is a participant in this conversation
        $isParticipant = $conversation->participants()
            ->where('user_id', $user->id)
            ->exists();
        
        // Check if user is admin/manager/agent
        $isAdmin = in_array($user->role->slug ?? '', ['admin', 'manager', 'agent']);
        
        // Determine access permissions
        // - Participants can always access conversations they're part of
        // - Admins can access internal conversations (but not customer conversations they're not part of)
        $canAccess = $isParticipant || ($isAdmin && $conversation->type === 'internal');
        
        if (!$canAccess) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized conversation access attempt via view', [
                'user_id' => $user->id,
                'user_role' => $user->role->slug ?? 'unknown',
                'conversation_id' => $conversation->id,
                'conversation_type' => $conversation->type,
                'is_participant' => $isParticipant,
                'is_admin' => $isAdmin,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            abort(403, 'You are not authorized to view this conversation');
        }
        
        $conversation->load(['participants.user', 'messages.user', 'ticket']);

        // Create a clean conversation object to avoid circular references
        $cleanConversation = [
            'id' => $conversation->id,
            'type' => $conversation->type,
            'title' => $conversation->title,
            'created_at' => $conversation->created_at,
            'updated_at' => $conversation->updated_at,
            'ticket' => $conversation->ticket ? [
                'id' => $conversation->ticket->id,
                'uid' => $conversation->ticket->uid,
                'subject' => $conversation->ticket->subject
            ] : null,
            'participants' => $conversation->participants->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'role' => $participant->role,
                    'user' => $participant->user ? [
                        'id' => $participant->user->id,
                        'name' => $participant->user->first_name . ' ' . $participant->user->last_name,
                        'first_name' => $participant->user->first_name,
                        'last_name' => $participant->user->last_name,
                        'email' => $participant->user->email
                    ] : null
                ];
            }),
            'messages' => $conversation->messages->map(function($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                    'user' => $message->user ? [
                        'id' => $message->user->id,
                        'name' => $message->user->first_name . ' ' . $message->user->last_name,
                        'first_name' => $message->user->first_name,
                        'last_name' => $message->user->last_name,
                        'email' => $message->user->email
                    ] : null
                ];
            })
        ];

        return Inertia::render('Conversations/View', [
            'title' => 'Conversation #' . $conversation->id,
            'conversation' => $cleanConversation,
            'user' => $user ? [
                'id' => $user->id,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'email' => $user->email ?? '',
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'slug' => $user->role->slug ?? 'na',
                    'name' => $user->role->name ?? 'Not Assigned',
                ] : ['slug' => 'na', 'name' => 'Not Assigned'],
            ] : null,
            'availableUsers' => User::whereHas('role', function($query) {
                $query->whereIn('slug', ['admin', 'manager', 'agent']);
            })->select('id', 'first_name', 'last_name', 'email')->get()->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email
                ];
            }),
        ]);
    }

    /**
     * Get conversations for a ticket
     */
    public function getTicketConversations(Request $request, $ticketId)
    {
        $conversations = Conversation::where('ticket_id', $ticketId)
            ->with(['participants.user', 'messages.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    /**
     * Send a message to a conversation
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            $user = Auth::user();
            
            // Check if user is a participant in this conversation
            $isParticipant = $conversation->participants()
                ->where('user_id', $user->id)
                ->exists();
            
            // Also check if user is the creator (they should always have access)
            $isCreator = $conversation->created_by == $user->id;

            // If user is the creator but not a participant (edge case - fix it automatically)
            if ($isCreator && !$isParticipant) {
                Log::info('Creator not found in participants, adding them automatically', [
                    'user_id' => $user->id,
                    'conversation_id' => $conversation->id,
                ]);
                
                // Determine creator's role
                $user->load('role');
                $creatorRole = $user->role->slug ?? 'customer';
                $isAdmin = in_array($creatorRole, ['admin', 'manager', 'agent']);
                $participantRole = $isAdmin ? 'agent' : 'customer';
                
                // Add creator as participant
                $conversation->participants()->create([
                    'user_id' => $user->id,
                    'role' => $participantRole,
                    'joined_at' => now()
                ]);
                
                $isParticipant = true; // Update flag
            }

            if (!$isParticipant && !$isCreator) {
                // Log for debugging
                Log::warning('User tried to send message to conversation they are not part of', [
                    'user_id' => $user->id,
                    'conversation_id' => $conversation->id,
                    'created_by' => $conversation->created_by,
                    'is_creator' => $isCreator,
                    'is_participant' => $isParticipant,
                    'participant_count' => $conversation->participants()->count(),
                    'participant_user_ids' => $conversation->participants()->pluck('user_id')->toArray(),
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not a participant in this conversation'
                    ], 403);
                }
                return back()->withErrors(['message' => 'You are not a participant in this conversation']);
            }

            // Create the message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'message' => $request->message,
                'user_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Load relationships for broadcasting
            $message->load('user');

            // Broadcast the message (non-blocking - don't fail if Pusher is misconfigured)
            try {
                broadcast(new NewChatMessage($message));
            } catch (\Exception $broadcastException) {
                // Log broadcast error but don't fail the message sending
                Log::warning('Failed to broadcast message (non-critical)', [
                    'conversation_id' => $conversation->id,
                    'message_id' => $message->id,
                    'error' => $broadcastException->getMessage()
                ]);
            }

            // Convert to array to avoid serialization issues
            $messageData = [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'message' => $message->message,
                'user_id' => $message->user_id,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
                'user' => $message->user ? [
                    'id' => $message->user->id,
                    'first_name' => $message->user->first_name ?? '',
                    'last_name' => $message->user->last_name ?? '',
                    'email' => $message->user->email ?? '',
                ] : null,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => $messageData
                ], 201);
            }

            return back()->with('success', 'Message sent successfully');

        } catch (\Exception $e) {
            Log::error('Error sending message', [
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message',
                    'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while sending the message'
                ], 500);
            }
            
            return back()->withErrors(['message' => 'Failed to send message: ' . $e->getMessage()]);
        }
    }
}