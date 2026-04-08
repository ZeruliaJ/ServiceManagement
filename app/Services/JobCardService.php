<?php

namespace App\Services;

use App\Models\TVS\JobCard;
use App\Models\TVS\JobCardStatus;
use App\Models\TVS\JobCardPart;
use App\Models\TVS\JobCardLabour;
use App\Models\TVS\JobCardPayment;
use App\Models\TVS\GatePass;
use App\Models\TVS\GatePassStatus;
use App\Models\TVS\PaymentStatus;
use App\Models\TVS\Vehicle;
use App\Models\TVS\Part;
use App\Models\TVS\PartReservation;
use App\Models\TVS\PartyType;
use Illuminate\Support\Facades\DB;

class JobCardService
{
    /**
     * Step 1: Reception - Create Job Card
     */
    public function createJobCard(array $jobCardData)
    {
        return DB::transaction(function () use ($jobCardData) {
            $vehicle = Vehicle::with('variant')->findOrFail($jobCardData['vehicle_id']);

            // Generate unique job card number
            $jobCardNo = $this->generateJobCardNumber();

            $jobCard = JobCard::create([
                'job_card_no' => $jobCardNo,
                'vehicle_id' => $jobCardData['vehicle_id'],
                'service_type_id' => $jobCardData['service_type_id'],
                'job_card_status_id' => JobCardStatus::where('code', 'PENDING')->first()->id,
                'customer_party_id' => $jobCardData['customer_party_id'],
                'bill_to_party_id' => $jobCardData['bill_to_party_id'],
                'free_service_coupon_id' => $jobCardData['free_service_coupon_id'] ?? null,
                'check_in_date' => $jobCardData['check_in_date'] ?? now(),
                'odometer_in' => $jobCardData['odometer_in'] ?? null,
                'fuel_level_in' => $jobCardData['fuel_level_in'] ?? null,
                'customer_complaints' => $jobCardData['customer_complaints'] ?? null,
                'estimated_delivery_date' => $jobCardData['estimated_delivery_date'] ?? now()->addDays(1),
                'priority' => $jobCardData['priority'] ?? 'Normal',
            ]);

            // Add standard checks based on vehicle type
            $this->addStandardChecks($jobCard, $vehicle);

            return $jobCard;
        });
    }

    /**
     * Step 2: Technician - Add parts and labour
     */
    public function addPartToJobCard(JobCard $jobCard, array $partData)
    {
        return DB::transaction(function () use ($jobCard, $partData) {
            $part = Part::findOrFail($partData['part_id']);
            $warehouse = $partData['warehouse_id'];
            $quantity = $partData['quantity'];

            // Check stock availability
            $stockAvailable = $part->getAvailableQuantity($warehouse);

            if ($stockAvailable < $quantity) {
                // Auto-reserve what's available and shift to pending parts queue
                $reservedQty = $stockAvailable;
                $jobCard->update(['job_card_status_id' => JobCardStatus::where('code', 'PENDING_PARTS')->first()->id]);
            } else {
                $reservedQty = $quantity;
            }

            // Create part reservation
            PartReservation::create([
                'part_id' => $part->id,
                'warehouse_id' => $warehouse,
                'job_card_id' => $jobCard->id,
                'quantity_reserved' => $reservedQty,
                'reservation_status' => 'Reserved',
                'reservation_date' => now(),
                'expected_fulfillment_date' => $partData['expected_fulfillment_date'] ?? now(),
            ]);

            // Add to job card parts
            return JobCardPart::create([
                'job_card_id' => $jobCard->id,
                'part_id' => $part->id,
                'warehouse_id' => $warehouse,
                'quantity' => $quantity,
                'unit_price' => $partData['unit_price'] ?? $part->unit_price,
                'discount_amount' => $partData['discount_amount'] ?? 0,
                'amount' => ($partData['unit_price'] ?? $part->unit_price) * $quantity - ($partData['discount_amount'] ?? 0),
                'charge_type_id' => $partData['charge_type_id'],
                'reason' => $partData['reason'] ?? 'Replacement',
            ]);
        });
    }

    /**
     * Add labour to job card
     */
    public function addLabourToJobCard(JobCard $jobCard, array $labourData)
    {
        return JobCardLabour::create([
            'job_card_id' => $jobCard->id,
            'operation_id' => $labourData['operation_id'],
            'technician_id' => $labourData['technician_id'],
            'hours' => $labourData['hours'],
            'rate' => $labourData['rate'],
            'amount' => $labourData['hours'] * $labourData['rate'],
            'charge_type_id' => $labourData['charge_type_id'],
            'remarks' => $labourData['remarks'] ?? null,
        ]);
    }

    /**
     * Step 3: Supervisor - Complete job card
     */
    public function completeJobCard(JobCard $jobCard, array $supervisorData)
    {
        return DB::transaction(function () use ($jobCard, $supervisorData) {
            $jobCard->update([
                'job_card_status_id' => JobCardStatus::where('code', 'COMPLETED')->first()->id,
                'odometer_out' => $supervisorData['odometer_out'] ?? null,
                'fuel_level_out' => $supervisorData['fuel_level_out'] ?? null,
                'supervisor_notes' => $supervisorData['supervisor_notes'] ?? null,
            ]);

            // Add supervisor signature
            $jobCard->signatures()->create([
                'signature_type' => 'Supervisor',
                'signature_data' => $supervisorData['signature_data'] ?? null,
                'signed_by_name' => $supervisorData['signed_by_name'],
                'signed_by_id' => $supervisorData['signed_by_id'] ?? null,
                'signed_date' => now(),
            ]);

            return $jobCard;
        });
    }

    /**
     * Step 4: Accounts - Process Payment
     */
    public function processPayment(JobCard $jobCard, array $paymentData)
    {
        return DB::transaction(function () use ($jobCard, $paymentData) {
            // Calculate totals
            $partsTotal = $jobCard->parts()->sum(DB::raw('amount'));
            $labourTotal = $jobCard->labour()->sum(DB::raw('amount'));
            $subtotal = $partsTotal + $labourTotal;
            $taxAmount = $subtotal * 0.18; // Assuming 18% VAT
            $grandTotal = $subtotal + $taxAmount;
            $discountAmount = $paymentData['discount_amount'] ?? 0;
            $finalTotal = $grandTotal - $discountAmount;

            // Create payment record
            $payment = JobCardPayment::create([
                'job_card_id' => $jobCard->id,
                'parts_total' => $partsTotal,
                'labour_total' => $labourTotal,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'grand_total' => $finalTotal,
                'payment_status_id' => PaymentStatus::where('code', $paymentData['payment_status'])->first()->id,
                'payment_mode_id' => $paymentData['payment_mode_id'] ?? null,
                'amount_paid' => $paymentData['amount_paid'] ?? 0,
                'balance_amount' => $finalTotal - ($paymentData['amount_paid'] ?? 0),
                'invoice_no' => $this->generateInvoiceNumber(),
                'receipt_no' => $this->generateReceiptNumber(),
                'payment_date' => $paymentData['payment_date'] ?? now(),
                'paid_by' => $paymentData['paid_by'] ?? null,
            ]);

            // Add customer authorization signature
            $jobCard->signatures()->create([
                'signature_type' => 'Customer',
                'signature_data' => $paymentData['customer_signature_data'] ?? null,
                'signed_by_name' => $paymentData['customer_name'],
                'signed_date' => now(),
            ]);

            // Add delivery certificate signature
            $jobCard->signatures()->create([
                'signature_type' => 'Delivery',
                'signature_data' => $paymentData['delivery_signature_data'] ?? null,
                'signed_by_name' => $paymentData['customer_name'],
                'signed_date' => now(),
            ]);

            // Update job card status to delivered
            $jobCard->update([
                'job_card_status_id' => JobCardStatus::where('code', 'DELIVERED')->first()->id,
                'actual_delivery_date' => now(),
            ]);

            return $payment;
        });
    }

    /**
     * Step 5: Gate - Generate Gate Pass
     */
    public function generateGatePass(JobCard $jobCard)
    {
        if ($jobCard->status->code !== 'DELIVERED') {
            throw new \Exception('Job card must be delivered before generating gate pass');
        }

        $gatePassNo = $this->generateGatePassNumber();

        return GatePass::create([
            'gate_pass_no' => $gatePassNo,
            'job_card_id' => $jobCard->id,
            'vehicle_id' => $jobCard->vehicle_id,
            'gate_pass_status_id' => GatePassStatus::where('code', 'GENERATED')->first()->id,
            'customer_name' => $jobCard->customerParty->name,
            'customer_id_type' => $jobCard->customerParty->partyType->code,
            'customer_id_no' => $jobCard->customerParty->tax_id,
            'gate_pass_generated_date' => now(),
            'qr_code' => $this->generateQRCode($gatePassNo, $jobCard->job_card_no),
        ]);
    }

    /**
     * Release vehicle at gate
     */
    public function releaseVehicleAtGate(GatePass $gatePass, array $releaseData)
    {
        return DB::transaction(function () use ($gatePass, $releaseData) {
            $gatePass->update([
                'gate_pass_status_id' => GatePassStatus::where('code', 'USED')->first()->id,
                'gate_pass_used_date' => now(),
                'used_by' => $releaseData['used_by'] ?? null,
            ]);

            return $gatePass;
        });
    }

    /**
     * Helper: Add standard checks based on vehicle type
     */
    private function addStandardChecks(JobCard $jobCard, Vehicle $vehicle)
    {
        $standardChecks = [
            'Brakes Check',
            'Horn Test',
            'Lights Check',
            'Tyre Condition',
            'Fuel Tank Level',
            'Oil Level',
            'Coolant Level',
            'Windscreen/Mirror Condition',
            'Seat Condition',
            'General Cleanliness'
        ];

        foreach ($standardChecks as $check) {
            $jobCard->standardChecks()->create([
                'check_item' => $check,
                'status' => null,
                'remarks' => null,
            ]);
        }

        // Add after trial checks
        $afterTrialChecks = [
            'Brakes',
            'Acceleration',
            'Clutch',
            'Vibration',
            'Noise',
            'Gear Shifting',
            'Steering',
            'Suspension'
        ];

        foreach ($afterTrialChecks as $check) {
            $jobCard->afterTrialChecks()->create([
                'check_item' => $check,
                'status' => null,
                'remarks' => null,
            ]);
        }
    }

    /**
     * Helper: Generate unique job card number
     */
    private function generateJobCardNumber()
    {
        $prefix = date('Ymd');
        $count = JobCard::where('job_card_no', 'like', $prefix . '%')->count();
        return $prefix . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Helper: Generate invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV-' . date('YM');
        $count = JobCardPayment::where('invoice_no', 'like', $prefix . '%')->count();
        return $prefix . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Helper: Generate receipt number
     */
    private function generateReceiptNumber()
    {
        $prefix = 'RCP-' . date('YM');
        $count = JobCardPayment::where('receipt_no', 'like', $prefix . '%')->count();
        return $prefix . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Helper: Generate gate pass number
     */
    private function generateGatePassNumber()
    {
        $prefix = 'GP-' . date('Ymd');
        $count = GatePass::where('gate_pass_no', 'like', $prefix . '%')->count();
        return $prefix . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Helper: Generate QR code
     */
    private function generateQRCode($gatePassNo, $jobCardNo)
    {
        return base64_encode($gatePassNo . '|' . $jobCardNo . '|' . now()->timestamp);
    }
}
