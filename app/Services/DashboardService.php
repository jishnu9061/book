<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Role;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected $cacheTimeout = 300; // 5 minutes

    public function getDashboardData($user)
    {
        $cacheKey = "dashboard_data_{$user->id}_{$user->role->slug}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($user) {
            return $this->buildDashboardData($user);
        });
    }

    protected function buildDashboardData($user)
    {
        $role = $user->role->slug;
        $filters = $this->getUserFilters($user);
        
        return [
            'tickets' => $this->getTicketMetrics($filters),
            'analytics' => $this->getAnalyticsData($filters, $role),
            'performance' => $this->getPerformanceMetrics($filters),
            'chart_data' => $this->getChartData($filters),
            'user_stats' => $this->getUserStats($role),
        ];
    }

    protected function getUserFilters($user)
    {
        $filters = [
            'byUser' => null,
            'byAssign' => null,
            'avgWhere' => []
        ];

        $closedStatus = Status::where('slug', 'like', '%closed%')->first();
        if ($closedStatus) {
            $filters['avgWhere'][] = ['status_id', '!=', $closedStatus->id];
        }

        switch ($user->role->slug) {
            case 'customer':
                $filters['byUser'] = $user->id;
                $filters['avgWhere'][] = ['user_id', '=', $user->id];
                break;
            case 'agent':
                $filters['byAssign'] = $user->id;
                $filters['avgWhere'][] = ['assigned_to', '=', $user->id];
                break;
        }

        return $filters;
    }

    protected function getTicketMetrics($filters)
    {
        $closedStatus = Status::where('slug', 'like', '%closed%')->first();
        $closedStatusId = $closedStatus ? $closedStatus->id : null;

        $baseQuery = Ticket::byUser($filters['byUser'])->byAssign($filters['byAssign']);

        return [
            'total' => $baseQuery->count(),
            'opened' => $closedStatusId ? $baseQuery->where('status_id', '!=', $closedStatusId)->count() : $baseQuery->count(),
            'closed' => $closedStatusId ? $baseQuery->where('status_id', $closedStatusId)->count() : 0,
            'new_today' => $baseQuery->whereDate('created_at', today())->count(),
            'unassigned' => $baseQuery->whereNull('assigned_to')->count(),
        ];
    }

    protected function getAnalyticsData($filters, $role)
    {
        if (!in_array($role, ['admin', 'manager'])) {
            return [];
        }

        return [
            'departments' => $this->getDepartmentAnalytics($filters),
            'types' => $this->getTypeAnalytics($filters),
            'creators' => $this->getCreatorAnalytics($filters),
        ];
    }

    protected function getDepartmentAnalytics($filters)
    {
        $query = Ticket::selectRaw("department_id, count(id) as total")
            ->groupBy('department_id')
            ->orderBy('total', 'DESC');

        if ($filters['byUser']) {
            $query->where('user_id', $filters['byUser']);
        }
        if ($filters['byAssign']) {
            $query->where('assigned_to', $filters['byAssign']);
        }

        $departments = $query->with('department')->get();
        $totalCount = $departments->sum('total');

        return $this->formatAnalyticsData($departments, $totalCount, 'department');
    }

    protected function getTypeAnalytics($filters)
    {
        $query = Ticket::selectRaw("type_id, count(id) as total")
            ->groupBy('type_id')
            ->orderBy('total', 'DESC');

        if ($filters['byUser']) {
            $query->where('user_id', $filters['byUser']);
        }
        if ($filters['byAssign']) {
            $query->where('assigned_to', $filters['byAssign']);
        }

        $types = $query->with('ticketType')->get();
        $totalCount = $types->sum('total');

        return $this->formatAnalyticsData($types, $totalCount, 'ticketType');
    }

    protected function getCreatorAnalytics($filters)
    {
        $query = Ticket::selectRaw("user_id, count(id) as total")
            ->groupBy('user_id')
            ->orderBy('total', 'DESC')
            ->limit(5);

        if ($filters['byUser']) {
            $query->where('user_id', $filters['byUser']);
        }
        if ($filters['byAssign']) {
            $query->where('assigned_to', $filters['byAssign']);
        }

        $creators = $query->with('user')->get();
        $totalCount = $creators->sum('total');

        return $this->formatAnalyticsData($creators, $totalCount, 'user');
    }

    protected function formatAnalyticsData($items, $totalCount, $relationName)
    {
        $colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1'
        ];

        $formatted = [];
        foreach ($items as $index => $item) {
            $name = $item->{$relationName}->name ?? 'Unknown';
            $count = $item->total;
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 2) : 0;

            $formatted[] = [
                'name' => $name,
                'count' => $count,
                'value' => $percentage,
                'label' => "{$name} {$percentage}% ({$count})",
                'color' => $colors[$index % count($colors)]
            ];
        }

        return $formatted;
    }

    protected function getPerformanceMetrics($filters)
    {
        $responseTimes = $this->calculateResponseTimes($filters);
        
        return [
            'first_response' => $responseTimes['first'],
            'last_response' => $responseTimes['last'],
            'avg_response_time' => $responseTimes['average'],
        ];
    }

    protected function calculateResponseTimes($filters)
    {
        $query = DB::table('tickets')
            ->select(DB::raw("
                MIN(TIME_TO_SEC(TIMEDIFF(response, created_at))) AS min_time,
                MAX(TIME_TO_SEC(TIMEDIFF(response, created_at))) AS max_time,
                AVG(TIME_TO_SEC(TIMEDIFF(response, created_at))) AS avg_time
            "))
            ->whereNotNull('response')
            ->where($filters['avgWhere']);

        $result = $query->first();

        return [
            'first' => $result->min_time ? $this->formatTime($result->min_time) : [],
            'last' => $result->max_time ? $this->formatTime($result->max_time) : [],
            'average' => $result->avg_time ? $this->formatTime($result->avg_time) : [],
        ];
    }

    protected function formatTime($seconds)
    {
        if (!$seconds) return [];
        
        $interval = CarbonInterval::seconds((int)$seconds)->cascade();
        return explode(' ', $interval->forHumans());
    }

    protected function getChartData($filters)
    {
        $months = [];
        $previousMonths = [];
        
        // Get last 12 months
        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::today()->startOfMonth()->subMonth($i);
            $previousMonths[] = $month->shortMonthName;
        }

        // Get ticket counts for each month
        $startDate = Carbon::today()->startOfMonth()->subMonths(11);
        $endDate = Carbon::today();

        $query = Ticket::selectRaw("
                DATE_FORMAT(created_at, '%b') as month,
                COUNT(*) as count
            ")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"));

        if ($filters['byUser']) {
            $query->where('user_id', $filters['byUser']);
        }
        if ($filters['byAssign']) {
            $query->where('assigned_to', $filters['byAssign']);
        }

        $monthlyData = $query->pluck('count', 'month')->toArray();
        $totalCount = array_sum($monthlyData);

        // Fill in missing months with 0
        foreach ($previousMonths as $month) {
            $months[$month] = $monthlyData[$month] ?? 0;
        }

        return [
            'months' => $months,
            'previous_months' => array_reverse($previousMonths),
            'total' => $totalCount,
            'last_month' => $this->getLastMonthCount($filters),
            'this_month' => $this->getThisMonthCount($filters),
        ];
    }

    protected function getLastMonthCount($filters)
    {
        $lastMonth = Carbon::now()->subMonth();
        $startDate = $lastMonth->startOfMonth();
        $endDate = $lastMonth->endOfMonth();

        $query = Ticket::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($filters['byUser']) {
            $query->where('user_id', $filters['byUser']);
        }
        if ($filters['byAssign']) {
            $query->where('assigned_to', $filters['byAssign']);
        }

        return $query->count();
    }

    protected function getThisMonthCount($filters)
    {
        $thisMonth = Carbon::now();
        $startDate = $thisMonth->startOfMonth();
        $endDate = $thisMonth->endOfMonth();

        $query = Ticket::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($filters['byUser']) {
            $query->where('user_id', $filters['byUser']);
        }
        if ($filters['byAssign']) {
            $query->where('assigned_to', $filters['byAssign']);
        }

        return $query->count();
    }

    protected function getUserStats($role)
    {
        if (!in_array($role, ['admin', 'manager'])) {
            return [];
        }

        $customerRole = Role::where('slug', 'customer')->first();
        $totalCustomers = $customerRole ? User::where('role_id', $customerRole->id)->count() : 0;
        $totalContacts = Contact::count();

        return [
            'customers' => $totalCustomers,
            'contacts' => $totalContacts,
        ];
    }

    public function clearCache($userId = null)
    {
        if ($userId) {
            Cache::forget("dashboard_data_{$userId}_admin");
            Cache::forget("dashboard_data_{$userId}_manager");
            Cache::forget("dashboard_data_{$userId}_agent");
            Cache::forget("dashboard_data_{$userId}_customer");
        } else {
            Cache::flush();
        }
    }
}
