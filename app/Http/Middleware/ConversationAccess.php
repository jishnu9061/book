<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $conversation = $request->route('conversation');
        $user = Auth::user();
        
        // Ensure user is authenticated
        if (!$user) {
            abort(401, 'Authentication required');
        }
        
        // Ensure conversation exists
        if (!$conversation) {
            abort(404, 'Conversation not found');
        }
        
        // Load user role if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        // Check if user is a participant in this conversation
        // Use fresh query to ensure we get the latest data
        $isParticipant = $conversation->participants()
            ->where('user_id', $user->id)
            ->exists();
        
        // Also check if user is the creator (they should always have access)
        $isCreator = $conversation->created_by == $user->id;
        
        // If user is the creator but not a participant (edge case - fix it automatically)
        if ($isCreator && !$isParticipant) {
            \Log::info('Creator not found in participants in middleware, adding them automatically', [
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
            ]);
            
            // Determine creator's role
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
        
        // Check if user is admin/manager/agent
        $isAdmin = in_array($user->role->slug ?? '', ['admin', 'manager', 'agent']);
        
        // Determine access permissions
        // - Creator can always access their conversations
        // - Participants can always access conversations they're part of
        // - Admins can access internal conversations (but not customer conversations they're not part of)
        $canAccess = $isCreator || $isParticipant || ($isAdmin && $conversation->type === 'internal');
        
        if (!$canAccess) {
            // Log unauthorized access attempt with detailed info
            \Log::warning('Unauthorized conversation access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role->slug ?? 'unknown',
                'conversation_id' => $conversation->id,
                'conversation_type' => $conversation->type,
                'created_by' => $conversation->created_by,
                'is_creator' => $isCreator,
                'is_participant' => $isParticipant,
                'is_admin' => $isAdmin,
                'participant_count' => $conversation->participants()->count(),
                'participant_user_ids' => $conversation->participants()->pluck('user_id')->toArray(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            abort(403, 'You are not authorized to access this conversation');
        }
        
        return $next($request);
    }
}





