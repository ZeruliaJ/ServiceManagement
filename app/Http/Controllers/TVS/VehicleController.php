<?php

namespace App\Http\Controllers\TVS;

use App\Http\Controllers\Controller;
use App\Models\TVS\Vehicle;
use App\Models\TVS\VehicleVariant;
use App\Models\TVS\VehicleType;
use App\Models\TVS\SaleType;
use App\Services\OwnershipValidationService;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    private $ownershipValidationService;

    public function __construct(OwnershipValidationService $ownershipValidationService)
    {
        $this->ownershipValidationService = $ownershipValidationService;
    }

    /**
     * Search vehicle by registration, chassis or engine number
     */
    public function search(Request $request)
    {
        $vehicle = $this->ownershipValidationService->searchVehicle(
            $request->input('registration_no'),
            $request->input('chassis_no'),
            $request->input('engine_no')
        );

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vehicle found',
            'data' => [
                'id' => $vehicle->id,
                'registration_no' => $vehicle->registration_no,
                'chassis_no' => $vehicle->chassis_no,
                'engine_no' => $vehicle->engine_no,
                'model' => $vehicle->variant?->model_name,
                'variant' => $vehicle->variant?->variant_name,
                'color' => $vehicle->color,
                'vehicle_type' => $vehicle->vehicleType?->name,
                'sale_type' => $vehicle->saleType?->name,
                'sale_date' => $vehicle->sale_date,
                'current_owner' => $vehicle->getCurrentOwner()?->party?->name,
                'warranty_status' => $vehicle->getActiveWarranty() ? 'Active' : 'Inactive',
                'last_service_date' => $vehicle->serviceHistory()->latest()->first()?->service_date,
                'is_provisional' => $vehicle->is_provisional,
                'is_validated' => $vehicle->is_validated,
            ]
        ]);
    }

    /**
     * Create provisional vehicle for unknown origins
     */
    public function createProvisional(Request $request)
    {
        $validated = $request->validate([
            'registration_no' => 'required|unique:vehicles',
            'chassis_no' => 'required|unique:vehicles',
            'engine_no' => 'required|unique:vehicles',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
        ]);

        $vehicle = $this->ownershipValidationService->createProvisionalVehicle($validated);

        return response()->json([
            'success' => true,
            'message' => 'Provisional vehicle created. Please submit for central validation',
            'data' => $vehicle
        ], 201);
    }

    /**
     * Get vehicle details
     */
    public function show(Vehicle $vehicle)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $vehicle->id,
                'registration_no' => $vehicle->registration_no,
                'chassis_no' => $vehicle->chassis_no,
                'engine_no' => $vehicle->engine_no,
                'model' => $vehicle->variant?->model_name,
                'variant' => $vehicle->variant?->variant_name,
                'color' => $vehicle->color,
                'vehicle_type' => $vehicle->vehicleType?->name,
                'sale_type' => $vehicle->saleType?->name,
                'current_owner' => $vehicle->getCurrentOwner()?->party,
                'warranties' => $vehicle->warranties,
                'service_history' => $vehicle->serviceHistory,
                'lifetime_data' => $vehicle->lifetimeData()->latest()->first(),
            ]
        ]);
    }

    /**
     * List all vehicles
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($request->has('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        if ($request->has('sale_type_id')) {
            $query->where('sale_type_id', $request->sale_type_id);
        }

        if ($request->has('is_provisional')) {
            $query->where('is_provisional', $request->is_provisional);
        }

        $vehicles = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    /**
     * Get vehicle types
     */
    public function getVehicleTypes()
    {
        return response()->json([
            'success' => true,
            'data' => VehicleType::all()
        ]);
    }

    /**
     * Get sale types
     */
    public function getSaleTypes()
    {
        return response()->json([
            'success' => true,
            'data' => SaleType::all()
        ]);
    }

    /**
     * Get vehicle variants by type
     */
    public function getVariants($vehicleTypeId)
    {
        $variants = VehicleVariant::where('vehicle_type_id', $vehicleTypeId)->get();

        return response()->json([
            'success' => true,
            'data' => $variants
        ]);
    }
}
