<?php

namespace App\Http\Controllers\TVS;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportingController extends Controller
{
    private $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Get TAT metrics for a branch
     */
    public function getBranchTATMetrics(Request $request)
    {
        $validated = $request->validate([
            'branch_code' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $startDate = $validated['start_date'] ? Carbon::parse($validated['start_date']) : null;
            $endDate = $validated['end_date'] ? Carbon::parse($validated['end_date']) : null;

            $metrics = $this->reportingService->getBranchTATMetrics(
                $validated['branch_code'],
                $startDate,
                $endDate
            );

            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get branch report
     */
    public function getBranchReport(Request $request)
    {
        $validated = $request->validate([
            'branch_code' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $startDate = $validated['start_date'] ? Carbon::parse($validated['start_date']) : null;
            $endDate = $validated['end_date'] ? Carbon::parse($validated['end_date']) : null;

            $report = $this->reportingService->getBranchReport(
                $validated['branch_code'],
                $startDate,
                $endDate
            );

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get warranty vs customer pay mix
     */
    public function getWarrantyVsCustomerPayMix(Request $request)
    {
        $validated = $request->validate([
            'branch_code' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $startDate = $validated['start_date'] ? Carbon::parse($validated['start_date']) : null;
            $endDate = $validated['end_date'] ? Carbon::parse($validated['end_date']) : null;

            $mix = $this->reportingService->getWarrantyVsCustomerPayMix(
                $validated['branch_code'] ?? 'ALL',
                $startDate,
                $endDate
            );

            return response()->json([
                'success' => true,
                'data' => $mix
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get vehicle lifetime data
     */
    public function getVehicleLifetimeData($vehicleId)
    {
        try {
            $vehicle = \App\Models\TVS\Vehicle::findOrFail($vehicleId);
            $lifetimeData = $this->reportingService->calculateVehicleLifetimeData($vehicle);

            return response()->json([
                'success' => true,
                'data' => $lifetimeData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get customer lifetime value
     */
    public function getCustomerLifetimeValue($partyId)
    {
        try {
            $party = \App\Models\TVS\Party::findOrFail($partyId);
            $clv = $this->reportingService->calculateCustomerLifetimeValue($party);

            return response()->json([
                'success' => true,
                'data' => $clv
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get repeat repair analysis
     */
    public function getRepeatRepairs($vehicleId)
    {
        try {
            $vehicle = \App\Models\TVS\Vehicle::findOrFail($vehicleId);
            $analyses = $this->reportingService->analyzeRepeatRepairs($vehicle);

            return response()->json([
                'success' => true,
                'data' => $analyses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get daily branch summary
     */
    public function getDailyBranchSummary(Request $request)
    {
        $validated = $request->validate([
            'branch_code' => 'required|string',
            'date' => 'nullable|date',
        ]);

        try {
            $date = $validated['date'] ? Carbon::parse($validated['date']) : null;

            $summary = $this->reportingService->generateDailyBranchSummary(
                $validated['branch_code'],
                $date
            );

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get free service conversion rate
     */
    public function getFreeServiceConversionRate(Request $request)
    {
        $validated = $request->validate([
            'branch_code' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $startDate = $validated['start_date'] ? Carbon::parse($validated['start_date']) : now()->subMonth();
            $endDate = $validated['end_date'] ? Carbon::parse($validated['end_date']) : now();

            $query = \App\Models\TVS\JobCard::whereBetween('check_in_date', [$startDate, $endDate]);

            $freeServices = (clone $query)->whereHas('serviceType', function($q) {
                $q->where('code', 'FREE');
            })->count();

            $paidServices = (clone $query)->whereHas('serviceType', function($q) {
                $q->where('code', 'PAID');
            })->count();

            $total = $query->count();
            $conversionRate = $total > 0 ? ($freeServices / $total * 100) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'free_services' => $freeServices,
                    'paid_services' => $paidServices,
                    'total_services' => $total,
                    'free_to_paid_conversion_rate' => round($conversionRate, 2) . '%',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
