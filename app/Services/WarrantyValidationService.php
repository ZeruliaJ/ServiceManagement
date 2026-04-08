<?php

namespace App\Services;

use App\Models\TVS\Warranty;
use App\Models\TVS\WarrantyValidation;
use App\Models\TVS\WarrantyClaim;
use App\Models\TVS\JobCard;
use App\Models\TVS\Vehicle;

class WarrantyValidationService
{
    /**
     * Validate warranty for a job card
     */
    public function validateWarrantyForJobCard(JobCard $jobCard)
    {
        $vehicle = $jobCard->vehicle;
        $warranty = $vehicle->getActiveWarranty();

        if (!$warranty) {
            return [
                'is_valid' => false,
                'warranty_id' => null,
                'status' => 'Not Available',
                'reason' => 'No active warranty found'
            ];
        }

        // Check if warranty is expired
        if ($warranty->isExpired()) {
            return [
                'is_valid' => false,
                'warranty_id' => $warranty->id,
                'status' => 'Expired',
                'reason' => 'Warranty period has expired'
            ];
        }

        // Check kilometer limit if applicable
        if ($warranty->kilometers_limit && $jobCard->odometer_in) {
            $kmCovered = $jobCard->odometer_in - ($jobCard->vehicle->serviceHistory()->first()?->odometer_reading ?? 0);
            if ($kmCovered > $warranty->kilometers_limit) {
                return [
                    'is_valid' => false,
                    'warranty_id' => $warranty->id,
                    'status' => 'Expired',
                    'reason' => 'Kilometers limit exceeded'
                ];
            }
        }

        // Create warranty validation record
        $validation = WarrantyValidation::create([
            'warranty_id' => $warranty->id,
            'job_card_id' => $jobCard->id,
            'validation_date' => now(),
            'validation_status' => 'Valid',
            'claim_eligible' => true,
        ]);

        return [
            'is_valid' => true,
            'warranty_id' => $warranty->id,
            'validation_id' => $validation->id,
            'status' => 'Active',
            'warranty_end_date' => $warranty->warranty_end_date,
            'coverage_type' => $warranty->coverage_type
        ];
    }

    /**
     * Create warranty claim
     */
    public function createWarrantyClaim(JobCard $jobCard, array $claimData)
    {
        $warranty = $jobCard->vehicle->getActiveWarranty();

        if (!$warranty) {
            throw new \Exception('No active warranty for this vehicle');
        }

        // Check if claim eligible
        $validation = $jobCard->warrantyValidations()
            ->where('warranty_id', $warranty->id)
            ->where('claim_eligible', true)
            ->latest()
            ->first();

        if (!$validation) {
            throw new \Exception('Warranty claim not eligible for this job card');
        }

        return WarrantyClaim::create([
            'warranty_id' => $warranty->id,
            'job_card_id' => $jobCard->id,
            'claim_date' => now(),
            'claim_amount' => $claimData['claim_amount'],
            'claim_status' => 'Pending',
            'claim_reason' => $claimData['claim_reason'] ?? null,
        ]);
    }

    /**
     * Approve warranty claim
     */
    public function approveWarrantyClaim(WarrantyClaim $claim, array $approvalData)
    {
        return $claim->update([
            'claim_status' => 'Approved',
            'approval_date' => now(),
            'approved_by' => $approvalData['approved_by'] ?? null,
        ]);
    }

    /**
     * Reject warranty claim
     */
    public function rejectWarrantyClaim(WarrantyClaim $claim, string $rejectionReason)
    {
        return $claim->update([
            'claim_status' => 'Rejected',
            'rejection_reason' => $rejectionReason,
        ]);
    }

    /**
     * Get warranty coverage details
     */
    public function getWarrantyCoverageDetails(Warranty $warranty)
    {
        return [
            'warranty_id' => $warranty->id,
            'start_date' => $warranty->warranty_start_date,
            'end_date' => $warranty->warranty_end_date,
            'is_active' => $warranty->isActive(),
            'is_expired' => $warranty->isExpired(),
            'kilometers_limit' => $warranty->kilometers_limit,
            'coverage_type' => $warranty->coverage_type,
            'terms_conditions' => $warranty->terms_conditions,
            'total_claims' => $warranty->claims()->count(),
            'approved_claims_value' => $warranty->claims()
                ->where('claim_status', 'Approved')
                ->sum('claim_amount'),
        ];
    }

    /**
     * Check multiple warranty criteria at once
     */
    public function performComprehensiveWarrantyCheck(Vehicle $vehicle, JobCard $jobCard)
    {
        $checks = [
            'warranty_exists' => $vehicle->warranties()->exists(),
            'warranty_active' => (bool)$vehicle->getActiveWarranty(),
            'warranty_expired' => $vehicle->getActiveWarranty()?->isExpired() ?? false,
            'job_card_valid' => $jobCard->status->code !== 'Pending',
        ];

        $results = [
            'all_passed' => true,
            'checks' => $checks,
            'eligible_for_claim' => true,
            'issues' => []
        ];

        foreach ($checks as $check => $passed) {
            if (!$passed) {
                $results['all_passed'] = false;
                $results['eligible_for_claim'] = false;
                $results['issues'][] = $check;
            }
        }

        return $results;
    }
}
