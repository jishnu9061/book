<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseApiController
{
    public function index(Request $request)
    {
        $user = Auth::user()->load('role');
        $byUser = null;
        $byAssign = null;

        if (in_array($user->role->slug, ['customer'])) {
            $byUser = $user->id;
        } elseif (in_array($user->role->slug, ['agent'])) {
            $byAssign = $user->id;
        }

        $openedStatus = Status::where('name', 'Closed')->first();
        $openedTickets = Ticket::byUser($byUser)->byAssign($byAssign);
        if (!empty($openedStatus)) {
            $openedTickets = $openedTickets->where('status_id', '!=', $openedStatus->id);
        }
        $openedTicketsCount = $openedTickets->count();

        $data = [
            'opened_tickets' => $openedTicketsCount,
            'total_tickets' => Ticket::byUser($byUser)->byAssign($byAssign)->count(),
            'closed_tickets' => Ticket::byUser($byUser)->byAssign($byAssign)
                ->where('status_id', $openedStatus->id ?? 0)
                ->count(),
        ];

        return $this->successResponse($data);
    }

    public function metrics(Request $request)
    {
        $user = Auth::user()->load('role');
        $byUser = null;
        $byAssign = null;

        if (in_array($user->role->slug, ['customer'])) {
            $byUser = $user->id;
        } elseif (in_array($user->role->slug, ['agent'])) {
            $byAssign = $user->id;
        }

        $metrics = [
            'tickets_by_status' => Ticket::byUser($byUser)->byAssign($byAssign)
                ->select('status_id', DB::raw('count(*) as total'))
                ->groupBy('status_id')
                ->get(),
            'tickets_by_priority' => Ticket::byUser($byUser)->byAssign($byAssign)
                ->select('priority_id', DB::raw('count(*) as total'))
                ->groupBy('priority_id')
                ->get(),
        ];

        return $this->successResponse($metrics);
    }

    public function analytics(Request $request)
    {
        // Placeholder for analytics data
        return $this->successResponse(['message' => 'Analytics endpoint']);
    }

    public function performance(Request $request)
    {
        // Placeholder for performance metrics
        return $this->successResponse(['message' => 'Performance metrics endpoint']);
    }

    public function charts(Request $request)
    {
        // Placeholder for chart data
        return $this->successResponse(['message' => 'Charts data endpoint']);
    }
}

