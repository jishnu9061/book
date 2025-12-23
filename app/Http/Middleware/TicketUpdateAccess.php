<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketUpdateAccess
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
        $user = Auth::user();
        
        // Ensure user is authenticated
        if (!$user) {
            abort(401, 'Authentication required');
        }
        
        // Check if user has permission to update tickets
        if (!$user->access['ticket']['update']) {
            // Log unauthorized access attempt
            \Log::warning('Unauthorized ticket update access attempt via middleware', [
                'user_id' => $user->id,
                'user_role' => $user->role->slug ?? 'unknown',
                'route' => $request->route()->getName(),
                'ticket_id' => $request->route('ticket')?->id,
                'ticket_uid' => $request->route('ticket')?->uid ?? $request->route('uid'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            abort(403, 'You do not have permission to update tickets');
        }
        
        // For customers, additional check will be done in the controller
        // since we need to verify they can only edit their own tickets
        
        return $next($request);
    }
}





