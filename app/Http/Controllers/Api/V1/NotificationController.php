<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseApiController
{
    public function index(Request $request)
    {
        [$perPage, $page] = $this->getPaginationParams($request);

        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if (empty($notification)) {
            return $this->notFoundResponse('Notification not found');
        }

        $notification->markAsRead();

        return $this->successResponse(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        return $this->successResponse(['message' => 'All notifications marked as read']);
    }
}

