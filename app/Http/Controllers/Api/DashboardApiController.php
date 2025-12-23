<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardApiController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get dashboard data
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->role) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated or role not found'
                ], 401);
            }

            $data = $this->dashboardService->getDashboardData($user);

            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get real-time ticket metrics
     */
    public function getTicketMetrics(): JsonResponse
    {
        try {
            $user = Auth::user();
            $filters = $this->dashboardService->getUserFilters($user);
            $metrics = $this->dashboardService->getTicketMetrics($filters);

            return response()->json([
                'success' => true,
                'data' => $metrics,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ticket metrics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get analytics data
     */
    public function getAnalytics(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role->slug, ['admin', 'manager', 'agent'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions'
                ], 403);
            }

            $filters = $this->dashboardService->getUserFilters($user);
            $analytics = $this->dashboardService->getAnalyticsData($filters, $user->role->slug);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): JsonResponse
    {
        try {
            $user = Auth::user();
            $filters = $this->dashboardService->getUserFilters($user);
            $metrics = $this->dashboardService->getPerformanceMetrics($filters);

            return response()->json([
                'success' => true,
                'data' => $metrics,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch performance metrics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get chart data
     */
    public function getChartData(): JsonResponse
    {
        try {
            $user = Auth::user();
            $filters = $this->dashboardService->getUserFilters($user);
            $chartData = $this->dashboardService->getChartData($filters);

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->dashboardService->clearCache($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully',
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get dashboard statistics for a specific date range
     */
    public function getDateRangeStats(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // This would require additional methods in DashboardService
            // For now, return a placeholder response
            return response()->json([
                'success' => true,
                'message' => 'Date range statistics feature coming soon',
                'data' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'tickets' => 0,
                    'resolved' => 0,
                    'pending' => 0
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch date range statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
