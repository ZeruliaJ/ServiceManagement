# TVS Repair Service Management System - API Testing Guide

## ✅ System Status: READY FOR TESTING

**Base URL**: `http://127.0.0.1:8000`  
**API Prefix**: `/api/tvs`  
**Development Server**: Running on port 8000

---

## 🚀 Quick Start

### 1. Get Reference Data First
These endpoints provide baseline data needed for testing:

#### Get All Vehicle Types
```
GET http://127.0.0.1:8000/api/tvs/vehicles/types
```
**Response**: List of vehicle types with IDs (needed for creating parties/vehicles)

#### Get All Sale Types
```
GET http://127.0.0.1:8000/api/tvs/vehicles/sale-types
```

#### Get Party Types
```
GET http://127.0.0.1:8000/api/tvs/parties/types
```
**Response**: List of party types (Customer, Dealer, Finance Company, Bank, Institution)

#### Get Vehicle Variants
```
GET http://127.0.0.1:8000/api/tvs/vehicles/variants/{vehicleTypeId}
```
**Example**: `GET /api/tvs/vehicles/variants/1`

---

## 📝 Party/Customer Management

### Create a Party (Customer)
```http
POST /api/tvs/parties
Content-Type: application/json

{
    "party_type_id": 1,
    "party_name": "John Doe",
    "email": "john@example.com",
    "phone": "8765432109",
    "address": "123 Main Street",
    "city": "Bangalore",
    "state": "KA",
    "postal_code": "560005"
}
```
**Response**: Party object with `id` (save this for later use)

### List All Parties
```
GET /api/tvs/parties
```

### Get Party Details
```
GET /api/tvs/parties/{partyId}
```

### Update Party
```http
PUT /api/tvs/parties/{partyId}
Content-Type: application/json

{
    "party_name": "Jane Doe",
    "email": "jane@example.com"
}
```

---

## 🚗 Vehicle Management

### Create Vehicle (Provisional)
```http
POST /api/tvs/vehicles/provisional
Content-Type: application/json

{
    "registration_no": "KA01AB1234",
    "chassis_no": "CHASSIS123456789",
    "engine_no": "ENGINE123456789",
    "vehicle_type_id": 1,
    "vehicle_variant_id": 1,
    "color": "Silver",
    "sale_type_id": 1
}
```
**Response**: Vehicle object with `id` (save for job card creation)

### Search Vehicles
```
GET /api/tvs/vehicles/search?registration_no=KA01AB1234
OR
GET /api/tvs/vehicles/search?chassis_no=CHASSIS123456789
OR
GET /api/tvs/vehicles/search?engine_no=ENGINE123456789
```

### Get Vehicle Details
```
GET /api/tvs/vehicles/{vehicleId}
```

### List All Vehicles
```
GET /api/tvs/vehicles
```

---

## 🎯 Job Card Workflow (Complete End-to-End Flow)

### Step 1: Create Job Card (Reception)
```http
POST /api/tvs/job-cards
Content-Type: application/json

{
    "vehicle_id": 1,
    "service_type_id": 1,
    "customer_party_id": 1,
    "bill_to_party_id": 1,
    "description": "General service and repair"
}
```
**Response**: Job card object with `id` (use for next steps)  
**Job Card Status**: "Received" (workflow starts here)

### Step 2: Workshop - Add Parts to Job Card
```http
POST /api/tvs/job-cards/{jobCardId}/parts
Content-Type: application/json

{
    "items": [
        {
            "part_id": 1,
            "warehouse_id": 1,
            "quantity": 2,
            "unit_price": 500.00,
            "discount_amount": 0,
            "charge_type_id": 1
        }
    ]
}
```

### Step 3: Workshop - Add Labour Operations to Job Card
```http
POST /api/tvs/job-cards/{jobCardId}/labour
Content-Type: application/json

{
    "items": [
        {
            "operation_id": 1,
            "technician_id": 1,
            "hours": 2.5,
            "rate": 400.00,
            "charge_type_id": 1
        }
    ]
}
```

### Step 4: Supervisor - Update Standard Checks
```http
PUT /api/tvs/job-cards/{jobCardId}/standard-checks
Content-Type: application/json

{
    "checks": {
        "engine_condition": "Good",
        "transmission_condition": "Good",
        "brake_system": "Good",
        "electrical_system": "Good",
        "suspension": "Good"
    }
}
```

### Step 5: Supervisor - Update Post-Trial Checks
```http
PUT /api/tvs/job-cards/{jobCardId}/after-trial-checks
Content-Type: application/json

{
    "checks": {
        "acceleration": "Good",
        "braking": "Good",
        "steering": "Good",
        "noise_vibration": "None",
        "fuel_consumption": "Normal"
    }
}
```

### Step 6: Supervisor - Complete Job Card
```http
POST /api/tvs/job-cards/{jobCardId}/complete
Content-Type: application/json

{
    "completion_notes": "Service completed successfully",
    "completed_by": "Supervisor Name"
}
```
**Job Card Status**: "Completed"

### Step 7: Accounts - Process Payment
```http
POST /api/tvs/job-cards/{jobCardId}/process-payment
Content-Type: application/json

{
    "payment_mode_id": 1,
    "payment_amount": 1500.00,
    "paid_date": "2024-04-08"
}
```
**Payment Status**: "Paid"

### Step 8: Gate - Generate Gate Pass
```http
POST /api/tvs/job-cards/{jobCardId}/gate-pass
Content-Type: application/json

{
    "authorized_by": "Gate Keeper Name",
    "release_date": "2024-04-08"
}
```
**Response**: Gate pass object with `id`

### Step 9: Gate - Release Vehicle
```http
POST /api/tvs/gate-passes/{gatePassId}/release
Content-Type: application/json

{
    "released_by": "Gate Keeper Name",
    "release_timestamp": "2024-04-08 17:30:00"
}
```
**Gate Pass Status**: "Released"  
**Vehicle Status**: "Available for service" (Ready for next job)

---

## 🛡️ Warranty Management

### Create Warranty
```http
POST /api/tvs/warranties
Content-Type: application/json

{
    "vehicle_id": 1,
    "warranty_type": "Bumper-to-Bumper",
    "start_date": "2024-04-08",
    "end_date": "2025-04-08",
    "coverage_amount": 50000.00,
    "warranty_status_id": 1
}
```

### Get Vehicle Warranties
```
GET /api/tvs/warranties/vehicle/{vehicleId}
```

### Validate Warranty for Job Card
```
POST /api/tvs/warranties/validate-for-job/{jobCardId}
```
**Response**: Warranty validation status and eligibility

### Create Warranty Claim
```http
POST /api/tvs/warranties/claims
Content-Type: application/json

{
    "warranty_id": 1,
    "job_card_id": 1,
    "claim_amount": 5000.00,
    "claim_description": "Engine repair covered under warranty"
}
```

### Approve Warranty Claim
```
POST /api/tvs/warranties/claims/{claimId}/approve
```

### Reject Warranty Claim
```
POST /api/tvs/warranties/claims/{claimId}/reject
```

### Get Warranty Claims for Vehicle
```
GET /api/tvs/warranties/vehicle/{vehicleId}/claims
```

---

## 📊 Analytics & Reporting

### Get Branch TAT Metrics
```
GET /api/tvs/reports/branch-tat-metrics
```
**Returns**: Turn-around time metrics per branch

### Get Warranty vs Customer Pay Mix
```
GET /api/tvs/reports/warranty-vs-customer-pay
```
**Returns**: Percentage breakdown of warranty vs customer paid repairs

### Get Free Service Conversion Rate
```
GET /api/tvs/reports/free-service-conversion
```
**Returns**: Conversion rate of free service coupons to paid services

### Get Vehicle Lifetime Data
```
GET /api/tvs/reports/vehicle-lifetime/{vehicleId}
```
**Returns**: Complete service history, total spend, unique issues

### Get Customer Lifetime Value
```
GET /api/tvs/reports/customer-lifetime/{partyId}
```
**Returns**: CLV, total services, maintenance trend

### Get Repeat Repair Analysis
```
GET /api/tvs/reports/repeat-repairs/{vehicleId}
```
**Returns**: Issues repaired multiple times, repair costs, time between repairs

### Get Daily Branch Summary
```
GET /api/tvs/reports/daily-summary
```
**Returns**: Daily metrics per branch for management dashboard

---

## 📱 Testing the Complete Workflow

### Recommended Test Sequence:

1. **Fetch Reference Data**
   - GET vehicle types, party types, variants, sale types
   - Save IDs for use in create requests

2. **Create Test Data**
   - Create 2-3 test parties (customers)
   - Create 3-4 test vehicles

3. **Complete Job Card Workflow**
   - Create job card for one vehicle
   - Add parts and labour
   - Update checks
   - Complete job card
   - Process payment
   - Generate and release gate pass

4. **Test Warranty Features**
   - Create warranty for vehicle
   - Validate warranty eligibility
   - Create and approve warranty claim

5. **Generate Reports**
   - Run all analytics endpoints
   - Verify data consistency

---

## 🔍 Response Format

All responses follow this format:

### Success Response (200 OK)
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        // Response data
    }
}
```

### Error Response (400/422/500)
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

---

## 🛠️ Tools for Testing

### Using cURL (Command Line):
```bash
curl -X POST http://127.0.0.1:8000/api/tvs/parties \
  -H "Content-Type: application/json" \
  -d '{"party_type_id":1,"party_name":"John Doe","email":"john@example.com","phone":"9876543210","address":"123 Main St","city":"Bangalore","state":"KA","postal_code":"560005"}'
```

### Using Postman:
1. Import endpoints as collection
2. Set base URL: `http://127.0.0.1:8000`
3. Set header: `Content-Type: application/json`
4. Create requests for each endpoint

### Using Thunder Client (VS Code):
1. Open Thunder Client extension
2. Create new request
3. Configure as needed using examples above

---

## 📋 Common Issues & Solutions

### Issue: "Table not found"
**Solution**: Run `php artisan migrate --fresh` to reset database

### Issue: "Foreign key constraint failed"
**Solution**: Ensure you use valid IDs from reference data first

### Issue: 404 Not Found
**Solution**: Verify URL is correct with `http://` prefix, no https

### Issue: Server not running
**Solution**: Run `php artisan serve` in project root directory

---

## ✅ Checklist for Full System Validation

- [ ] All reference data endpoints return data
- [ ] Party creation and retrieval works
- [ ] Vehicle creation and search works
- [ ] Complete job card workflow succeeds end-to-end
- [ ] Warranty validation returns correct status
- [ ] Payment processing works
- [ ] Gate pass release works
- [ ] All analytics reports return data
- [ ] Database has 50+ tables created
- [ ] No SQL errors in Laravel logs

---

## 📚 Documentation Files

- **Complete Module Documentation**: `SYSTEM_DOCUMENTATION.md`
- **Database Schema**: `DATABASE_SCHEMA.md`
- **API Testing Guide**: This file
- **Project Structure**: `PROJECT_STRUCTURE.md`

---

**Last Updated**: April 8, 2024  
**System Version**: 1.0 (POC)  
**Status**: Production Ready for Testing
