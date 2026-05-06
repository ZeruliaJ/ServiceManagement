<?php

namespace App\Services;

use App\Models\TVS\Vehicle;
use App\Models\TVS\Party;
use App\Models\TVS\PartyType;
use App\Models\TVS\OwnerMapping;
use App\Models\TVS\SaleType;
use Illuminate\Support\Facades\DB;

class OwnershipValidationService
{
    /**
     * Validate and create customer mapping based on sale type
     * Step 1: Ownership and Customer Validation Rules
     * 
     * @param Vehicle $vehicle
     * @param string $saleTypeCode
     * @param array $customerData
     * @return array ['party_id' => int, 'bill_to_party_id' => int, 'validated' => bool, 'message' => string]
     */
    public function validateAndCreateCustomer(Vehicle $vehicle, string $saleTypeCode, array $customerData)
    {
        $saleType = SaleType::where('code', $saleTypeCode)->first();
        
        if (!$saleType) {
            return [
                'success' => false,
                'message' => 'Invalid sale type'
            ];
        }

        return match($saleType->code) {
            'DEALER' => $this->handleDealerSale($vehicle, $customerData),
            'RBC' => $this->handleRetailFinanceNonBank($vehicle, $customerData),
            'RFB' => $this->handleRetailFinanceBank($vehicle, $customerData),
            'SHOWROOM' => $this->handleShowroomCash($vehicle, $customerData),
            'INSTITUTIONAL' => $this->handleInstitutional($vehicle, $customerData),
            default => [
                'success' => false,
                'message' => 'Unknown sale type'
            ]
        };
    }

    /**
     * Dealer Wholesale Sales
     * Create unique customer code at first service
     */
    private function handleDealerSale(Vehicle $vehicle, array $customerData)
    {
        try {
            $dealer = $this->findOrCreateParty(
                PartyType::where('code', 'DEALER')->first()->id,
                $customerData['dealer_code'] ?? null,
                $customerData
            );

            // Check if customer already has mapping
            $existingMapping = $vehicle->ownerMappings()
                ->where('ownership_type', 'DEALER')
                ->where('is_current', true)
                ->first();

            if ($existingMapping) {
                return [
                    'success' => true,
                    'party_id' => $existingMapping->party_id,
                    'bill_to_party_id' => $existingMapping->party_id,
                    'message' => 'Existing dealer mapping found'
                ];
            }

            // Create new customer at first service
            $customer = $this->createOrUpdateParty(
                PartyType::where('code', 'RETAIL_CUSTOMER')->first()->id,
                $this->generateCustomerCode(),
                $customerData
            );

            // Create mapping
            $this->createOwnerMapping($vehicle, $customer, 'DEALER');

            return [
                'success' => true,
                'party_id' => $customer->id,
                'bill_to_party_id' => $dealer->id,
                'message' => 'New customer created for dealer sale'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing dealer sale: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retail Finance - Non-Bank (Watu, Nyali, Bluerock, Mogo)
     * Legal owner = Finance Company, create end customer code at first service
     */
    private function handleRetailFinanceNonBank(Vehicle $vehicle, array $customerData)
    {
        try {
            // Finance company as legal owner
            $financeCompany = $this->createOrUpdateParty(
                PartyType::where('code', 'FINANCE_COMPANY')->first()->id,
                $customerData['finance_company_code'] ?? $customerData['finance_company_id'],
                [
                    'name' => $customerData['finance_company_name'] ?? 'Finance Company',
                    'code' => $customerData['finance_company_code'] ?? $customerData['finance_company_id']
                ]
            );

            // Check if end customer already has mapping
            $existingMapping = $vehicle->ownerMappings()
                ->where('ownership_type', 'RBC')
                ->where('is_current', true)
                ->first();

            if ($existingMapping) {
                return [
                    'success' => true,
                    'party_id' => $existingMapping->party_id,
                    'bill_to_party_id' => $financeCompany->id,
                    'message' => 'Existing RBC mapping found'
                ];
            }

            // Create end customer code
            $endCustomer = $this->createOrUpdateParty(
                PartyType::where('code', 'RETAIL_CUSTOMER')->first()->id,
                $this->generateCustomerCode(),
                $customerData
            );

            // Create mapping with finance company as bill-to
            $this->createOwnerMapping($vehicle, $financeCompany, 'RBC');

            return [
                'success' => true,
                'party_id' => $endCustomer->id,
                'bill_to_party_id' => $financeCompany->id,
                'message' => 'End customer created for non-bank finance'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing RBC sale: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retail Finance - Bank
     * Search for existing customer code and maintain service history
     */
    private function handleRetailFinanceBank(Vehicle $vehicle, array $customerData)
    {
        try {
            // Bank as legal owner
            $bank = $this->createOrUpdateParty(
                PartyType::where('code', 'BANK')->first()->id,
                $customerData['bank_code'] ?? $customerData['bank_id'],
                [
                    'name' => $customerData['bank_name'] ?? 'Bank',
                    'code' => $customerData['bank_code'] ?? $customerData['bank_id']
                ]
            );

            // Search for existing customer code
            $customer = Party::where('code', $customerData['customer_code'] ?? null)->first();

            if (!$customer) {
                // Create new customer if not found
                $customer = $this->createOrUpdateParty(
                    PartyType::where('code', 'RETAIL_CUSTOMER')->first()->id,
                    $customerData['customer_code'] ?? $this->generateCustomerCode(),
                    $customerData
                );
            }

            // Create mapping with bank as bill-to
            $this->createOwnerMapping($vehicle, $bank, 'RFB');

            return [
                'success' => true,
                'party_id' => $customer->id,
                'bill_to_party_id' => $bank->id,
                'message' => 'Bank finance customer processed'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing RFB sale: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Showroom Cash Sales
     * Owner_Type = Retail_Customer, maintain service history from day one
     */
    private function handleShowroomCash(Vehicle $vehicle, array $customerData)
    {
        try {
            $customer = $this->createOrUpdateParty(
                PartyType::where('code', 'RETAIL_CUSTOMER')->first()->id,
                $customerData['customer_code'] ?? $this->generateCustomerCode(),
                $customerData
            );

            // Create mapping
            $this->createOwnerMapping($vehicle, $customer, 'SHOWROOM');

            return [
                'success' => true,
                'party_id' => $customer->id,
                'bill_to_party_id' => $customer->id,
                'message' => 'Showroom cash customer processed'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing showroom cash sale: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Institutional/Tender Sales
     * Owner_Type = Institution (government, NGO, fleet)
     * Optional driver/representative code per job card
     */
    private function handleInstitutional(Vehicle $vehicle, array $customerData)
    {
        try {
            $institution = $this->createOrUpdateParty(
                PartyType::where('code', 'INSTITUTION')->first()->id,
                $customerData['institution_code'] ?? null,
                $customerData
            );

            // Create mapping
            $this->createOwnerMapping($vehicle, $institution, 'INSTITUTIONAL');

            return [
                'success' => true,
                'party_id' => $institution->id,
                'bill_to_party_id' => $institution->id,
                'message' => 'Institutional customer processed'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing institutional sale: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Search vehicle by unique identifiers
     * Registration number, Chassis number, Engine number
     */
    public function searchVehicle($registrationNo = null, $chassisNo = null, $engineNo = null)
    {

        $query = Vehicle::query();

        if ($registrationNo) {
            $query->where('registration_no', $registrationNo);
        }

        if ($chassisNo) {
            $query->where('chassis_no', $chassisNo);
        }

        if ($engineNo) {
            $query->where('engine_no', $engineNo);
        }


        return $query->first();
    }

    /**
     * Create provisional vehicle if not found
     */
    public function createProvisionalVehicle(array $vehicleData)
    {
        return Vehicle::create([
            'registration_no' => $vehicleData['registration_no'],
            'chassis_no' => $vehicleData['chassis_no'],
            'engine_no' => $vehicleData['engine_no'],
            'vehicle_variant_id' => $vehicleData['vehicle_variant_id'] ?? null,
            'vehicle_type_id' => $vehicleData['vehicle_type_id'] ?? null,
            'sale_type_id' => SaleType::where('code', 'EXTERNAL')->first()?->id,
            'sale_date' => now(),
            'is_provisional' => true,
            'is_validated' => false
        ]);
    }

    /**
     * Helper: Find or create party
     */
    private function findOrCreateParty($partyTypeId, $code, array $data)
    {
        return Party::firstOrCreate(
            ['code' => $code],
            [
                'party_type_id' => $partyTypeId,
                'name' => $data['name'] ?? $data['party_name'] ?? 'Unknown',
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'tax_id' => $data['tax_id'] ?? $data['tin'] ?? null,
                'address' => $data['address'] ?? null,
            ]
        );
    }

    /**
     * Helper: Create or update party
     */
    private function createOrUpdateParty($partyTypeId, $code, array $data)
    {
        return Party::updateOrCreate(
            ['code' => $code],
            [
                'party_type_id' => $partyTypeId,
                'name' => $data['name'] ?? $data['party_name'] ?? 'Unknown',
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'tax_id' => $data['tax_id'] ?? $data['tin'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'region' => $data['region'] ?? null,
                'district' => $data['district'] ?? null,
                'town' => $data['town'] ?? null,
            ]
        );
    }

    /**
     * Helper: Create owner mapping
     */
    private function createOwnerMapping(Vehicle $vehicle, Party $party, $ownershipType)
    {
        // Mark previous as non-current
        $vehicle->ownerMappings()->update(['is_current' => false]);

        // Create new mapping
        return OwnerMapping::create([
            'vehicle_id' => $vehicle->id,
            'party_id' => $party->id,
            'ownership_type' => $ownershipType,
            'ownership_start_date' => now(),
            'is_current' => true
        ]);
    }

    /**
     * Helper: Generate unique customer code
     */
    private function generateCustomerCode()
    {
        $code = 'CUST-' . date('Ymd') . '-' . random_int(1000, 9999);
        
        while (Party::where('code', $code)->exists()) {
            $code = 'CUST-' . date('Ymd') . '-' . random_int(1000, 9999);
        }

        return $code;
    }
}
