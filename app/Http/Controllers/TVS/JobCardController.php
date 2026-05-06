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
            'odometer_working' => 'nullable|in:yes,no',   
            'warehouse' => 'nullable|string',             
            'fuel_level_in' => 'nullable|string',
            'customer_complaints' => 'nullable|string',
            'estimated_delivery_date' => 'required|date',
            'priority' => 'required|in:Normal,Urgent,Emergency',
            'free_service_coupon_id' => 'nullable|exists:free_service_coupons,id',
           'standard_checks' => 'required|array',
           'after_trial_checks' => 'required|array',
           'created_by_name' => 'nullable|string'
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
    try {
        return response()->json([
            'success' => true,
            'data' => $jobCard->load([
                'vehicle', 'serviceType', 'status', 'customerParty', 'billToParty',
                'standardChecks', 'afterTrialChecks', 'parts', 'labour', 'signatures',
                   'payment.paymentStatus',  
    'payment.paymentMode',     
      'gatePass.status',  
                 'parts.part',      
    'parts.warehouse',  
    'parts.chargeType',
     'labour.technician.user',  
                'labour.chargeType'   
            ])
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Add parts to job card (Workshop step)
     */
    public function addParts(JobCard $jobCard, Request $request)
    {
        $validated = $request->validate([
            'parts' => 'required|array',
            'parts.*.part_id' => 'required|string',
            'parts.*.part_name' => 'nullable|string',
            'parts.*.warehouse_id' => 'required|string',
            'parts.*.quantity' => 'required|integer|min:1',
            'parts.*.unit_price' => 'nullable|numeric',
            'parts.*.discount_amount' => 'nullable|numeric',
            'parts.*.charge_type_id' => 'nullable|exists:charge_types,id',
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
            'labour.*.operation_name' => 'required|string|max:255',
            'labour.*.technician_id' => 'required|exists:technicians,id',
            'labour.*.hours' => 'nullable|numeric',
            'labour.*.rate' => 'required|numeric',
            'labour.*.charge_type_id' => 'nullable|exists:charge_types,id',
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
//Save signatures
public function saveSignature(JobCard $jobCard, Request $request)
{
    $validated = $request->validate([
        'signature_type' => 'required|string',
        'signature_data' => 'nullable|string',
        'signed_by_name' => 'required|string',
        'signed_by_id'   => 'nullable|string',
    ]);

    $jobCard->signatures()->create([
        'signature_type' => $validated['signature_type'],
        'signature_data' => $validated['signature_data'] ?? null,
        'signed_by_name' => $validated['signed_by_name'],
        'signed_by_id'   => $validated['signed_by_id'] ?? null,
        'signed_date'    => now(),
    ]);

    return response()->json(['success' => true, 'message' => 'Signature saved']);
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

    // Filter by status name (blade sends "Pending", "In Progress" etc.)
    if ($request->filled('status')) {
        $query->whereHas('status', function($q) use ($request) {
            $q->where('name', $request->status);
        });
    }

    if ($request->filled('vehicle_id')) {
        $query->where('vehicle_id', $request->vehicle_id);
    }

    if ($request->filled('customer_party_id')) {
        $query->where('customer_party_id', $request->customer_party_id);
    }

    // Search by job card no, vehicle reg/chassis, or customer name
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('job_card_no', 'like', "%$search%")
              ->orWhereHas('vehicle', function($q2) use ($search) {
                  $q2->where('registration_no', 'like', "%$search%")
                     ->orWhere('chassis_no', 'like', "%$search%");
              })
              ->orWhereHas('customerParty', function($q2) use ($search) {
                  $q2->where('name', 'like', "%$search%");
              });
        });
    }

    $jobCards = $query->with([
                    'vehicle',
                  
                    'customerParty',  // blade uses customer_party?.name
                   
                    'payment',
                ])
                ->latest()
                ->paginate(15);

    return response()->json([
        'success' => true,
        'data'    => $jobCards,
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
