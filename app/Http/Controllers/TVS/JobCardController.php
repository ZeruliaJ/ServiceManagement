<?php

namespace App\Http\Controllers\TVS;

use App\Http\Controllers\Controller;
use App\Models\TVS\JobCard;
use App\Models\TVS\GatePass;
use App\Services\JobCardService;
use App\Services\WarrantyValidationService;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    private $jobCardService;
    private $warrantyValidationService;

    public function __construct(
        JobCardService $jobCardService,
        WarrantyValidationService $warrantyValidationService
    ) {
        $this->jobCardService = $jobCardService;
        $this->warrantyValidationService = $warrantyValidationService;
    }

    /**
     * Create new job card (Reception step)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_type_id' => 'required|exists:service_types,id',
            'customer_party_id' => 'required|exists:parties,id',
            'bill_to_party_id' => 'required|exists:parties,id',
            'check_in_date' => 'nullable|date',
            'odometer_in' => 'nullable|integer',
            'fuel_level_in' => 'nullable|string',
            'customer_complaints' => 'nullable|string',
            'estimated_delivery_date' => 'required|date',
            'priority' => 'required|in:Normal,Urgent,Emergency',
            'free_service_coupon_id' => 'nullable|exists:free_service_coupons,id',
        ]);

        try {
            $jobCard = $this->jobCardService->createJobCard($validated);

            return response()->json([
                'success' => true,
                'message' => 'Job card created successfully',
                'data' => $jobCard->load('vehicle', 'serviceType', 'status'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating job card: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get job card details
     */
    public function show(JobCard $jobCard)
    {
        return response()->json([
            'success' => true,
            'data' => $jobCard->load([
                'vehicle', 'serviceType', 'status', 'customerParty', 'billToParty',
                'standardChecks', 'afterTrialChecks', 'parts', 'labour', 'signatures', 'payment'
            ])
        ]);
    }

    /**
     * Add parts to job card (Workshop step)
     */
    public function addParts(JobCard $jobCard, Request $request)
    {
        $validated = $request->validate([
            'parts' => 'required|array',
            'parts.*.part_id' => 'required|exists:parts,id',
            'parts.*.warehouse_id' => 'required|exists:warehouses,id',
            'parts.*.quantity' => 'required|integer|min:1',
            'parts.*.unit_price' => 'nullable|numeric',
            'parts.*.discount_amount' => 'nullable|numeric',
            'parts.*.charge_type_id' => 'required|exists:charge_types,id',
            'parts.*.reason' => 'required|string',
        ]);

        try {
            $addedParts = [];
            foreach ($validated['parts'] as $partData) {
                $addedParts[] = $this->jobCardService->addPartToJobCard($jobCard, $partData);
            }

            return response()->json([
                'success' => true,
                'message' => count($addedParts) . ' parts added to job card',
                'data' => $addedParts
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding parts: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Add labour to job card (Workshop step)
     */
    public function addLabour(JobCard $jobCard, Request $request)
    {
        $validated = $request->validate([
            'labour' => 'required|array',
            'labour.*.operation_id' => 'required|exists:labour_operations,id',
            'labour.*.technician_id' => 'required|exists:technicians,id',
            'labour.*.hours' => 'required|numeric|min:0.25',
            'labour.*.rate' => 'required|numeric',
            'labour.*.charge_type_id' => 'required|exists:charge_types,id',
            'labour.*.remarks' => 'nullable|string',
        ]);

        try {
            $addedLabour = [];
            foreach ($validated['labour'] as $labourData) {
                $addedLabour[] = $this->jobCardService->addLabourToJobCard($jobCard, $labourData);
            }

            return response()->json([
                'success' => true,
                'message' => count($addedLabour) . ' labour entries added',
                'data' => $addedLabour
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding labour: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Complete job card (Supervisor step)
     */
    public function complete(JobCard $jobCard, Request $request)
    {
        $validated = $request->validate([
            'odometer_out' => 'nullable|integer',
            'fuel_level_out' => 'nullable|string',
            'supervisor_notes' => 'nullable|string',
            'signed_by_name' => 'required|string',
            'signed_by_id' => 'nullable|string',
            'signature_data' => 'nullable|string',
        ]);

        try {
            $jobCard = $this->jobCardService->completeJobCard($jobCard, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Job card completed',
                'data' => $jobCard->load('signatures')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing job card: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process payment (Accounts step)
     */
    public function processPayment(JobCard $jobCard, Request $request)
    {
        $validated = $request->validate([
            'payment_status' => 'required|string',
            'payment_mode_id' => 'nullable|exists:payment_modes,id',
            'amount_paid' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
            'customer_name' => 'required|string',
            'paid_by' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'customer_signature_data' => 'nullable|string',
            'delivery_signature_data' => 'nullable|string',
        ]);

        try {
            $payment = $this->jobCardService->processPayment($jobCard, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $payment->load('jobCard', 'paymentStatus', 'paymentMode')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate gate pass (Gate step)
     */
    public function generateGatePass(JobCard $jobCard)
    {
        try {
            $gatePass = $this->jobCardService->generateGatePass($jobCard);

            return response()->json([
                'success' => true,
                'message' => 'Gate pass generated',
                'data' => $gatePass
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating gate pass: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Release vehicle at gate
     */
    public function releaseVehicle(GatePass $gatePass, Request $request)
    {
        $validated = $request->validate([
            'used_by' => 'nullable|string',
        ]);

        try {
            $gatePass = $this->jobCardService->releaseVehicleAtGate($gatePass, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle released',
                'data' => $gatePass
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error releasing vehicle: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * List all job cards with filters
     */
    public function index(Request $request)
    {
        $query = JobCard::query();

        if ($request->has('status')) {
            $query->whereHas('status', function($q) {
                $q->where('code', $request->status);
            });
        }

        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('customer_party_id')) {
            $query->where('customer_party_id', $request->customer_party_id);
        }

        $jobCards = $query->with(['vehicle', 'status', 'customerParty', 'payment'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $jobCards
        ]);
    }

    /**
     * Update standard checks
     */
    public function updateStandardChecks(JobCard $jobCard, Request $request)
    {
        $validated = $request->validate([
            'checks' => 'required|array',
            'checks.*.id' => 'required|exists:job_card_standard_checks',
            'checks.*.status' => 'required|in:OK,Not OK',
            'checks.*.remarks' => 'nullable|string',
        ]);

        foreach ($validated['checks'] as $check) {
            $jobCard->standardChecks()->find($check['id'])->update([
                'status' => $check['status'],
                'remarks' => $check['remarks'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Standard checks updated',
            'data' => $jobCard->standardChecks
        ]);
    }

    /**
     * Update after trial checks
     */
    public function updateAfterTrialChecks(JobCard $jobCard, Request $request)
    {
        $validated = $request->validate([
            'checks' => 'required|array',
            'checks.*.id' => 'required|exists:job_card_after_trial_checks',
            'checks.*.status' => 'required|in:OK,Not OK',
            'checks.*.remarks' => 'nullable|string',
        ]);

        foreach ($validated['checks'] as $check) {
            $jobCard->afterTrialChecks()->find($check['id'])->update([
                'status' => $check['status'],
                'remarks' => $check['remarks'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'After trial checks updated',
            'data' => $jobCard->afterTrialChecks
        ]);
    }
}
