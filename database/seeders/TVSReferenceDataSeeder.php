<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TVS\{
    VehicleType, SaleType, ServiceType, JobCardStatus, 
    PaymentStatus, PaymentMode, WarrantyStatus, GatePassStatus,
    ChargeType, PartyType, LabourOperation
};

class TVSReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        // Vehicle Types
        VehicleType::updateOrCreate(['code' => '2W'], ['name' => 'Two Wheeler']);
        VehicleType::updateOrCreate(['code' => '3W'], ['name' => 'Three Wheeler']);

        // Sale Types
        SaleType::updateOrCreate(['code' => 'DEALER'], ['name' => 'Dealer', 'description' => 'Dealer Wholesale Sales']);
        SaleType::updateOrCreate(['code' => 'RBC'], ['name' => 'Retail Finance Non-Bank', 'description' => 'Finance via non-bank (Watu, Nyali, Bluerock, Mogo)']);
        SaleType::updateOrCreate(['code' => 'RFB'], ['name' => 'Retail Finance Bank', 'description' => 'Finance via Banks']);
        SaleType::updateOrCreate(['code' => 'SHOWROOM'], ['name' => 'Showroom Cash', 'description' => 'Retail showroom cash sales']);
        SaleType::updateOrCreate(['code' => 'INSTITUTIONAL'], ['name' => 'Institutional', 'description' => 'Institutional/Tender/Government sales']);
        SaleType::updateOrCreate(['code' => 'EXTERNAL'], ['name' => 'External/Unknown', 'description' => 'External or unknown origin']);

        // Service Types
        ServiceType::updateOrCreate(['code' => 'FREE'], ['name' => 'Free Service']);
        ServiceType::updateOrCreate(['code' => 'PAID'], ['name' => 'Paid Service']);
        ServiceType::updateOrCreate(['code' => 'WARRANTY'], ['name' => 'Warranty Service']);
        ServiceType::updateOrCreate(['code' => 'GOODWILL'], ['name' => 'Goodwill Service']);
        ServiceType::updateOrCreate(['code' => 'CAMPAIGN'], ['name' => 'Campaign Service']);

        // Job Card Statuses
        JobCardStatus::updateOrCreate(['code' => 'PENDING'], ['name' => 'Pending']);
        JobCardStatus::updateOrCreate(['code' => 'PENDING_PARTS'], ['name' => 'Pending Parts']);
        JobCardStatus::updateOrCreate(['code' => 'IN_PROGRESS'], ['name' => 'In Progress']);
        JobCardStatus::updateOrCreate(['code' => 'COMPLETED'], ['name' => 'Completed']);
        JobCardStatus::updateOrCreate(['code' => 'DELIVERED'], ['name' => 'Delivered']);

        // Payment Statuses
        PaymentStatus::updateOrCreate(['code' => 'PAID'], ['name' => 'Paid']);
        PaymentStatus::updateOrCreate(['code' => 'CREDIT'], ['name' => 'Credit']);
        PaymentStatus::updateOrCreate(['code' => 'WARRANTY_CLAIM_PENDING'], ['name' => 'Warranty Claim Pending']);
        PaymentStatus::updateOrCreate(['code' => 'FINANCE_CLAIM_PENDING'], ['name' => 'Finance Claim Pending']);

        // Payment Modes
        PaymentMode::updateOrCreate(['code' => 'CASH'], ['name' => 'Cash']);
        PaymentMode::updateOrCreate(['code' => 'CARD'], ['name' => 'Card']);
        PaymentMode::updateOrCreate(['code' => 'BANK_TRANSFER'], ['name' => 'Bank Transfer']);
        PaymentMode::updateOrCreate(['code' => 'WALLET'], ['name' => 'Wallet']);
        PaymentMode::updateOrCreate(['code' => 'CHEQUE'], ['name' => 'Cheque']);

        // Warranty Statuses
        WarrantyStatus::updateOrCreate(['code' => 'Active'], ['name' => 'Active']);
        WarrantyStatus::updateOrCreate(['code' => 'Expired'], ['name' => 'Expired']);
        WarrantyStatus::updateOrCreate(['code' => 'Not Available'], ['name' => 'Not Available']);

        // Gate Pass Statuses
        GatePassStatus::updateOrCreate(['code' => 'GENERATED'], ['name' => 'Generated']);
        GatePassStatus::updateOrCreate(['code' => 'USED'], ['name' => 'Used']);
        GatePassStatus::updateOrCreate(['code' => 'CANCELLED'], ['name' => 'Cancelled']);

        // Charge Types
        ChargeType::updateOrCreate(['code' => 'Chargeable'], ['name' => 'Chargeable']);
        ChargeType::updateOrCreate(['code' => 'Warranty'], ['name' => 'Warranty']);
        ChargeType::updateOrCreate(['code' => 'Goodwill'], ['name' => 'Goodwill']);
        ChargeType::updateOrCreate(['code' => 'Campaign'], ['name' => 'Campaign']);

        // Party Types
        PartyType::updateOrCreate(['code' => 'RETAIL_CUSTOMER'], ['name' => 'Retail Customer']);
        PartyType::updateOrCreate(['code' => 'DEALER'], ['name' => 'Dealer']);
        PartyType::updateOrCreate(['code' => 'FINANCE_COMPANY'], ['name' => 'Finance Company']);
        PartyType::updateOrCreate(['code' => 'BANK'], ['name' => 'Bank']);
        PartyType::updateOrCreate(['code' => 'INSTITUTION'], ['name' => 'Institution']);

        // Default Labour Operations for 2W
        $twoWheeler = VehicleType::where('code', '2W')->first();
        if ($twoWheeler) {
            LabourOperation::updateOrCreate(
                ['operation_code' => 'OIL_CHANGE'],
                [
                    'operation_name' => 'Oil Change',
                    'standard_labor_rate' => 5000,
                    'standard_hours' => 0.5,
                    'vehicle_type_id' => $twoWheeler->id,
                    'is_active' => true
                ]
            );
            LabourOperation::updateOrCreate(
                ['operation_code' => 'FILTER_CHANGE'],
                [
                    'operation_name' => 'Filter Change',
                    'standard_labor_rate' => 3000,
                    'standard_hours' => 0.25,
                    'vehicle_type_id' => $twoWheeler->id,
                    'is_active' => true
                ]
            );
            LabourOperation::updateOrCreate(
                ['operation_code' => 'BRAKE_INSPECTION'],
                [
                    'operation_name' => 'Brake Inspection',
                    'standard_labor_rate' => 7000,
                    'standard_hours' => 1,
                    'vehicle_type_id' => $twoWheeler->id,
                    'is_active' => true
                ]
            );
        }

        // Default Labour Operations for 3W
        $threeWheeler = VehicleType::where('code', '3W')->first();
        if ($threeWheeler) {
            LabourOperation::updateOrCreate(
                ['operation_code' => 'OIL_CHANGE'],
                [
                    'operation_name' => 'Oil Change',
                    'standard_labor_rate' => 8000,
                    'standard_hours' => 0.5,
                    'vehicle_type_id' => $threeWheeler->id,
                    'is_active' => true
                ]
            );
            LabourOperation::updateOrCreate(
                ['operation_code' => 'FILTER_CHANGE'],
                [
                    'operation_name' => 'Filter Change',
                    'standard_labor_rate' => 5000,
                    'standard_hours' => 0.25,
                    'vehicle_type_id' => $threeWheeler->id,
                    'is_active' => true
                ]
            );
        }

        $this->command->info('TVS reference data seeded successfully!');
    }
}
