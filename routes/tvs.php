<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TVS\{
    VehicleController,
    JobCardController,
    PartyController,
    WarrantyController,
    ReportingController
};

Route::prefix('api/tvs')->group(function () {
    
    // Vehicle Management
    Route::prefix('vehicles')->group(function () {
        Route::get('search', [VehicleController::class, 'search']);
        Route::post('provisional', [VehicleController::class, 'createProvisional']);
        Route::get('types', [VehicleController::class, 'getVehicleTypes']);
        Route::get('sale-types', [VehicleController::class, 'getSaleTypes']);
        Route::get('variants/{vehicleTypeId}', [VehicleController::class, 'getVariants']);
        Route::get('{vehicle}', [VehicleController::class, 'show']);
        Route::get('', [VehicleController::class, 'index']);
    });

    // Party/Customer Management
    Route::prefix('parties')->group(function () {
        Route::post('', [PartyController::class, 'store']);
        Route::get('types', [PartyController::class, 'getPartyTypes']);
        Route::get('{party}', [PartyController::class, 'show']);
        Route::put('{party}', [PartyController::class, 'update']);
        Route::get('', [PartyController::class, 'index']);
    });

    // Job Card Management - Core Workflow
    Route::prefix('job-cards')->group(function () {
        // Create job card (Reception)
        Route::post('', [JobCardController::class, 'store']);
        
        // Get job card details
        Route::get('{jobCard}', [JobCardController::class, 'show']);
        
        // List all job cards
        Route::get('', [JobCardController::class, 'index']);

        // Workshop - Add parts and labour
        Route::post('{jobCard}/parts', [JobCardController::class, 'addParts']);
        Route::post('{jobCard}/labour', [JobCardController::class, 'addLabour']);

        // Update checks
        Route::put('{jobCard}/standard-checks', [JobCardController::class, 'updateStandardChecks']);
        Route::put('{jobCard}/after-trial-checks', [JobCardController::class, 'updateAfterTrialChecks']);

        // Supervisor - Complete job card
        Route::post('{jobCard}/complete', [JobCardController::class, 'complete']);

        // Accounts - Process payment
        Route::post('{jobCard}/process-payment', [JobCardController::class, 'processPayment']);

        // Gate - Generate and release gate pass
        Route::post('{jobCard}/gate-pass', [JobCardController::class, 'generateGatePass']);
    });

    // Gate Pass Management
    Route::prefix('gate-passes')->group(function () {
        Route::post('{gatePass}/release', [JobCardController::class, 'releaseVehicle']);
    });

    // Warranty Management
    Route::prefix('warranties')->group(function () {
        Route::post('', [WarrantyController::class, 'store']);
        Route::get('{warranty}', [WarrantyController::class, 'show']);
        Route::get('', [WarrantyController::class, 'index']);
        Route::get('vehicle/{vehicleId}', [WarrantyController::class, 'getByVehicle']);
        Route::get('vehicle/{vehicleId}/claims', [WarrantyController::class, 'getClaims']);

        // Warranty Validation
        Route::post('validate-for-job/{jobCard}', [WarrantyController::class, 'validateForJobCard']);

        // Warranty Claims
        Route::post('claims', [WarrantyController::class, 'createClaim']);
        Route::post('claims/{claim}/approve', [WarrantyController::class, 'approveClaim']);
        Route::post('claims/{claim}/reject', [WarrantyController::class, 'rejectClaim']);
    });

    // Analytics & Reporting
    Route::prefix('reports')->group(function () {
        Route::get('branch-tat-metrics', [ReportingController::class, 'getBranchTATMetrics']);
        Route::get('branch-report', [ReportingController::class, 'getBranchReport']);
        Route::get('warranty-vs-customer-pay', [ReportingController::class, 'getWarrantyVsCustomerPayMix']);
        Route::get('free-service-conversion', [ReportingController::class, 'getFreeServiceConversionRate']);
        Route::get('vehicle-lifetime/{vehicleId}', [ReportingController::class, 'getVehicleLifetimeData']);
        Route::get('customer-lifetime/{partyId}', [ReportingController::class, 'getCustomerLifetimeValue']);
        Route::get('repeat-repairs/{vehicleId}', [ReportingController::class, 'getRepeatRepairs']);
        Route::get('daily-summary', [ReportingController::class, 'getDailyBranchSummary']);
    });

});
