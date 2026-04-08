# TVS Repair Service Management System - POC Documentation

## Overview
This is a comprehensive Proof of Concept (POC) implementation of the TVS Repair Service Management System for Car & General Tanzania. The system manages vehicles from first sale through all workshop visits, ensuring correct customer/owner validation, accurate job card creation, warranty control, and secure vehicle release across all branches.

## Project Structure

### Database Layer
All migrations are located in `database/migrations/` with the following files:

- **2026_04_08_000001_create_parties_table.php** - Party/Customer Management
  - `party_types` - Type of parties (Retail Customer, Dealer, Finance Company, Bank, Institution)
  - `parties` - Customer/party master data
  - `owner_mappings` - Vehicle ownership tracking

- **2026_04_08_000002_create_vehicles_table.php** - Vehicle Management
  - `vehicle_types` - 2W, 3W classification
  - `vehicle_variants` - Model and variant information
  - `sale_types` - Sales channels (Dealer, RBC, RFB, Showroom, Institutional)
  - `vehicles` - Vehicle master data
  - `vehicle_registrations` - Registration information
  - `vehicle_service_history` - Service visit tracking

- **2026_04_08_000003_create_warranties_table.php** - Warranty Management
  - `warranty_statuses` - Active, Expired, Not Available
  - `warranties` - Warranty records
  - `warranty_validations` - Warranty validation per job card
  - `warranty_claims` - Warranty claim tracking

- **2026_04_08_000004_create_job_cards_table.php** - Job Card Workflow
  - `service_types` - Free, Paid, Warranty, Goodwill, Campaign
  - `job_card_statuses` - Pending, In Progress, Completed, Delivered
  - `free_service_coupons` - Free service coupon management
  - `job_cards` - Main job card records
  - `job_card_standard_checks` - Pre-service verification checks
  - `job_card_after_trial_checks` - Post-repair test ride checks

- **2026_04_08_000005_create_parts_table.php** - Parts Management
  - `warehouses` - Inventory warehouse locations
  - `parts` - Parts master data
  - `part_stock` - Stock levels and reservations
  - `part_reservations` - Parts reserved for job cards
  - `charge_types` - Chargeable, Warranty, Goodwill, Campaign
  - `job_card_parts` - Parts consumed in job cards

- **2026_04_08_000006_create_labour_table.php** - Labour Management
  - `labour_operations` - Labour service operations
  - `technicians` - Technician information
  - `job_card_labour` - Labour entries per job card
  - `job_card_signatures` - Digital signatures and authorizations

- **2026_04_08_000007_create_gate_pass_table.php** - Payment & Gate Pass
  - `payment_statuses` - Paid, Credit, Warranty Claim Pending, Finance Claim Pending
  - `payment_modes` - Cash, Card, Bank Transfer, Wallet, Cheque
  - `job_card_payments` - Payment records
  - `gate_pass_statuses` - Generated, Used, Cancelled
  - `gate_passes` - Vehicle release authorization

- **2026_04_08_000008_create_analytics_table.php** - Analytics & Reporting
  - `branches` - Branch information
  - `job_card_metrics` - TAT and performance metrics
  - `vehicle_lifetime_data` - Lifetime service data per vehicle
  - `customer_lifetime_value` - Customer value metrics
  - `daily_branch_summary` - Daily job card summaries
  - `repeat_repair_analysis` - Repeat defect tracking

### Models Layer
All Eloquent models are in `app/Models/TVS/`:

**Party Models:**
- `PartyType.php` - Party type definitions
- `Party.php` - Customer/party master
- `OwnerMapping.php` - Vehicle ownership mapping

**Vehicle Models:**
- `VehicleType.php` - 2W/3W classification
- `VehicleVariant.php` - Vehicle model variants
- `SaleType.php` - Sales channel types
- `Vehicle.php` - Main vehicle master
- `VehicleRegistration.php` - Registration details
- `VehicleServiceHistory.php` - Service history tracking

**Warranty Models:**
- `WarrantyStatus.php` - Warranty status types
- `Warranty.php` - Warranty master
- `WarrantyValidation.php` - Warranty validation per job card
- `WarrantyClaim.php` - Warranty claim tracking

**Job Card Models:**
- `ServiceType.php` - Service type definitions
- `JobCardStatus.php` - Job card status definitions
- `FreeServiceCoupon.php` - Free service coupon
- `JobCard.php` - Main job card model
- `JobCardStandardCheck.php` - Pre-service checks
- `JobCardAfterTrialCheck.php` - Post-service checks

**Parts Models:**
- `Warehouse.php` - Warehouse/location master
- `Part.php` - Parts master
- `PartStock.php` - Stock levels and reorder info
- `PartReservation.php` - Parts reservation tracking
- `ChargeType.php` - Charge type definitions
- `JobCardPart.php` - Parts consumed in job card

**Labour Models:**
- `LabourOperation.php` - Labour operation master
- `Technician.php` - Technician information
- `JobCardLabour.php` - Labour entries
- `JobCardSignature.php` - Digital signatures

**Payment & Gate Models:**
- `PaymentStatus.php` - Payment status types
- `PaymentMode.php` - Payment mode types
- `JobCardPayment.php` - Payment records
- `GatePassStatus.php` - Gate pass status types
- `GatePass.php` - Gate pass/vehicle release

**Analytics Models:**
- `Branch.php` - Branch master
- `JobCardMetric.php` - TAT metrics
- `VehicleLifetimeData.php` - Vehicle lifetime metrics
- `CustomerLifetimeValue.php` - Customer CLV
- `DailyBranchSummary.php` - Daily summaries
- `RepeatRepairAnalysis.php` - Repeat repair tracking

### Services Layer
Core business logic in `app/Services/`:

1. **OwnershipValidationService.php**
   - Implements 5 ownership/customer validation rules:
     - Dealer Sales: Create customer code at first service
     - Retail Finance (Non-Bank): Map end-customer at first service
     - Retail Finance (Bank): Search existing customer code
     - Showroom Cash: Use retail customer code from day one
     - Institutional: Use institution as party, optional driver code
   - Methods: `validateAndCreateCustomer()`, `searchVehicle()`, `createProvisionalVehicle()`

2. **JobCardService.php**
   - Handles complete job card lifecycle:
     - Reception: `createJobCard()`
     - Workshop: `addPartToJobCard()`, `addLabourToJobCard()`
     - Supervisor: `completeJobCard()`
     - Accounts: `processPayment()`
     - Gate: `generateGatePass()`, `releaseVehicleAtGate()`
   - Auto-generates job card numbers, invoices, receipts, gate passes
   - Manages standard and after-trial checks

3. **WarrantyValidationService.php**
   - Warranty validation and claims processing
   - Methods: `validateWarrantyForJobCard()`, `createWarrantyClaim()`, `approveWarrantyClaim()`
   - Checks warranty status, expiry, kilometer limits

4. **ReportingService.php**
   - Analytics and reporting:
     - TAT (Turn Around Time) calculations
     - Branch performance reports
     - Vehicle lifetime data
     - Customer lifetime value
     - Repeat repair analysis
     - Daily branch summaries
     - Free service to paid conversion rates

### Controllers Layer
API controllers in `app/Http/Controllers/TVS/`:

1. **VehicleController.php**
   - Vehicle search by registration/chassis/engine number
   - Create provisional vehicles for unknown origins
   - Get vehicle details and history
   - List vehicle types, sale types, variants

2. **JobCardController.php**
   - Create job card (reception)
   - Add parts and labour (workshop)
   - Update standard and after-trial checks
   - Complete job card (supervisor)
   - Process payment (accounts)
   - Generate and release gate pass (gate)

3. **PartyController.php**
   - Create party (customer/institution)
   - Get and update party information
   - List parties with filters

4. **WarrantyController.php**
   - Create and manage warranties
   - Validate warranty for job card
   - Create, approve, and reject warranty claims
   - Get warranty coverage details

5. **ReportingController.php**
   - TAT metrics and branch reports
   - Warranty vs customer pay analysis
   - Vehicle and customer lifetime value
   - Repeat repair analysis
   - Daily branch summaries

## Workflow - Step by Step

### Step 1: Vehicle & Customer Validation (Reception)
```
1. Search vehicle by registration/chassis/engine number
   GET /api/tvs/vehicles/search

2. System validates ownership based on sale type
   - Validates registration data from warranty app
   - Validates registration data from registration app

3. Create/Map customer based on ownership rules
   POST /api/tvs/job-cards
   (Ownership validation happens internally)
```

### Step 2: Job Card Creation (Reception)
```
1. Advisor creates job card
   POST /api/tvs/job-cards

2. System pre-loads:
   - Standard checks based on vehicle model
   - Service packages

3. System creates digital job card with:
   - Vehicle details snapshot
   - Current warranty status
   - Owner information
   - All required checks
```

### Step 3: Workshop Execution
```
1. Technician adds parts consumed
   POST /api/tvs/job-cards/{jobCard}/parts

2. System checks stock availability and auto-reserves
   - If available: Reserved immediately
   - If unavailable: Job shifts to "Pending Parts" status

3. Technician adds labour operations
   POST /api/tvs/job-cards/{jobCard}/labour

4. After test ride, technician completes checks
   PUT /api/tvs/job-cards/{jobCard}/after-trial-checks
```

### Step 4: Supervisor Approval
```
1. Supervisor reviews parts and labour lines
2. Supervisor validates warranty/goodwill decisions
3. Supervisor completes job card with digital signature
   POST /api/tvs/job-cards/{jobCard}/complete
```

### Step 5: Payment Processing (Accounts)
```
1. System calculates totals
   - Parts Total, Labour Total, Subtotal, Tax, Grand Total

2. Cashier processes payment
   POST /api/tvs/job-cards/{jobCard}/process-payment

3. Customer and delivery certificates signed digitally
   - Customer Authorization (repairs approval)
   - Delivery Certificate (vehicle condition)

4. Job card status updated to "Delivered"
```

### Step 6: Gate Pass & Release
```
1. Gate officer generates gate pass
   POST /api/tvs/job-cards/{jobCard}/gate-pass

2. QR code generated for scanning

3. At exit, gate officer releases vehicle
   POST /api/tvs/gate-passes/{gatePass}/release

4. System logs exit time and sets status to "Used"
```

## API Endpoints

### Vehicle Management
- `GET /api/tvs/vehicles/search` - Search vehicle
- `POST /api/tvs/vehicles/provisional` - Create provisional vehicle
- `GET /api/tvs/vehicles/{vehicle}` - Get vehicle details
- `GET /api/tvs/vehicles` - List vehicles
- `GET /api/tvs/vehicles/types` - Get vehicle types
- `GET /api/tvs/vehicles/sale-types` - Get sale types
- `GET /api/tvs/vehicles/variants/{vehicleTypeId}` - Get variants

### Job Card Workflow
- `POST /api/tvs/job-cards` - Create job card
- `GET /api/tvs/job-cards/{jobCard}` - Get job card details
- `GET /api/tvs/job-cards` - List job cards
- `POST /api/tvs/job-cards/{jobCard}/parts` - Add parts
- `POST /api/tvs/job-cards/{jobCard}/labour` - Add labour
- `PUT /api/tvs/job-cards/{jobCard}/standard-checks` - Update checks
- `PUT /api/tvs/job-cards/{jobCard}/after-trial-checks` - Update after-trial checks
- `POST /api/tvs/job-cards/{jobCard}/complete` - Complete job card
- `POST /api/tvs/job-cards/{jobCard}/process-payment` - Process payment
- `POST /api/tvs/job-cards/{jobCard}/gate-pass` - Generate gate pass

### Party Management
- `POST /api/tvs/parties` - Create party
- `GET /api/tvs/parties/{party}` - Get party details
- `PUT /api/tvs/parties/{party}` - Update party
- `GET /api/tvs/parties` - List parties
- `GET /api/tvs/parties/types` - Get party types

### Warranty Management
- `POST /api/tvs/warranties` - Create warranty
- `GET /api/tvs/warranties/{warranty}` - Get warranty
- `GET /api/tvs/warranties` - List warranties
- `POST /api/tvs/warranties/validate-for-job/{jobCard}` - Validate warranty
- `POST /api/tvs/warranties/claims` - Create warranty claim
- `POST /api/tvs/warranties/claims/{claim}/approve` - Approve claim
- `POST /api/tvs/warranties/claims/{claim}/reject` - Reject claim

### Analytics & Reporting
- `GET /api/tvs/reports/branch-tat-metrics` - TAT metrics
- `GET /api/tvs/reports/branch-report` - Branch report
- `GET /api/tvs/reports/warranty-vs-customer-pay` - Warranty mix
- `GET /api/tvs/reports/free-service-conversion` - Free service conversion
- `GET /api/tvs/reports/vehicle-lifetime/{vehicleId}` - Vehicle lifetime
- `GET /api/tvs/reports/customer-lifetime/{partyId}` - Customer CLV
- `GET /api/tvs/reports/repeat-repairs/{vehicleId}` - Repeat repair analysis
- `GET /api/tvs/reports/daily-summary` - Daily branch summary

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate --path=database/migrations/2026_04_08_*
```

### 2. Seed Reference Data
```bash
php artisan db:seed --class=TVSReferenceDataSeeder
```

### 3. Register Routes
Add to your `routes/api.php`:
```php
require __DIR__ . '/tvs.php';
```

### 4. Verify Installation
```bash
php artisan tinker
>>> App\Models\TVS\VehicleType::all();
```

## Key Features Implemented

✅ **Ownership & Customer Validation** - 5 different sale channel rules
✅ **Vehicle Master & Registration** - Unique identifiers (Registration, Chassis, Engine)
✅ **Job Card Workflow** - Complete lifecycle from check-in to gate pass
✅ **Parts Management** - Stock tracking and auto-reservations
✅ **Labour Management** - Technician time tracking and rates
✅ **Warranty Management** - Validation, claims, and approvals
✅ **Digital Signatures** - Supervisor, Customer, Delivery, Gate Pass signatures
✅ **Gate Pass System** - QR codes and vehicle release
✅ **Payment Processing** - Multiple payment modes and statuses
✅ **Analytics & Reporting** - TAT, vehicle lifetime, customer CLV, repeat repairs
✅ **Provisional Vehicles** - Unknown origin handling with central validation flag

## Next Steps / Future Enhancements

- [ ] Mobile app implementation (Android tablets)
- [ ] Web interface for supervisors & managers
- [ ] ERP integration (SAP, Oracle, Tally)
- [ ] Mobile booking app integration
- [ ] OEM DMS integration
- [ ] SMS/Email notifications
- [ ] Real-time dashboard
- [ ] Machine learning for predictive maintenance
- [ ] Audit trail and activity logging
- [ ] Multi-branch corporate dashboard
- [ ] Offline-first mobile sync

## Database Relationships Summary

**Vehicle relationships:**
- A Vehicle has many JobCards, Warranties, ServiceHistory, OwnerMappings, GatePasses
- Vehicle belongs to VehicleVariant, VehicleType, SaleType

**JobCard relationships:**
- JobCard has many Parts, Labour, Checks, Signatures, Payments, WarrantyValidations
- JobCard belongs to Vehicle, Party (Customer & BillTo), ServiceType, JobCardStatus

**Party relationships:**
- Party has many Vehicles (via OwnerMapping), JobCards
- Party has CustomerLifetimeValue metrics

**Warranty relationships:**
- Warranty belongs to Vehicle
- Warranty has many Validations, Claims

## Support & Documentation
For detailed implementation examples, refer to the controller files and service layer documentation.

