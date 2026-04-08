<?php

namespace App\Services;

use App\Models\TVS\JobCard;
use App\Models\TVS\JobCardMetric;
use App\Models\TVS\DailyBranchSummary;
use App\Models\TVS\VehicleLifetimeData;
use App\Models\TVS\CustomerLifetimeValue;
use App\Models\TVS\RepeatRepairAnalysis;
use App\Models\TVS\Branch;
use App\Models\TVS\Vehicle;
use App\Models\TVS\Party;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingService
{
    /**
     * Calculate and store job card metrics (TAT - Turn Around Time)
     */
    public function calculateJobCardMetrics(JobCard $jobCard, string $branchCode)
    {
        $branch = Branch::where('branch_code', $branchCode)->first();

        $checkInDate = $jobCard->check_in_date;
        $jobOpenDate = $jobCard->status->code === 'PENDING' ? null : $jobCard->updated_at;
        $completionDate = $jobCard->status->code === 'COMPLETED' ? $jobCard->updated_at : null;
        $deliveryDate = $jobCard->status->code === 'DELIVERED' ? $jobCard->actual_delivery_date : null;
        $gateOutDate = $jobCard->gatePass()->whereHas('status', function($q) {
            $q->where('code', 'USED');
        })->first()?->gate_pass_used_date;

        // Calculate TAT in hours
        $tatCheckInToOpen = $jobOpenDate ? $checkInDate->diffInHours($jobOpenDate) : null;
        $tatOpenToCompletion = $completionDate && $jobOpenDate ? $jobOpenDate->diffInHours($completionDate) : null;
        $tatCompletionToDelivery = $deliveryDate && $completionDate ? $completionDate->diffInHours($deliveryDate) : null;
        $tatDeliveryToGateOut = $gateOutDate && $deliveryDate ? $deliveryDate->diffInHours($gateOutDate) : null;
        $tatTotal = $gateOutDate ? $checkInDate->diffInHours($gateOutDate) : null;

        return JobCardMetric::updateOrCreate(
            ['job_card_id' => $jobCard->id],
            [
                'branch_id' => $branch->id,
                'check_in_date' => $checkInDate,
                'job_open_date' => $jobOpenDate,
                'completion_date' => $completionDate,
                'delivery_date' => $deliveryDate,
                'gate_out_date' => $gateOutDate,
                'tat_check_in_to_open' => $tatCheckInToOpen,
                'tat_open_to_completion' => $tatOpenToCompletion,
                'tat_completion_to_delivery' => $tatCompletionToDelivery,
                'tat_delivery_to_gate_out' => $tatDeliveryToGateOut,
                'tat_total' => $tatTotal,
            ]
        );
    }

    /**
     * Get TAT metrics for a branch
     */
    public function getBranchTATMetrics(string $branchCode, Carbon $startDate = null, Carbon $endDate = null)
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        $branch = Branch::where('branch_code', $branchCode)->first();

        $metrics = JobCardMetric::where('branch_id', $branch->id)
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->get();

        return [
            'branch' => $branch->branch_name,
            'period' => "$startDate to $endDate",
            'total_job_cards' => $metrics->count(),
            'average_tat_hours' => $metrics->avg('tat_total'),
            'average_check_in_to_open' => $metrics->avg('tat_check_in_to_open'),
            'average_open_to_completion' => $metrics->avg('tat_open_to_completion'),
            'average_completion_to_delivery' => $metrics->avg('tat_completion_to_delivery'),
            'average_delivery_to_gate_out' => $metrics->avg('tat_delivery_to_gate_out'),
            'min_tat' => $metrics->min('tat_total'),
            'max_tat' => $metrics->max('tat_total'),
        ];
    }

    /**
     * Generate daily branch summary
     */
    public function generateDailyBranchSummary(string $branchCode, Carbon $date = null)
    {
        $date = $date ?? now();
        $branch = Branch::where('branch_code', $branchCode)->first();

        $jobCards = JobCard::whereHas('vehicle')
            ->whereBetween('check_in_date', [$date->startOfDay(), $date->endOfDay()])
            ->get();

        $summaryData = [
            'pending_count' => $jobCards->whereIn('job_card_status_id', 
                \App\Models\TVS\JobCardStatus::whereIn('code', ['PENDING', 'PENDING_PARTS'])->pluck('id'))->count(),
            'in_progress_count' => $jobCards->whereIn('job_card_status_id',
                \App\Models\TVS\JobCardStatus::where('code', 'IN_PROGRESS')->pluck('id'))->count(),
            'completed_count' => $jobCards->whereIn('job_card_status_id',
                \App\Models\TVS\JobCardStatus::where('code', 'COMPLETED')->pluck('id'))->count(),
            'delivered_count' => $jobCards->whereIn('job_card_status_id',
                \App\Models\TVS\JobCardStatus::where('code', 'DELIVERED')->pluck('id'))->count(),
        ];

        $dailyRevenue = $jobCards->sum(function($jc) {
            return $jc->payment?->grand_total ?? 0;
        });

        $warrantyClaims = $jobCards->sum(function($jc) {
            return $jc->warrantyClaims()
                ->where('claim_status', 'Approved')
                ->sum('claim_amount');
        });

        $freeServices = $jobCards->filter(function($jc) {
            return $jc->serviceType->code === 'FREE';
        })->count();

        return DailyBranchSummary::updateOrCreate(
            ['branch_id' => $branch->id, 'summary_date' => $date->startOfDay()],
            [
                'pending_count' => $summaryData['pending_count'],
                'in_progress_count' => $summaryData['in_progress_count'],
                'completed_count' => $summaryData['completed_count'],
                'delivered_count' => $summaryData['delivered_count'],
                'daily_revenue' => $dailyRevenue,
                'warranty_claims_value' => $warrantyClaims,
                'free_services_count' => $freeServices,
            ]
        );
    }

    /**
     * Calculate vehicle lifetime data
     */
    public function calculateVehicleLifetimeData(Vehicle $vehicle)
    {
        $jobCards = $vehicle->jobCards()->where('job_card_status_id',
            \App\Models\TVS\JobCardStatus::where('code', 'DELIVERED')->first()->id
        )->get();

        $totalCost = 0;
        $totalPartsCost = 0;
        $totalLabourCost = 0;
        $totalWarrantyClaims = 0;

        foreach ($jobCards as $jc) {
            if ($jc->payment) {
                $totalCost += $jc->payment->grand_total;
                $totalPartsCost += $jc->payment->parts_total;
                $totalLabourCost += $jc->payment->labour_total;
            }

            $totalWarrantyClaims += $jc->warrantyClaims()
                ->where('claim_status', 'Approved')
                ->sum('claim_amount');
        }

        return VehicleLifetimeData::updateOrCreate(
            ['vehicle_id' => $vehicle->id],
            [
                'total_service_visits' => $jobCards->count(),
                'total_service_cost' => $totalCost,
                'total_parts_cost' => $totalPartsCost,
                'total_labour_cost' => $totalLabourCost,
                'total_warranty_claims' => $totalWarrantyClaims,
                'first_service_date' => $jobCards->min('check_in_date'),
                'last_service_date' => $jobCards->max('check_in_date'),
                'average_service_interval_days' => $jobCards->count() > 1 
                    ? $jobCards->min('check_in_date')->diffInDays($jobCards->max('check_in_date')) / ($jobCards->count() - 1)
                    : 0,
            ]
        );
    }

    /**
     * Calculate customer lifetime value
     */
    public function calculateCustomerLifetimeValue(Party $customer)
    {
        $vehicles = $customer->ownerMappings()->pluck('vehicle_id');
        $jobCards = JobCard::whereIn('vehicle_id', $vehicles)
            ->where('job_card_status_id',
                \App\Models\TVS\JobCardStatus::where('code', 'DELIVERED')->first()->id
            )
            ->get();

        $totalValue = 0;
        foreach ($jobCards as $jc) {
            if ($jc->payment) {
                $totalValue += $jc->payment->grand_total;
            }
        }

        $repeatVisits = $jobCards->groupBy('vehicle_id')
            ->filter(function($cards) {
                return $cards->count() > 1;
            })->count();

        return CustomerLifetimeValue::updateOrCreate(
            ['party_id' => $customer->id],
            [
                'total_vehicles_serviced' => $vehicles->count(),
                'lifetime_value' => $totalValue,
                'total_job_cards' => $jobCards->count(),
                'first_service_date' => $jobCards->min('check_in_date'),
                'last_service_date' => $jobCards->max('check_in_date'),
                'repeat_visits' => $repeatVisits,
                'average_visit_value' => $jobCards->count() > 0 ? $totalValue / $jobCards->count() : 0,
            ]
        );
    }

    /**
     * Analyze repeat repairs for a vehicle
     */
    public function analyzeRepeatRepairs(Vehicle $vehicle)
    {
        $jobCards = $vehicle->jobCards()
            ->where('job_card_status_id',
                \App\Models\TVS\JobCardStatus::where('code', 'DELIVERED')->first()->id
            )
            ->with('parts')
            ->get();

        $defectMap = [];
        
        foreach ($jobCards as $jc) {
            foreach ($jc->parts as $part) {
                $defectType = $part->reason;
                if (!isset($defectMap[$defectType])) {
                    $defectMap[$defectType] = [
                        'count' => 0,
                        'first_date' => $jc->check_in_date,
                        'last_date' => $jc->check_in_date,
                        'total_cost' => 0,
                    ];
                }
                $defectMap[$defectType]['count']++;
                $defectMap[$defectType]['last_date'] = $jc->check_in_date;
                $defectMap[$defectType]['total_cost'] += $part->amount;
            }
        }

        $analyses = [];
        foreach ($defectMap as $defectType => $data) {
            if ($data['count'] > 1) { // Only store repeat repairs
                $analyses[] = RepeatRepairAnalysis::updateOrCreate(
                    ['vehicle_id' => $vehicle->id, 'defect_type' => $defectType],
                    [
                        'repeat_count' => $data['count'],
                        'first_occurrence_date' => $data['first_date'],
                        'last_occurrence_date' => $data['last_date'],
                        'total_cost' => $data['total_cost'],
                    ]
                );
            }
        }

        return $analyses;
    }

    /**
     * Get comprehensive branch report
     */
    public function getBranchReport(string $branchCode, Carbon $startDate = null, Carbon $endDate = null)
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();
        $branch = Branch::where('branch_code', $branchCode)->first();

        $dailySummaries = DailyBranchSummary::where('branch_id', $branch->id)
            ->whereBetween('summary_date', [$startDate, $endDate])
            ->get();

        return [
            'branch_name' => $branch->branch_name,
            'region' => $branch->region,
            'zone' => $branch->zone,
            'period' => "$startDate to $endDate",
            'summary' => [
                'total_job_cards' => $dailySummaries->sum('pending_count') + 
                                    $dailySummaries->sum('in_progress_count') +
                                    $dailySummaries->sum('completed_count') +
                                    $dailySummaries->sum('delivered_count'),
                'total_revenue' => $dailySummaries->sum('daily_revenue'),
                'total_warranty_claims' => $dailySummaries->sum('warranty_claims_value'),
                'total_free_services' => $dailySummaries->sum('free_services_count'),
                'average_daily_job_cards' => $dailySummaries->avg('delivered_count'),
            ],
            'daily_summaries' => $dailySummaries
        ];
    }

    /**
     * Get warranty vs customer pay analysis
     */
    public function getWarrantyVsCustomerPayMix(string $branchCode, Carbon $startDate = null, Carbon $endDate = null)
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        // Get jobs by charge type
        $warrantyJobs = JobCard::join('job_card_parts', 'job_cards.id', '=', 'job_card_parts.job_card_id')
            ->join('charge_types', 'job_card_parts.charge_type_id', '=', 'charge_types.id')
            ->where('charge_types.code', 'Warranty')
            ->whereBetween('job_cards.check_in_date', [$startDate, $endDate])
            ->sum('job_card_parts.amount');

        $customerPayJobs = JobCard::join('job_card_parts', 'job_cards.id', '=', 'job_card_parts.job_card_id')
            ->join('charge_types', 'job_card_parts.charge_type_id', '=', 'charge_types.id')
            ->where('charge_types.code', 'Chargeable')
            ->whereBetween('job_cards.check_in_date', [$startDate, $endDate])
            ->sum('job_card_parts.amount');

        $total = $warrantyJobs + $customerPayJobs;

        return [
            'warranty_value' => $warrantyJobs,
            'warranty_percentage' => $total > 0 ? ($warrantyJobs / $total * 100) : 0,
            'customer_pay_value' => $customerPayJobs,
            'customer_pay_percentage' => $total > 0 ? ($customerPayJobs / $total * 100) : 0,
            'total_value' => $total,
        ];
    }
}
