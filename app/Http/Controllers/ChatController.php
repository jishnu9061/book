<?php

namespace App\Http\Controllers;

use App\Events\NewPublicChatMessage;
use App\Http\Middleware\RedirectIfCustomer;
use App\Http\Middleware\RedirectIfNotParmitted;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use App\Events\NewChatMessage;

class ChatController extends Controller {

    public function index(Request $request){
        $this->middleware(RedirectIfCustomer::class);
        return Inertia::render('Chat/Index', [
            'title' => 'Chat',
            'filters' => $request->only(['search']),
            'chat' => null,
            'conversations' => Conversation::orderBy('updated_at', 'DESC')
                ->filter($request->only(['search']))
                ->withCount([
                    'messages',
                    'messages as messages_count' => function ($query) {
                        $query->whereNotNull('user_id')->where('is_read', '=', 0);
                    }])
                ->paginate(10)
                ->withQueryString()
                ->through(function ($chat) {
                    return [
                        'id' => $chat->id,
                        'slug' => $chat->slug??'',
                        'total_entry' => $chat->messages_count,
                        'title' => $chat->title,
                        'creator' => $chat->creator ? $chat->creator->first_name . ' ' . $chat->creator->last_name : 'Unknown',
                        'created_at' => $chat->created_at,
                        'updated_at' => $chat->updated_at,
                    ];
                }),
        ]);
    }

    public function init(Request $request){
        $data = $request->validate([
            'first_name' => 'required|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:50',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'source' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Find or create contact
            $contact = Contact::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email']
                ]
            );

            // Check for existing active conversation
            $conversation = Conversation::where('contact_id', $contact->id)
                ->where('status', 'active')
                ->first();

            if (!$conversation) {
                // Create new conversation
                $conversation = Conversation::create([
                    'contact_id' => $contact->id,
                    'title' => 'New conversation from ' . $contact->first_name,
                    'status' => 'active',
                    'priority' => $data['priority'] ?? 'medium',
                    'department' => $data['department'] ?? 'general',
                    'source' => $data['source'] ?? 'website',
                    'metadata' => json_encode([
                        'subject' => $data['subject'] ?? null,
                        'initiated_at' => now()->toISOString(),
                        'customer_name' => $contact->first_name . ' ' . $contact->last_name
                    ]),
                    'last_activity' => now()
                ]);

                // Create initial welcome message
                $welcomeMessage = "Hello " . $contact->first_name . "! Welcome to our support chat. How can I help you today?";

                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'message' => $welcomeMessage,
                    'message_type' => 'text',
                    'is_read' => false,
                    'user_id' => null, // System message
                    'contact_id' => null
                ]);

                // Update conversation title with first message
                $conversation->update(['title' => $welcomeMessage]);

                // Assign to available admin (optional)
                $adminRole = Role::where('slug', 'admin')->first();
                if ($adminRole) {
                    $availableAdmin = User::where('role_id', $adminRole->id)
                        ->first();

                    if ($availableAdmin) {
                        Participant::create([
                            'conversation_id' => $conversation->id,
                            'user_id' => $availableAdmin->id,
                            'contact_id' => $contact->id
                        ]);
                    }
                }

            // Broadcast the conversation creation
            broadcast(new NewChatMessage($message));
            }

            // Load conversation with all relationships
            $conversation->load([
                'creator',
                'messages' => function($query) {
                    $query->orderBy('created_at', 'asc');
                },
                'messages.user',
                'participants.user'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'conversation' => $conversation,
                'message' => 'Chat initialized successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Chat initialization failed: ' . $e->getMessage(), [
                'email' => $data['email'] ?? $request->input('email', 'unknown'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize chat. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getConversation($id, $contact_id){
        $this->middleware(RedirectIfCustomer::class);
        $conversation = Conversation::with([
            'creator',
            'messages' => function($q){
                $q->orderBy('updated_at', 'asc');
            },
            'messages.attachments',
            'participant',
            'participant.user'
        ])->where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->where('contact_id', $contact_id)->first();
        return response()->json($conversation);
    }

    public function chat(Request $request, $id){
        Message::where(['conversation_id' => $id, 'is_read' => 0])->update(array('is_read' => 1));
        return Inertia::render('Chat/Index', [
            'title' => 'Chat',
            'filters' => $request->only(['search']),
            'chat' => Conversation::with([
                'creator',
                'messages' => function($q){
                    $q->orderBy('updated_at', 'asc');
                },
                'messages.contact',
                'messages.user',
                'messages.attachments',
                'participant',
                'participant.creator'
            ])
                ->where(function ($query) use ($id) {
                    $query->where('id', $id)->orWhere('slug', $id);
                })->first(),
            'conversations' => Conversation::orderBy('updated_at', 'DESC')
                ->filter($request->only(['search']))
                ->withCount([
                    'messages',
                    'messages as messages_count' => function ($query) {
                        $query->whereNotNull('user_id')->where('is_read', '=', 0);
                    }])
                ->paginate(10)
                ->withQueryString()
                ->through(function ($chat) {
                    return [
                        'id' => $chat->id,
                        'slug' => $chat->slug??'',
                        'total_entry' => $chat->messages_count,
                        'title' => $chat->title,
                        'creator' => $chat->creator ? $chat->creator->first_name . ' ' . $chat->creator->last_name : 'Unknown',
                        'created_at' => $chat->created_at,
                        'updated_at' => $chat->updated_at,
                    ];
                }),
        ]);
    }

    public function emptyChat(Request $request){
        return Inertia::render('Chat/Index', [
            'filters' => $request->only('search'),
            'title' => 'Chat',
            'chat' => Conversation::with([
                'creator',
                'messages' => function($q){
                    $q->orderBy('updated_at', 'asc');
                },
                'messages.contact',
                'messages.user',
                'messages.attachments',
                'participant',
                'participant.creator'
            ])->first(),
            'conversations' => Conversation::orderBy('updated_at', 'DESC')
                ->filter(Request::only('search'))
                ->withCount([
                    'messages',
                    'messages as messages_count' => function ($query) {
                        $query->where('is_read', '=', 0);
                    }])
                ->paginate(10)
                ->withQueryString()
                ->through(function ($chat) {
                    return [
                        'id' => $chat->id,
                        'total_entry' => $chat->messages_count,
                        'title' => $chat->title,
                        'creator' => $chat->creator ? $chat->creator->first_name . ' ' . $chat->creator->last_name : 'Unknown',
                        'created_at' => $chat->created_at,
                        'updated_at' => $chat->updated_at,
                    ];
                }),
        ]);
    }

    public function newMessage(Request $request){
        $this->middleware(RedirectIfCustomer::class);

        // Rate limiting: max 10 messages per minute per user
        $data = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_id' => 'required|exists:conversations,id',
            'user_id' => 'nullable|exists:users,id',
            'message_type' => 'nullable|in:text,image,file,system',
        ]);

        // Check rate limit
        $rateLimitKey = 'chat_messages_' . Auth::id() . '_' . $request->ip();
        $rateLimit = \Cache::get($rateLimitKey, 0);

        if ($rateLimit >= 10) {
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please wait before sending another message.',
                'retry_after' => 60
            ], 429);
        }

        \Cache::put($rateLimitKey, $rateLimit + 1, 60); // 1 minute cache

        try {
            DB::beginTransaction();

            // Verify conversation exists and user has access
            $conversation = Conversation::findOrFail($data['conversation_id']);

            // Create message
            $newMessage = Message::create([
                'message' => $data['message'],
                'conversation_id' => $data['conversation_id'],
                'user_id' => $data['user_id'] ?? Auth::id(),
                'contact_id' => null, // Admin messages don't have contact_id
                'message_type' => $data['message_type'] ?? 'text',
                'is_read' => false
            ]);

            // Load relationships for broadcasting
            $newMessage->load(['user', 'contact']);

            // Update conversation
            $conversation->update([
                'title' => $newMessage->message,
                'last_activity' => now()
            ]);

            // Broadcast the message using the unified channel
            \Log::info('Admin Chat: Broadcasting message', [
                'message_id' => $newMessage->id,
                'conversation_id' => $newMessage->conversation_id,
                'user_id' => $newMessage->user_id,
                'message' => $newMessage->message
            ]);

            broadcast(new NewChatMessage($newMessage))->toOthers();

            \Log::info('Admin Chat: Message broadcast completed', [
                'message_id' => $newMessage->id,
                'conversation_id' => $newMessage->conversation_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $newMessage->id,
                    'message' => $newMessage->message,
                    'conversation_id' => $newMessage->conversation_id,
                    'user_id' => $newMessage->user_id,
                    'contact_id' => $newMessage->contact_id,
                    'message_type' => $newMessage->message_type,
                    'is_read' => $newMessage->is_read,
                    'created_at' => $newMessage->created_at,
                    'updated_at' => $newMessage->updated_at,
                    'user' => $newMessage->user ? [
                        'id' => $newMessage->user->id,
                        'first_name' => $newMessage->user->first_name,
                        'last_name' => $newMessage->user->last_name,
                        'photo' => $newMessage->user->photo_path,
                    ] : null,
                    'contact' => $newMessage->contact ? [
                        'id' => $newMessage->contact->id,
                        'first_name' => $newMessage->contact->first_name,
                        'last_name' => $newMessage->contact->last_name,
                        'email' => $newMessage->contact->email,
                    ] : null,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Message creation failed: ' . $e->getMessage(), [
                'conversation_id' => $data['conversation_id'] ?? $request->input('conversation_id', 'unknown'),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function sendPublicMessage(Request $request){
        $data = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_id' => 'required|exists:conversations,id',
            'contact_id' => 'required|exists:contacts,id',
            'message_type' => 'nullable|in:text,image,file,system',
        ]);

        // Rate limiting for public messages: max 5 messages per minute per IP
        $rateLimitKey = 'public_chat_messages_' . $request->ip();
        $rateLimit = \Cache::get($rateLimitKey, 0);

        if ($rateLimit >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please wait before sending another message.',
                'retry_after' => 60
            ], 429);
        }

        \Cache::put($rateLimitKey, $rateLimit + 1, 60); // 1 minute cache

        try {
            DB::beginTransaction();

            // Verify conversation belongs to the contact
            $conversation = Conversation::where('id', $data['conversation_id'])
                ->where('contact_id', $data['contact_id'])
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found or access denied.'
                ], 404);
            }

            // Create message
            $newMessage = Message::create([
                'message' => $data['message'],
                'conversation_id' => $data['conversation_id'],
                'contact_id' => $data['contact_id'],
                'user_id' => null, // Public messages don't have user_id
                'message_type' => $data['message_type'] ?? 'text',
                'is_read' => false
            ]);

            // Load relationships for broadcasting
            $newMessage->load(['contact', 'user']);

            // Update conversation
            $conversation->update([
                'title' => $newMessage->message,
                'last_activity' => now()
            ]);

            // Broadcast the message using the unified channel
            \Log::info('Public Chat: Broadcasting message', [
                'message_id' => $newMessage->id,
                'conversation_id' => $newMessage->conversation_id,
                'contact_id' => $newMessage->contact_id,
                'message' => $newMessage->message
            ]);

            broadcast(new NewChatMessage($newMessage));

            \Log::info('Public Chat: Message broadcast completed', [
                'message_id' => $newMessage->id,
                'conversation_id' => $newMessage->conversation_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $newMessage->id,
                    'message' => $newMessage->message,
                    'conversation_id' => $newMessage->conversation_id,
                    'user_id' => $newMessage->user_id,
                    'contact_id' => $newMessage->contact_id,
                    'message_type' => $newMessage->message_type,
                    'is_read' => $newMessage->is_read,
                    'created_at' => $newMessage->created_at,
                    'updated_at' => $newMessage->updated_at,
                    'user' => $newMessage->user ? [
                        'id' => $newMessage->user->id,
                        'first_name' => $newMessage->user->first_name,
                        'last_name' => $newMessage->user->last_name,
                        'photo' => $newMessage->user->photo_path,
                    ] : null,
                    'contact' => $newMessage->contact ? [
                        'id' => $newMessage->contact->id,
                        'first_name' => $newMessage->contact->first_name,
                        'last_name' => $newMessage->contact->last_name,
                        'email' => $newMessage->contact->email,
                    ] : null,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Public message creation failed: ' . $e->getMessage(), [
                'conversation_id' => $data['conversation_id'] ?? $request->input('conversation_id', 'unknown'),
                'contact_id' => $data['contact_id'] ?? $request->input('contact_id', 'unknown'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function create()
    {
        $this->middleware(RedirectIfCustomer::class);
        return Inertia::render('Chat/Create');
    }

    public function store(Request $request)
    {
        $this->middleware(RedirectIfCustomer::class);

        $data = $request->validate([
            'creator' => ['required', 'max:100'],
            'contact_id' => ['required', 'exists:contacts,id'],
            'title' => ['nullable', 'max:100'],
            'priority' => ['nullable', 'in:low,medium,high,urgent'],
            'department' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $conversation = Conversation::create([
                'title' => $data['title'] ?? 'New Conversation',
                'contact_id' => $data['contact_id'],
                'status' => 'active',
                'priority' => $data['priority'] ?? 'medium',
                'department' => $data['department'] ?? 'general',
                'source' => 'admin',
                'last_activity' => now(),
            ]);

            return Redirect::route('chat')->with('success', 'Chat created successfully.');

        } catch (\Exception $e) {
            \Log::error('Chat creation failed: ' . $e->getMessage());
            return Redirect::back()->with('error', 'Failed to create chat. Please try again.');
        }
    }

    public function edit(Conversation $chat)
    {
        $this->middleware(RedirectIfCustomer::class);
        return Inertia::render('Chat/Edit', [
            'chat' => [
                'id' => $chat->id,
                'title' => $chat->title,
                'creator' => $chat->creator ? $chat->creator->first_name . ' ' . $chat->creator->last_name : 'Unknown',
                'created_at' => $chat->created_at,
                'updated_at' => $chat->updated_at,
            ],
        ]);
    }

    public function update(Request $request, Conversation $chat)
    {
        $this->middleware(RedirectIfCustomer::class);

        $data = $request->validate([
            'title' => ['nullable', 'max:100'],
            'status' => ['nullable', 'in:active,inactive,resolved'],
            'priority' => ['nullable', 'in:low,medium,high,urgent'],
            'department' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $updateData = array_filter($data, function($value) {
                return $value !== null;
            });

            if (!empty($updateData)) {
                $updateData['last_activity'] = now();
                $chat->update($updateData);
            }

            return Redirect::back()->with('success', 'Conversation updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Chat update failed: ' . $e->getMessage());
            return Redirect::back()->with('error', 'Failed to update conversation. Please try again.');
        }
    }

    public function destroy(Conversation $chat) {
        $chat->delete();
        return Redirect::route('chat')->with('success', 'Conversation deleted.');
    }

    public function restore(Conversation $chat)
    {
        $chat->restore();

        return Redirect::back()->with('success', 'Conversation restored.');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request)
    {
        $data = $request->validate([
            'message_id' => 'required|exists:messages,id',
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        try {
            $message = Message::where('id', $data['message_id'])
                ->where('conversation_id', $data['conversation_id'])
                ->first();

            if ($message) {
                $message->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Message marked as read'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Mark as read failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark message as read'
            ], 500);
        }
    }

    /**
     * Mark all messages in conversation as read
     */
    public function markConversationAsRead(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        try {
            Message::where('conversation_id', $data['conversation_id'])
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All messages marked as read'
            ]);

        } catch (\Exception $e) {
            \Log::error('Mark conversation as read failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark conversation as read'
            ], 500);
        }
    }

    /**
     * Get typing indicators for a conversation
     */
    public function getTypingIndicators(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        try {
            $indicators = DB::table('chat_typing_indicators')
                ->where('conversation_id', $data['conversation_id'])
                ->where('is_typing', true)
                ->where('updated_at', '>', now()->subSeconds(5))
                ->get();

            return response()->json([
                'success' => true,
                'typing_indicators' => $indicators
            ]);

        } catch (\Exception $e) {
            \Log::error('Get typing indicators failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get typing indicators'
            ], 500);
        }
    }

    /**
     * Update typing indicator
     */
    public function updateTypingIndicator(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'is_typing' => 'required|boolean',
            'user_id' => 'nullable|exists:users,id',
            'contact_id' => 'nullable|exists:contacts,id',
        ]);

        try {
            $indicator = DB::table('chat_typing_indicators')
                ->where('conversation_id', $data['conversation_id'])
                ->where('user_id', $data['user_id'])
                ->where('contact_id', $data['contact_id'])
                ->first();

            if ($indicator) {
                DB::table('chat_typing_indicators')
                    ->where('id', $indicator->id)
                    ->update([
                        'is_typing' => $data['is_typing'],
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('chat_typing_indicators')->insert([
                    'conversation_id' => $data['conversation_id'],
                    'user_id' => $data['user_id'],
                    'contact_id' => $data['contact_id'],
                    'is_typing' => $data['is_typing'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Typing indicator updated'
            ]);

        } catch (\Exception $e) {
            \Log::error('Update typing indicator failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update typing indicator'
            ], 500);
        }
    }

    /**
     * Upload attachments for a conversation
     */
    public function uploadAttachments(Request $request)
    {
        $this->middleware(RedirectIfCustomer::class);

        $data = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'files' => 'required|array|max:5',
            'files.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt,zip,rar,mp4,avi,mov,wmv,mp3,wav,ogg'
        ]);

        try {
            DB::beginTransaction();

            // Verify conversation exists and user has access
            $conversation = Conversation::findOrFail($data['conversation_id']);

            $attachments = [];
            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Store file
                $path = $file->storeAs('chat-attachments', $filename, 'public');

                // Create attachment record
                $attachment = \App\Models\Attachment::create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'user_id' => Auth::id(),
                    'ticket_id' => null, // Chat attachments don't belong to tickets
                    'conversation_id' => $conversation->id
                ]);

                $attachments[] = $attachment;
                $uploadedFiles[] = [
                    'id' => $attachment->id,
                    'name' => $attachment->name,
                    'path' => $attachment->path,
                    'size' => $attachment->size,
                    'mime_type' => $attachment->mime_type,
                    'url' => asset('storage/' . $attachment->path)
                ];
            }

            // Create a message with attachments
            $message = Message::create([
                'message' => '📎 ' . count($attachments) . ' file(s) attached',
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'contact_id' => null,
                'message_type' => 'file',
                'is_read' => false
            ]);

            // Attach files to message
            foreach ($attachments as $attachment) {
                $attachment->update(['message_id' => $message->id]);
            }

            // Load relationships for broadcasting
            $message->load(['user', 'contact', 'attachments']);

            // Update conversation
            $conversation->update([
                'title' => $message->message,
                'last_activity' => now()
            ]);

            // Broadcast the message
            broadcast(new NewChatMessage($message))->toOthers();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'attachments' => $uploadedFiles,
                'message_data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'conversation_id' => $message->conversation_id,
                    'user_id' => $message->user_id,
                    'contact_id' => $message->contact_id,
                    'message_type' => $message->message_type,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                    'user' => $message->user ? [
                        'id' => $message->user->id,
                        'first_name' => $message->user->first_name,
                        'last_name' => $message->user->last_name,
                        'photo' => $message->user->photo_path,
                    ] : null,
                    'contact' => $message->contact ? [
                        'id' => $message->contact->id,
                        'first_name' => $message->contact->first_name,
                        'last_name' => $message->contact->last_name,
                        'email' => $message->contact->email,
                    ] : null,
                    'attachments' => $message->attachments->map(function($attachment) {
                        return [
                            'id' => $attachment->id,
                            'name' => $attachment->name,
                            'path' => $attachment->path,
                            'size' => $attachment->size,
                            'mime_type' => $attachment->mime_type,
                            'url' => asset('storage/' . $attachment->path)
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('File upload failed: ' . $e->getMessage(), [
                'conversation_id' => $data['conversation_id'] ?? $request->input('conversation_id', 'unknown'),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload files. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
