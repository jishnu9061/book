<?php

namespace App\Services\AI;

use App\Models\Ticket;
use App\Models\AITicketClassification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AIPredictiveAnalyticsService
{
    private $enabled;

    public function __construct()
    {
        $this->enabled = config('ai.enabled', false) && config('ai.analytics.enabled', true);
    }

    /**
     * Get predictive analytics for the dashboard
     */
    public function getPredictiveAnalytics(int $days = 7): array
    {
        if (!$this->enabled) {
            return $this->getDefaultAnalytics();
        }

        try {
            $cacheKey = "ai_predictive_analytics_{$days}";
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                return $cached;
            }

            $analytics = $this->generatePredictiveAnalytics($days);
            
            // Cache for 1 hour
            Cache::put($cacheKey, $analytics, 3600);
            
            return $analytics;
        } catch (\Exception $e) {
            Log::error('AI Predictive Analytics failed: ' . $e->getMessage());
            return $this->getDefaultAnalytics();
        }
    }

    /**
     * Generate predictive analytics data
     */
    private function generatePredictiveAnalytics(int $days): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        // Ticket volume trends
        $ticketVolume = $this->analyzeTicketVolume($startDate, $endDate);
        
        // SLA compliance predictions
        $slaPredictions = $this->analyzeSlaCompliance($startDate, $endDate);
        
        // Resolution time predictions
        $resolutionPredictions = $this->analyzeResolutionTimes($startDate, $endDate);
        
        // Agent workload predictions
        $workloadPredictions = $this->analyzeAgentWorkload($startDate, $endDate);
        
        // Customer satisfaction predictions
        $satisfactionPredictions = $this->analyzeCustomerSatisfaction($startDate, $endDate);

        return [
            'ticket_volume' => $ticketVolume,
            'sla_compliance' => $slaPredictions,
            'resolution_times' => $resolutionPredictions,
            'agent_workload' => $workloadPredictions,
            'customer_satisfaction' => $satisfactionPredictions,
            'ai_enabled' => true,
            'generated_at' => now()
        ];
    }

    /**
     * Analyze ticket volume trends
     */
    private function analyzeTicketVolume($startDate, $endDate): array
    {
        $tickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $volumeData = $tickets->pluck('count', 'date')->toArray();
        $averageVolume = $tickets->avg('count');
        $trend = $this->calculateTrend($volumeData);

        // Predict next 7 days
        $predictions = $this->predictTicketVolume($volumeData, 7);

        return [
            'current_period' => [
                'total_tickets' => $tickets->sum('count'),
                'average_daily' => round($averageVolume, 1),
                'trend' => $trend,
                'trend_percentage' => $this->calculateTrendPercentage($volumeData)
            ],
            'predictions' => $predictions,
            'chart_data' => $this->formatChartData($volumeData, $predictions)
        ];
    }

    /**
     * Analyze SLA compliance
     */
    private function analyzeSlaCompliance($startDate, $endDate): array
    {
        $tickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('sla_breach_at')
            ->get();

        $totalTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
        $breachedTickets = $tickets->count();
        
        $complianceRate = $totalTickets > 0 ? (($totalTickets - $breachedTickets) / $totalTickets) * 100 : 100;
        
        // Predict SLA compliance for next period
        $predictedCompliance = $this->predictSlaCompliance($complianceRate, $breachedTickets, $totalTickets);

        return [
            'current_compliance' => round($complianceRate, 1),
            'breached_tickets' => $breachedTickets,
            'total_tickets' => $totalTickets,
            'predicted_compliance' => $predictedCompliance,
            'risk_level' => $this->getSlaRiskLevel($complianceRate),
            'recommendations' => $this->getSlaRecommendations($complianceRate, $breachedTickets)
        ];
    }

    /**
     * Analyze resolution times
     */
    private function analyzeResolutionTimes($startDate, $endDate): array
    {
        $resolvedTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('close')
            ->get();

        $resolutionTimes = $resolvedTickets->map(function ($ticket) {
            return $ticket->created_at->diffInHours($ticket->close);
        });

        $averageResolutionTime = $resolutionTimes->avg() ?? 24.0; // Default to 24 hours if no data
        $medianResolutionTime = $resolutionTimes->median() ?? 24.0;

        // Predict resolution times for next period
        $predictedResolutionTime = $this->predictResolutionTime($resolutionTimes->toArray());

        return [
            'current_average' => round($averageResolutionTime, 1),
            'current_median' => round($medianResolutionTime, 1),
            'predicted_average' => round($predictedResolutionTime, 1),
            'trend' => $this->calculateResolutionTrend($resolutionTimes->toArray()),
            'performance_level' => $this->getResolutionPerformanceLevel($averageResolutionTime)
        ];
    }

    /**
     * Analyze agent workload
     */
    private function analyzeAgentWorkload($startDate, $endDate): array
    {
        $agentWorkload = DB::table('tickets')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('assigned_to')
            ->select('assigned_to', DB::raw('COUNT(*) as ticket_count'))
            ->groupBy('assigned_to')
            ->get();

        $averageWorkload = $agentWorkload->avg('ticket_count') ?? 0.0;
        $maxWorkload = $agentWorkload->max('ticket_count') ?? 0;
        $minWorkload = $agentWorkload->min('ticket_count') ?? 0;

        // Identify overloaded agents
        $overloadedAgents = $agentWorkload->where('ticket_count', '>', $averageWorkload * 1.5);

        return [
            'average_workload' => round($averageWorkload, 1),
            'max_workload' => $maxWorkload,
            'min_workload' => $minWorkload,
            'overloaded_agents' => $overloadedAgents->count(),
            'workload_distribution' => $this->getWorkloadDistribution($agentWorkload),
            'recommendations' => $this->getWorkloadRecommendations($overloadedAgents->count(), $averageWorkload)
        ];
    }

    /**
     * Analyze customer satisfaction (placeholder)
     */
    private function analyzeCustomerSatisfaction($startDate, $endDate): array
    {
        // This would typically integrate with customer feedback systems
        // For now, return placeholder data
        return [
            'current_satisfaction' => 85.2,
            'predicted_satisfaction' => 87.1,
            'trend' => 'improving',
            'key_factors' => [
                'Response time improvements',
                'Better ticket resolution',
                'Enhanced communication'
            ],
            'recommendations' => [
                'Continue current response time improvements',
                'Focus on first-call resolution',
                'Implement customer feedback surveys'
            ]
        ];
    }

    /**
     * Calculate trend from data points
     */
    private function calculateTrend(array $data): string
    {
        if (count($data) < 2) {
            return 'stable';
        }

        $values = array_values($data);
        $firstHalf = array_slice($values, 0, count($values) / 2);
        $secondHalf = array_slice($values, count($values) / 2);

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        if ($secondAvg > $firstAvg * 1.1) {
            return 'increasing';
        } elseif ($secondAvg < $firstAvg * 0.9) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }

    /**
     * Calculate trend percentage
     */
    private function calculateTrendPercentage(array $data): float
    {
        if (count($data) < 2) {
            return 0;
        }

        $values = array_values($data);
        $first = $values[0];
        $last = end($values);

        if ($first == 0) {
            return $last > 0 ? 100 : 0;
        }

        return round((($last - $first) / $first) * 100, 1);
    }

    /**
     * Predict ticket volume for next period
     */
    private function predictTicketVolume(array $data, int $days): array
    {
        if (empty($data)) {
            return [];
        }

        $values = array_values($data);
        $average = array_sum($values) / count($values);
        $trend = $this->calculateTrend($data);

        $predictions = [];
        for ($i = 1; $i <= $days; $i++) {
            $baseValue = $average;
            
            // Apply trend
            if ($trend === 'increasing') {
                $baseValue *= (1 + ($i * 0.05)); // 5% increase per day
            } elseif ($trend === 'decreasing') {
                $baseValue *= (1 - ($i * 0.03)); // 3% decrease per day
            }

            // Add some randomness
            $randomFactor = 0.8 + (mt_rand(0, 40) / 100); // 0.8 to 1.2
            $predictions[now()->addDays($i)->format('Y-m-d')] = round($baseValue * $randomFactor);
        }

        return $predictions;
    }

    /**
     * Predict SLA compliance
     */
    private function predictSlaCompliance(float $currentCompliance, int $breachedTickets, int $totalTickets): float
    {
        // Simple prediction based on current trend
        $breachRate = $totalTickets > 0 ? $breachedTickets / $totalTickets : 0;
        
        if ($breachRate > 0.1) { // More than 10% breach rate
            return max($currentCompliance - 2, 70); // Predict slight decrease
        } elseif ($breachRate < 0.05) { // Less than 5% breach rate
            return min($currentCompliance + 1, 100); // Predict slight increase
        } else {
            return $currentCompliance; // Predict stable
        }
    }

    /**
     * Predict resolution time
     */
    private function predictResolutionTime(array $resolutionTimes): float
    {
        if (empty($resolutionTimes)) {
            return 24; // Default 24 hours
        }

        $average = array_sum($resolutionTimes) / count($resolutionTimes);
        $trend = $this->calculateTrend($resolutionTimes);

        if ($trend === 'decreasing') {
            return $average * 0.95; // 5% improvement
        } elseif ($trend === 'increasing') {
            return $average * 1.05; // 5% increase
        } else {
            return $average; // Stable
        }
    }

    /**
     * Get SLA risk level
     */
    private function getSlaRiskLevel(float $complianceRate): string
    {
        if ($complianceRate >= 95) {
            return 'low';
        } elseif ($complianceRate >= 85) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    /**
     * Get SLA recommendations
     */
    private function getSlaRecommendations(float $complianceRate, int $breachedTickets): array
    {
        $recommendations = [];

        if ($complianceRate < 90) {
            $recommendations[] = 'Review and optimize ticket assignment processes';
            $recommendations[] = 'Implement automated SLA monitoring alerts';
        }

        if ($breachedTickets > 10) {
            $recommendations[] = 'Consider increasing agent capacity during peak hours';
            $recommendations[] = 'Implement priority-based ticket routing';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Continue current SLA management practices';
        }

        return $recommendations;
    }

    /**
     * Get resolution performance level
     */
    private function getResolutionPerformanceLevel(float $averageTime): string
    {
        if ($averageTime <= 4) {
            return 'excellent';
        } elseif ($averageTime <= 8) {
            return 'good';
        } elseif ($averageTime <= 24) {
            return 'average';
        } else {
            return 'needs_improvement';
        }
    }

    /**
     * Get workload distribution
     */
    private function getWorkloadDistribution($agentWorkload): array
    {
        $distribution = [
            'low' => 0,
            'medium' => 0,
            'high' => 0
        ];

        $average = $agentWorkload->avg('ticket_count') ?? 0.0;

        // If no average workload, all agents are in medium category
        if ($average == 0) {
            $distribution['medium'] = $agentWorkload->count();
            return $distribution;
        }

        foreach ($agentWorkload as $agent) {
            if ($agent->ticket_count < $average * 0.7) {
                $distribution['low']++;
            } elseif ($agent->ticket_count > $average * 1.3) {
                $distribution['high']++;
            } else {
                $distribution['medium']++;
            }
        }

        return $distribution;
    }

    /**
     * Get workload recommendations
     */
    private function getWorkloadRecommendations(int $overloadedCount, float $averageWorkload): array
    {
        $recommendations = [];

        if ($overloadedCount > 0) {
            $recommendations[] = 'Redistribute tickets from overloaded agents';
            $recommendations[] = 'Consider hiring additional agents';
        }

        if ($averageWorkload > 20) {
            $recommendations[] = 'Implement workload balancing algorithms';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Current workload distribution is optimal';
        }

        return $recommendations;
    }

    /**
     * Calculate resolution time trend
     */
    private function calculateResolutionTrend(array $resolutionTimes): string
    {
        if (count($resolutionTimes) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($resolutionTimes, 0, count($resolutionTimes) / 2);
        $secondHalf = array_slice($resolutionTimes, count($resolutionTimes) / 2);

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        if ($secondAvg < $firstAvg * 0.9) {
            return 'improving';
        } elseif ($secondAvg > $firstAvg * 1.1) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Format data for charts
     */
    private function formatChartData(array $historical, array $predictions): array
    {
        $chartData = [];

        // Historical data
        foreach ($historical as $date => $value) {
            $chartData[] = [
                'date' => $date,
                'value' => $value,
                'type' => 'historical'
            ];
        }

        // Predictions
        foreach ($predictions as $date => $value) {
            $chartData[] = [
                'date' => $date,
                'value' => $value,
                'type' => 'prediction'
            ];
        }

        return $chartData;
    }

    /**
     * Get default analytics when AI is not available
     */
    private function getDefaultAnalytics(): array
    {
        return [
            'ticket_volume' => [
                'current_period' => [
                    'total_tickets' => 0,
                    'average_daily' => 0,
                    'trend' => 'stable',
                    'trend_percentage' => 0
                ],
                'predictions' => [],
                'chart_data' => []
            ],
            'sla_compliance' => [
                'current_compliance' => 0,
                'breached_tickets' => 0,
                'total_tickets' => 0,
                'predicted_compliance' => 0,
                'risk_level' => 'unknown',
                'recommendations' => ['AI analytics not available']
            ],
            'resolution_times' => [
                'current_average' => 0,
                'current_median' => 0,
                'predicted_average' => 0,
                'trend' => 'stable',
                'performance_level' => 'unknown'
            ],
            'agent_workload' => [
                'average_workload' => 0,
                'max_workload' => 0,
                'min_workload' => 0,
                'overloaded_agents' => 0,
                'workload_distribution' => ['low' => 0, 'medium' => 0, 'high' => 0],
                'recommendations' => ['AI analytics not available']
            ],
            'customer_satisfaction' => [
                'current_satisfaction' => 0,
                'predicted_satisfaction' => 0,
                'trend' => 'unknown',
                'key_factors' => [],
                'recommendations' => ['AI analytics not available']
            ],
            'ai_enabled' => false,
            'generated_at' => now()
        ];
    }

    /**
     * Check if AI is enabled and available
     */
    public function isAvailable(): bool
    {
        return $this->enabled;
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->enabled,
            'available' => $this->isAvailable(),
            'prediction_horizon_days' => config('ai.analytics.prediction_horizon_days', 7),
            'update_frequency_hours' => config('ai.analytics.update_frequency_hours', 24),
            'last_check' => now()
        ];
    }
}
