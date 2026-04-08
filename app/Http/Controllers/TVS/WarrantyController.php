<?php

namespace App\Http\Controllers\TVS;

use App\Http\Controllers\Controller;
use App\Models\TVS\Warranty;
use App\Models\TVS\WarrantyClaim;
use App\Models\TVS\JobCard;
use App\Services\WarrantyValidationService;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    private $warrantyValidationService;

    public function __construct(WarrantyValidationService $warrantyValidationService)
    {
        $this->warrantyValidationService = $warrantyValidationService;
    }

    /**
     * Create warranty
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'warranty_start_date' => 'required|date',
            'warranty_end_date' => 'required|date|after:warranty_start_date',
            'warranty_status_id' => 'required|exists:warranty_statuses,id',
            'kilometers_limit' => 'nullable|integer',
            'coverage_type' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'source_system' => 'nullable|string',
        ]);

        $warranty = Warranty::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Warranty created',
            'data' => $warranty
        ], 201);
    }

    /**
     * Get warranty details
     */
    public function show(Warranty $warranty)
    {
        $coverageDetails = $this->warrantyValidationService->getWarrantyCoverageDetails($warranty);

        return response()->json([
            'success' => true,
            'data' => array_merge($warranty->toArray(), $coverageDetails)
        ]);
    }

    /**
     * Validate warranty for job card
     */
    public function validateForJobCard(JobCard $jobCard)
    {
        $result = $this->warrantyValidationService->validateWarrantyForJobCard($jobCard);

        return response()->json([
            'success' => $result['is_valid'],
            'data' => $result
        ], $result['is_valid'] ? 200 : 400);
    }

    /**
     * Create warranty claim
     */
    public function createClaim(Request $request)
    {
        $validated = $request->validate([
            'job_card_id' => 'required|exists:job_cards,id',
            'claim_amount' => 'required|numeric|min:0.01',
            'claim_reason' => 'nullable|string',
        ]);

        try {
            $jobCard = JobCard::findOrFail($validated['job_card_id']);
            $claim = $this->warrantyValidationService->createWarrantyClaim($jobCard, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Warranty claim created',
                'data' => $claim
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Approve warranty claim
     */
    public function approveClaim(WarrantyClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'approved_by' => 'nullable|string',
        ]);

        $this->warrantyValidationService->approveWarrantyClaim($claim, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Warranty claim approved',
            'data' => $claim->fresh()
        ]);
    }

    /**
     * Reject warranty claim
     */
    public function rejectClaim(WarrantyClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $this->warrantyValidationService->rejectWarrantyClaim($claim, $validated['rejection_reason']);

        return response()->json([
            'success' => true,
            'message' => 'Warranty claim rejected',
            'data' => $claim->fresh()
        ]);
    }

    /**
     * Get all warranties for vehicle
     */
    public function getByVehicle($vehicleId)
    {
        $warranties = Warranty::where('vehicle_id', $vehicleId)
            ->with('warrantyStatus')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $warranties
        ]);
    }

    /**
     * List all warranties with filters
     */
    public function index(Request $request)
    {
        $query = Warranty::query();

        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('warranty_status_id')) {
            $query->where('warranty_status_id', $request->warranty_status_id);
        }

        $warranties = $query->with(['vehicle', 'warrantyStatus'])->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $warranties
        ]);
    }

    /**
     * Get warranty claims for vehicle
     */
    public function getClaims($vehicleId)
    {
        $claims = WarrantyClaim::whereHas('warranty', function($q) {
            $q->where('vehicle_id', $vehicleId);
        })->with(['warranty', 'jobCard'])->get();

        return response()->json([
            'success' => true,
            'data' => $claims
        ]);
    }
}
