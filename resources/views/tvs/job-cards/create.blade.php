<x-app-layout :title="$title">

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .form-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .form-card .card-header { background:#fff; border-bottom:1px solid #f0f0f0; padding:.7rem 1rem; }
    .section-label { font-size:.75rem; font-weight:700; color:#273d80; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px; }
    .vehicle-snapshot { border-radius:9px; padding:14px; background:#f0f4ff; border:1px solid #c7d8f8; display:none; margin-top:10px; }
    .vehicle-snapshot.show { display:block; }
    .snap-item { display:flex; justify-content:space-between; font-size:.8rem; padding:4px 0; border-bottom:1px solid rgba(0,0,0,.04); }
    .snap-item:last-child { border-bottom:none; }
    .snap-label { color:#6b7280; font-weight:600; }
    .snap-val { font-weight:600; color:#1e2a4a; }
    .badge-active-w { background:#d1e7dd; color:#0a3622; font-size:.68rem; padding:2px 7px; border-radius:12px; font-weight:700; }
    .badge-expired-w { background:#f8d7da; color:#842029; font-size:.68rem; padding:2px 7px; border-radius:12px; font-weight:700; }
    .ownership-hint { border-radius:8px; padding:10px 14px; font-size:.8rem; background:#fffbeb; border-left:3px solid #f59e0b; margin-bottom:10px; display:none; }
    .ownership-hint.show { display:block; }
    .step-badge { width:28px; height:28px; border-radius:50%; background:#273d80; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; margin-right:8px; flex-shrink:0; }
</style>
@endpush

<div class="container-fluid">
   <div class="page-hero">
    <a href="{{ route('tvs.job-cards') }}" class="btn btn-sm mb-2" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;font-size:.78rem;"><i class="bx bx-arrow-back me-1"></i>Back to Job Cards</a>
   <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">
    Create Job Card – Reception
    @if(auth()->user()->warehouse)
        | <i class="bx bx-buildings me-1"></i>{{ auth()->user()->warehouse }}
    @endif
</h2>
  {{-- <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">Step 1: Vehicle & customer validation → Step 2: Service details → Step 3: Assign technician</p> --}} 
</div>

    <div class="row g-3">
         {{-- Step 1: Vehicle Search --}}
<div class="card form-card">
    <div class="card-header">
        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
            <span class="step-badge">1</span>Vehicle Information
        </h6>

        <div class="d-flex gap-2 align-items-center ms-auto">
            <input type="text" id="vehicleSearch" class="form-control form-control-sm"
                placeholder="Search by chassis / engine / reg no..."
                style="width:260px;">
            
            <button type="button" class="btn btn-sm btn-primary" onclick="searchVehicle()">
                <i class="fas fa-search"></i> Search
            </button>
        </div>

    </div>
    <div class="card-body p-3">
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Chassis No</label>
                <input type="text" id="vs-chassis" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Engine No</label>
                <input type="text" id="vs-engine" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Registration No</label>
                <input type="text" id="vs-reg" class="form-control form-control-sm" placeholder="T123ABC">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Vehicle Model</label>
                <input type="text" id="vs-model" class="form-control form-control-sm">
            </div>
        </div>{{-- end row 1 --}}


        <div class="row g-2 mt-1">
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Color</label>
                <input type="text" id="vs-color" class="form-control form-control-sm">
            </div>
           
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Odometer Reading (km)</label>
                <input type="number" id="odometerIn" class="form-control form-control-sm" placeholder="e.g. 12500">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Fuel Level</label>
                <select id="fuelLevel" class="form-select form-select-sm">
                    <option value="">Select...</option>
                    <option>Empty</option>
                    <option>1/4</option>
                    <option>1/2</option>
                    <option>3/4</option>
                    <option>Full</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Odometer Working</label>
                <select id="odometerWorking" class="form-select form-select-sm">
                    <option value="">Select...</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
        </div>{{-- end row 2 --}}
 <div class="row g-2 mt-1">
        <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Last Service </label>
                <input type="text" id="lastService" class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Warranty Status </label>
                <input type="text" id="warrantyStatus" class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Warranty Expires </label>
                <input type="text" id="warrantyExpires" class="form-control form-control-sm">
        </div>
         <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Dealer </label>
                <input type="text" id="Dealer" class="form-control form-control-sm">
        </div>
</div>
 <div class="row g-2 mt-1">
        <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Purchase Date</label>
                <input type="text" id="purchaseDate" class="form-control form-control-sm">
        </div>
    </div>{{-- end card-body --}}
</div>{{-- end card --}}

<div class="card form-card">
   <div class="card-header d-flex align-items-center">
    <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
        <span class="step-badge">2</span> Customer Details
    </h6>

    <div class="d-flex gap-2 align-items-center ms-auto">
        <input type="text" id="customerSearch" class="form-control form-control-sm" 
            placeholder="Search by phone..." style="width:180px;">
        <button type="button" class="btn btn-sm btn-primary" onclick="searchCustomer()">
            <i class="fas fa-search"></i> Search
        </button>
    </div>
</div>
    <div class="card-body p-3">
        <div class="row g-2">
          <div class="col-md-3">
          <label class="form-label fw-600" style="font-size:.75rem;">Name</label>
          <input type="text" id="customerName" class="form-control form-control-sm">       
</div>
         <div class="col-md-3">
          <label class="form-label fw-600" style="font-size:.75rem;">Phone</label>
          <input type="text" id="customerPhone" class="form-control form-control-sm">        
</div>
       <div class="col-md-3">
        <label class="form-label fw-600" style="font-size:.75rem;">Email</label>    
        <input type="text" id="customerEmail" class="form-control form-control-sm">
</div>
        <div class="col-md-3">
          <label class="form-label fw-600" style="font-size:.75rem;">Address</label>
            <input type="text" id="customerAddress" class="form-control form-control-sm">
    </div>
</div>
</div>  {{--end card body --}}       
</div>  {{--end card --}}


{{-- Step 2: Service Details --}}
<div class="card form-card">
    <div class="card-header">
        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
            <span class="step-badge">3</span>Service Details
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="row g-2">
        {{--   <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Check-In Date</label>
                <input type="text" id="checkInDate" class="form-control form-control-sm">
            </div>
            --}} 
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Estimated Delivery <span class="text-danger">*</span></label>
                <input type="text" id="estimatedDelivery" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Service Type <span class="text-danger">*</span></label>
                <select id="serviceTypeId" class="form-select form-select-sm">
                    <option value="">Select service type...</option>
                        <option value="free" selected>Free Service</option>
                        <option value="paid">Paid Service</option>
                        <option value="warranty_repair">Warranty Repair</option>
                        <option value="pdi_repair">PDI Repair</option>
                        <option value="free_service_camp">Free Service Camp</option>
                        <option value="goodwill_warranty">Good Will Warranty</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Priority <span class="text-danger">*</span></label>
                <select id="priority" class="form-select form-select-sm">
                    <option value="Normal">Normal</option>
                    <option value="Urgent">Urgent</option>
                    <option value="Emergency">Emergency</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-600" style="font-size:.75rem;">Customer Complaints / Requirements <span class="text-danger">*</span></label>
                <textarea id="complaints" class="form-control form-control-sm" rows="3" placeholder="Describe customer complaints, issues, and service requirements..."></textarea>
            </div>
        </div>{{-- end row --}}
    </div>{{-- end card-body --}}
</div>{{-- end card --}}

            {{-- Standard Checks preload preview --}}
<div class="card form-card" id="checksCard">
    <div class="card-header">
        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;"><i class="bx bx-list-check me-2" style="color:#22c55e;"></i>Standard Checks (Pre-loaded)</h6>
    </div>
    <div class="card-body p-3">
    {{-- Standard Checks --}}
        <div class="mb-3">
        <h6 class="fw-700 mb-2" style="font-size:.78rem;color:#1e2a4a;border-bottom:1px solid #e2e8f0;padding-bottom:6px;">Standard Checks</h6>
        <div class="row g-2">
    @foreach(['Damages','Missing Items','Mirrors','Seat Cover','Bag Hook','Accessories','Tool Kit','Battery','Fuse','Gear Box Oil','Fuel Quantity','Lights/Horn','Wheels/Tyres','Brake Fluid Level','Oil Leakage'] as $index => $check)
    <div class="col-md-4">
        <div class="border rounded p-2" style="font-size:.78rem; border-color: #dee2e6 !important;" id="card_{{ $index }}">
            <div class="fw-600 mb-1">{{ $check }}</div>

            {{-- OK / Not OK Toggle --}}
            <div class="btn-group btn-group-sm w-100 mb-0" role="group">
                <input type="radio" class="btn-check" name="std_{{ $index }}" id="std_ok_{{ $index }}" value="ok"
                    onclick="toggleNotOkPanel({{ $index }}, false)">
                <label class="btn btn-outline-success" for="std_ok_{{ $index }}">
                    <i class="bx bx-check me-1"></i>OK
                </label>

                <input type="radio" class="btn-check" name="std_{{ $index }}" id="std_notok_{{ $index }}" value="not_ok"
                    onclick="toggleNotOkPanel({{ $index }}, true)">
                <label class="btn btn-outline-danger" for="std_notok_{{ $index }}">
                    <i class="bx bx-x me-1"></i>Not OK
                </label>
            </div>

            {{-- Collapsible Upload/Capture Panel --}}
            <div class="notok-panel mt-2" id="notok_panel_{{ $index }}" style="display:none;">
                <div class="row g-2">
                    {{-- Capture Photo --}}
                    <div class="col-6">
                        <label for="capture_{{ $index }}"
                            class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                            style="border: 2px dashed #adb5bd; background: transparent; color: #555; cursor: pointer; font-size:.75rem; padding: 6px 4px; border-radius: 6px;">
                            <i class="bx bx-camera"></i> Capture Photo
                        </label>
                        <input type="file" id="capture_{{ $index }}" name="photo_capture_{{ $index }}"
                            accept="image/*" capture="environment" class="d-none"
                            onchange="previewImage(this, {{ $index }})">
                    </div>

                    {{-- Upload --}}
                    <div class="col-6">
                        <label for="upload_{{ $index }}"
                            class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                            style="border: 2px dashed #adb5bd; background: transparent; color: #555; cursor: pointer; font-size:.75rem; padding: 6px 4px; border-radius: 6px;">
                            <i class="bx bx-upload"></i> Upload
                        </label>
                        <input type="file" id="upload_{{ $index }}" name="photo_upload_{{ $index }}"
                            accept="image/*" class="d-none"
                            onchange="previewImage(this, {{ $index }})">
                    </div>
                </div>

              {{-- Image Preview --}}
<div id="preview_{{ $index }}" class="mt-2" style="display:none;">
    <div style="position:relative; display:inline-block; width:100%;">
        <img id="preview_img_{{ $index }}" src="" alt="Preview"
            style="width:100%; height:auto; border-radius:6px; border:1px solid #dee2e6; display:block;">
        <button type="button"
            onclick="clearImage({{ $index }})"
            style="position:absolute; top:6px; right:6px; background:#dc3545; border:none; color:#fff;
                   width:24px; height:24px; border-radius:50%; cursor:pointer; font-size:14px;
                   display:flex; align-items:center; justify-content:center; line-height:1; padding:0;">
            &times;
        </button>
    </div>
</div>

        </div>
    </div>
</div>
    @endforeach
</div>
    </div>

    {{-- After Trial Checks --}}
    <div class="mb-3">
        <h6 class="fw-700 mb-2" style="font-size:.78rem;color:#1e2a4a;border-bottom:1px solid #e2e8f0;padding-bottom:6px;">After Trial Checks</h6>
        <div class="row g-2">
            @foreach(['Starting','Brakes','Speedometer','Acceleration','Steering','Battery Voltage','Clutch','Gear Shift','Suspension','Vibration/Noise','Tyre Pressure'] as $index => $check)
            <div class="col-md-4">
                <div class="border rounded p-2" style="font-size:.78rem;">
                    <div class="fw-600 mb-1">{{ $check }}</div>
                    <div class="btn-group btn-group-sm w-100">
                        <input type="radio" class="btn-check" name="atc_{{ $index }}" id="atc_ok_{{ $index }}" value="ok">
                        <label class="btn btn-outline-success" for="atc_ok_{{ $index }}"><i class="bx bx-check me-1"></i>OK</label>
                        <input type="radio" class="btn-check" name="atc_{{ $index }}" id="atc_notok_{{ $index }}" value="not_ok">
                        <label class="btn btn-outline-danger" for="atc_notok_{{ $index }}"><i class="bx bx-x me-1"></i>Not OK</label>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>{{-- end card body --}}
</div>{{-- end card --}}

 <div class="card form-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
            <i class="bx bx-box me-2" style="color:#273d80;"></i>Parts Consumed
        </h6>
        <button class="btn btn-sm" id="btnShowAddPart" style="background:#273d80;color:#fff;border-radius:7px;font-size:.75rem;">
            <i class="bx bx-plus me-1"></i>Add Part
        </button>
    </div>
    <div class="card-body p-3">
        <div id="addPartForms"></div>
        <div id="partStockInfo" class="mt-2" style="font-size:.78rem;"></div>

        <div class="d-flex justify-content-end mt-2">
            <div style="font-size:.85rem;"><span class="text-muted">Parts Total: </span><strong id="partsTotal" style="color:#273d80;">TZS 0</strong>
            </div>
        </div>
    </div>
</div>{{-- end card --}}

<div class="card form-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
            <i class="bx bx-time me-2" style="color:#f59e0b;"></i>Labour Charges
        </h6>
        <button class="btn btn-sm" id="btnShowAddLabour" style="background:#273d80;color:#fff;border-radius:7px;font-size:.75rem;">
            <i class="bx bx-plus me-1"></i>Add Labour
        </button>
    </div>
    <div class="card-body p-3">
        <div id="addLabourForms"></div>
        <button id="btnAddLabour" class="btn btn-sm mt-2 mb-3" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;display:none;">
            <i class="bx bx-plus me-1"></i>Add Labour To Job Card
        </button>
    
        <div class="d-flex justify-content-end mt-2">
            <div style="font-size:.85rem;"><span class="text-muted">Labour Total: </span><strong id="labourTotal" style="color:#273d80;">TZS 0</strong>
            </div>
        </div>
    </div> {{--end card body--}}
</div> {{-- end card --}}

{{-- Authorization --}}
<div class="card form-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
            <i class="bx bx-shield-quarter me-2" style="color:#f59e0b;"></i>Authorization
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">

            {{-- Technician --}}
            <div class="col-md-6">
                <label for="technician_id" class="form-label" style="font-size:.8rem;font-weight:600;color:#1e2a4a;">
                    <i class="bx bx-wrench me-1" style="color:#273d80;"></i>Assign Technician
                </label>
                <select class="form-select form-select-sm" id="technician_id" name="technician_id"
                    style="font-size:.8rem;border-radius:7px;">
                    <option value="">— Select Technician —</option>
                    @foreach($technicians ?? [] as $tech)
                        <option value="{{ $tech->id }}"
                            {{ old('technician_id', $jobCard->technician_id ?? '') == $tech->id ? 'selected' : '' }}>
                            {{ $tech->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Supervisor --}}
            <div class="col-md-6">
                <label for="supervisor_id" class="form-label" style="font-size:.8rem;font-weight:600;color:#1e2a4a;">
                    <i class="bx bx-user-check me-1" style="color:#273d80;"></i>Supervisor
                </label>
                <select class="form-select form-select-sm" id="supervisor_id" name="supervisor_id"
                    style="font-size:.8rem;border-radius:7px;">
                    <option value="">— Select Supervisor —</option>
                    @foreach($supervisors ?? [] as $supervisor)
                        <option value="{{ $supervisor->id }}"
                            {{ old('supervisor_id', $jobCard->supervisor_id ?? '') == $supervisor->id ? 'selected' : '' }}>
                            {{ $supervisor->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Job Status --}}
            <div class="col-md-6">
                <label for="job_status" class="form-label" style="font-size:.8rem;font-weight:600;color:#1e2a4a;">
                    <i class="bx bx-loader-circle me-1" style="color:#273d80;"></i>Job Status
                </label>
                <select class="form-select form-select-sm" id="job_status" name="job_status"
                    style="font-size:.8rem;border-radius:7px;">
                    @php
                        $statuses = [
                            'pending'     => 'Pending',
                            'in_progress' => 'In Progress',
                            'completed'   => 'Completed',
                            'delivered'   => 'Delivered',
                        ];
                    @endphp
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}"
                            {{ old('job_status', $jobCard->job_status ?? 'pending') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Internal Notes --}}
            <div class="col-md-6">
                <label for="internal_notes" class="form-label" style="font-size:.8rem;font-weight:600;color:#1e2a4a;">
                    <i class="bx bx-note me-1" style="color:#273d80;"></i>Internal Notes
                </label>
                <textarea class="form-control form-control-sm" id="internal_notes" name="internal_notes"
                    rows="3" placeholder="Add any internal notes or remarks..."
                    style="font-size:.8rem;border-radius:7px;resize:none;">{{ old('internal_notes', $jobCard->internal_notes ?? '') }}</textarea>
            </div>

        </div>
    </div>
</div>

{{-- end authorization --}}

<div class="card form-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
            <i class="bx bx-edit me-2" style="color:#f59e0b;"></i>Digital Signatures & Authorization
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="row g-4">

            {{-- Supervisor Signature --}}
            <div class="col-md-6">
                <p class="mb-2" style="font-size:.8rem;font-weight:600;color:#1e2a4a;">
                    <i class="bx bx-user-check me-1" style="color:#273d80;"></i>Supervisor Authorization
                </p>
                <div class="signature-container" id="supervisorSignatureContainer"
                    style="border:2px dashed #ced4da;border-radius:8px;padding:10px;background:#fafafa;position:relative;">
                    <p class="signature-placeholder text-center text-muted mb-1" id="supervisorSignaturePlaceholder"
                        style="font-size:.75rem;pointer-events:none;">
                        <i class="bx bx-pen me-1"></i>Click to sign
                    </p>
                    <canvas id="supervisorSignatureCanvas" class="signature-canvas"
                        style="width:100%;height:110px;display:block;cursor:crosshair;border-radius:5px;"></canvas>
                    <input type="hidden" name="supervisor_signature_data" id="supervisorSignatureData"
                        value="{{ old('supervisor_signature_data', $jobCard->supervisor_signature_data ?? '') }}">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="signature-status text-muted" id="supervisorSignatureStatus"
                            style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Not signed</small>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSupervisorSignature"
                            style="font-size:.72rem;border-radius:6px;">
                            <i class="bx bx-eraser me-1"></i>Clear
                        </button>
                    </div>
                </div>
                <input type="text" class="form-control form-control-sm mt-2" id="supervisor_name"
                    name="supervisor_name" placeholder="Supervisor full name"
                    value="{{ old('supervisor_name', $jobCard->supervisor_name ?? '') }}"
                    style="font-size:.8rem;border-radius:7px;">
            </div>

            {{-- Customer Consent + Signature --}}
            <div class="col-md-6">
                <p class="mb-2" style="font-size:.8rem;font-weight:600;color:#1e2a4a;">
                    <i class="bx bx-user me-1" style="color:#273d80;"></i>Customer Authorization
                </p>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="customer_consent" name="customer_consent"
                        value="1" {{ old('customer_consent', $jobCard->customer_consent ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="customer_consent" style="font-size:.78rem;color:#374151;">
                        I hereby authorize the repairs of my vehicle as specified above.
                    </label>
                </div>
                <div class="signature-container" id="customerSignatureContainer"
                    style="border:2px dashed #ced4da;border-radius:8px;padding:10px;background:#fafafa;position:relative;">
                    <p class="signature-placeholder text-center text-muted mb-1" id="customerSignaturePlaceholder"
                        style="font-size:.75rem;pointer-events:none;">
                        <i class="bx bx-pen me-1"></i>Click to sign
                    </p>
                    <canvas id="customerSignatureCanvas" class="signature-canvas"
                        style="width:100%;height:110px;display:block;cursor:crosshair;border-radius:5px;"></canvas>
                    <input type="hidden" name="customer_signature_data" id="customerSignatureData"
                        value="{{ old('customer_signature_data', $jobCard->customer_signature_data ?? '') }}">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="signature-status text-muted" id="customerSignatureStatus"
                            style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Not signed</small>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearCustomerSignature"
                            style="font-size:.72rem;border-radius:6px;">
                            <i class="bx bx-eraser me-1"></i>Clear
                        </button>
                    </div>
                </div>
                <input type="text" class="form-control form-control-sm mt-2" id="customer_signed_by"
                    name="customer_signed_by" placeholder="Customer full name"
                    value="{{ old('customer_signed_by', $jobCard->customer_signed_by ?? '') }}"
                    style="font-size:.8rem;border-radius:7px;">
            </div>

            {{-- Gate Pass Signature --}}
          

            {{-- Delivery Certificate + Signature --}}
            
                <div class="row g-2 mt-1">
                    <div class="col-7">
                        <input type="text" class="form-control form-control-sm" id="delivery_customer_name"
                            name="delivery_customer_name" placeholder="Customer full name"
                            value="{{ old('delivery_customer_name', $jobCard->delivery_customer_name ?? '') }}"
                            style="font-size:.8rem;border-radius:7px;">
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control form-control-sm" id="delivery_date"
    name="delivery_date"
    value="{{ old('delivery_date', $jobCard->delivery_date ?? now()->format('Y-m-d')) }}"
    style="font-size:.8rem;border-radius:7px;">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

            {{-- Submit --}}
          {{--  <div class="d-flex gap-2 mt-2">
                <button id="btnCreateJobCard" class="btn flex-grow-1" style="background:#c0172b;color:#fff;border-radius:8px;font-size:.88rem;font-weight:700;padding:10px;">
                    <i class="bx bx-plus-circle me-2"></i>Create Job Card
                </button>
                <a href="{{ route('tvs.job-cards') }}" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.88rem;padding:10px 20px;">Cancel</a>
            </div>
          --}}
            
    <div class="card custom-card">
    <div class="card-body">
        <div class="d-flex justify-content-start gap-3">
            <button id="btnCreateJobCard" class="btn btn-primary btn-lg px-5">
                <i class="bx bx-send me-1"></i>Submit
            </button>

            <button type="button" class="btn btn-outline-danger btn-lg px-4">
                <i class="bx bx-x me-1"></i>Cancel
            </button>
        </div>
    </div>
</div>

      

    </div>
</div>

@push('scripts')
<script>

$('#btnCreateJobCard').on('click', function() {
    console.log('button clicked');
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    const data = {
        // Vehicle
        chassis_number:     $('#vs-chassis').val(),
        engine_number:      $('#vs-engine').val(),
        registration_number: $('#vs-reg').val(),

        // Customer
        customer_name:      $('#customerName').val(),
        customer_phone:     $('#customerPhone').val(),
        customer_email:     $('#customerEmail').val(),
        customer_address:   $('#customerAddress').val(),

        // Job Card
        check_in_date:          $('#checkInDate').val(),
        estimated_delivery_date: $('#estimatedDelivery').val(),
        service_type_id:        $('#serviceTypeId').val(),
        priority:               $('#priority').val(),
        customer_complaints:    $('#complaints').val(),
        odometer_in:            $('#odometerIn').val(),
        fuel_level_in:          $('#fuelLevel').val(),

        // Assignment
        assigned_technician_id: $('#technician_id').val(),
        supervisor_id:          $('#supervisor_id').val(),
        job_status:             $('#job_status').val(),
        supervisor_notes:       $('#internal_notes').val(),

        // Signatures
        supervisor_signature:   $('#supervisorSignatureData').val(),
        customer_signature:     $('#customerSignatureData').val(),
        supervisor_name:        $('#supervisor_name').val(),
        customer_signed_by:     $('#customer_signed_by').val(),

        _token: '{{ csrf_token() }}',
    };

    $.ajax({
        url: '/tvs/job-cards',
        method: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                window.location.href = response.redirect ?? '/tvs/job-cards';
            } else {
                alert(response.message ?? 'Failed to save job card.');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Job Card');
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                alert(Object.values(errors).flat().join('\n'));
            } else {
                alert('Error saving job card.');
            }
            btn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Job Card');
        }
    });
});


 function searchVehicle() {
    const query = $('#vehicleSearch').val().trim();

    if (!query) {
        alert('Please enter a chassis, engine, or registration number.');
        return;
    }

    showVehicleLoading(true);

    $.ajax({
        url: '/tvs/proxy/search-vehicle',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            if (!response.success) {
                alert('No vehicle found.');
                clearVehicleFields();
                return;
            }
            const v = response.vehicle;
            $('#vehicleId').val(v.id);          // ✅ vehicle id
            $('#customerId').val(v.customer_id); // ✅ customer_id from vehicle
            populateVehicleFields(v);
        },
        error: function() {
            alert('Error searching for vehicle.');
        },
        complete: function() {
            showVehicleLoading(false);
        }
    });
}

function populateVehicleFields(v) {
    $('#vs-chassis').val(v.chassis_number ?? '');
    $('#vs-engine').val(v.engine_number ?? '');
    $('#vs-model').val(v.vehicle_model ?? '');
    $('#vs-color').val(v.color ?? '');
    $('#Dealer').val(v.dealer ?? '');
    $('#vs-reg').val(v.registration_number ?? '');
    $('#registrationDate').val(v.registration_date ?? '');
    $('#lastService').val(formatDate(v.last_service_date) ?? '');
    $('#warrantyStatus').val(v.warranty_status ?? '');
    $('#warrantyExpires').val(formatDate(v.warranty_end_date) ?? '');
    $('#purchaseDate').val(formatDate(v.purchase_date) ?? '');
       

    // Make readonly after filling
    $('#vs-chassis, #vs-engine, #vs-model, #vs-color, #Dealer, #registrationNumber, #registrationDate, #lastService, #warrantyStatus, #warrantyExpires')
        .prop('readonly', true);
}

function clearVehicleFields() {
    $('#vs-chassis, #vs-engine, #vs-model, #vs-color, #Dealer, #registrationNumber, #registrationDate, #lastService, #warrantyStatus, #warrantyExpires')
        .val('')
        .prop('readonly', false);
}

function showVehicleLoading(show) {
    if (show) {
        $('button[onclick="searchVehicle()"]').html('<i class="fas fa-spinner fa-spin"></i> Searching...').prop('disabled', true);
    } else {
        $('button[onclick="searchVehicle()"]').html('<i class="fas fa-search"></i> Search').prop('disabled', false);
    }
}

$('#vehicleSearch').on('keypress', function(e) {
    if (e.which === 13) searchVehicle();
});

 // Show add part row
var partRowIndex = 0;
var labourRowIndex = 0;

function newLabourRow(index) {
    return `
    <div class="labour-row mb-2" id="labourRow${index}" style="background:#f8f9fa;border-radius:9px;padding:14px;">
        <div class="row g-2">
            <div class="col-md-8">
                <label class="form-label fw-600" style="font-size:.75rem;">Job to be done <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm labourOpSearch" placeholder="Enter the job...">
               
            </div>
           <!-- <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:.75rem;">Hours <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-sm labourHours" value="1" min="0.25" step="0.25">
            </div> -->
            <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:.75rem;">Amount (TZS)</label>
                <input type="number" class="form-control form-control-sm labourRate" placeholder="0">
            </div>
         <!--   <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:.75rem;">Charge Type</label>
                <select class="form-select form-select-sm labourChargeType">
                    <option value="1">Paid Service</option>
                    <option value="2">1st Free Service</option>
                    <option value="3">2nd Free Service</option>
                    <option value="4">3rd Free Service</option>
  <option value="5">Accidental Repair</option>
    <option value="6">General Repair</option>

                </select>
            </div> 
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Technician</label>
                <select class="form-select form-select-sm labourTechId">
                    <option value="">Select...</option>
                </select>
            </div>  -->
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-sm btn-danger btnRemoveLabourRow w-100" data-row="${index}">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        </div>
    </div>`;
}

// Remove a labour row
$(document).on('click', '.btnRemoveLabourRow', function () {
    $('#labourRow' + $(this).data('row')).remove();
    if ($('.labour-row').length === 0) {
        $('#btnAddLabour').hide();
    }
});

$(document).on('select2:select', '.partId', function (e) {
    var $row = $(this).closest('.part-row');
    var item = e.params.data;

    $row.find('.partCode').val(item.code || '');
    $row.find('.partWarehouse').val(item.warehouse);  
    $row.find('.partUnitPrice').val(item.price);
    $row.find('.partStock').val(item.stock);
    // Remove old warning, reset qty
    $row.find('.stockWarning').remove();
    $row.find('.partQty').removeClass('is-invalid');
    $row.data('available-stock', item.stock);
  
     updatePartsTotal();
});

function initPartSelect2($row) {
    $row.find('.partId').select2({
        
        placeholder: 'Search part name or code...',
        minimumInputLength: 2,
        ajax: {
            url: '/api/tvs/parts/search',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return { search: params.term };
            },
            processResults: function (res) {
                if (!res.success || !res.data) return { results: [] };
                return {
                    results: res.data.map(function (item) {
                        return {
                            id:        item.ItemCode,
                            text:      item.ItemName, 
                            code:      item.ItemCode,
                            name:      item.ItemName,
                            warehouse: item.WhsCode,
                            stock:     item.OnHand,
                            price:     item.Price
                        };
                    })
                };
            },
            cache: true
        }
    });
}

function newPartRow(index) {
    return `
    <div class="part-row mb-2" id="partRow${index}" style="background:#f8f9fa;border-radius:9px;padding:14px;">
        <div class="row g-2">
            <div class="col-md-2">
              <label class="form-label fw-600" style="font-size:.75rem;">Part <span class="text-danger">*</span></label>
              <select class="form-select form-select-sm partId" style="width:100%;">
              <option value="">Search part name or code...</option>
              </select>  
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:.75rem;">Part Code</label>
                <input type="text" class="form-control form-control-sm partCode" readonly style="background:#f0f0f0;">
            </div>
             <div class="col-md-1">
                <label class="form-label fw-600" style="font-size:.75rem;">Warehouse</label>
                <input type="text" class="form-control form-control-sm partWarehouse" readonly style="background:#f0f0f0;">
            </div>
             <div class="col-md-1">
                <label class="form-label fw-600" style="font-size:.75rem;">Stock</label>
                <input type="text" class="form-control form-control-sm partStock" readonly style="background:#f0f0f0;">
            </div>
            <div class="col-md-1">
                <label class="form-label fw-600" style="font-size:.75rem;">Qty <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-sm partQty" value="1" min="1">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:.75rem;">Unit Price <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-sm partUnitPrice" readOnly>
            </div>
             <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:.75rem;">Amount<span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-sm partAmount" readOnly>
            </div>
          <!--  <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:.75rem;">Charge Type</label>
                <select class="form-select form-select-sm partChargeType">
                    <option value="1">Chargeable</option>
                    <option value="2">Warranty</option>
                    <option value="3">Goodwill</option>
                    <option value="4">Campaign</option>
                    <option value="5">Free of Charge (FOC)</option>
                    <option value="6">Customer Usage Feedback Trial (CUFT)</option>
                </select>
            </div> 
            <div class="col-md-3">
                <label class="form-label fw-600" style="font-size:.75rem;">Reason</label>
                <select class="form-select form-select-sm partReason">
                    <option>Replacement</option>
                    <option>Adjustment</option>
                    <option>Repair</option>
                    <option>Preventive</option>
                </select>
            </div>-->
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-sm btn-danger btnRemoveRow w-100" data-row="${index}">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
            <div class="col-md-12">
                <div class="partStockInfo mt-1"></div>
            </div>
        </div>
    </div>`;
}

$('#btnShowAddPart').on('click', function () {
    partRowIndex++;
    $('#addPartForms').append(newPartRow(partRowIndex));
     var $newRow = $('#partRow' + partRowIndex);  
    initPartSelect2($newRow);
   $('#btnAddPart').show();
  
});
// Remove a part row
$(document).on('click', '.btnRemoveRow', function () {
    $('#partRow' + $(this).data('row')).remove();
    if ($('.part-row').length === 0) {
        $('#btnAddPart').hide();
    }
});

// Show add labour row
$('#btnShowAddLabour').on('click', function () {
    labourRowIndex++;
    $('#addLabourForms').append(newLabourRow(labourRowIndex));

  //  let $select = $('#labourRow' + labourRowIndex).find('.labourTechId');
  //  loadTechnicians($select);

    $('#btnAddLabour').show();
});

// Calculate and update parts total
function updatePartsTotal() {
    let total = 0;

    $('#addPartForms .part-row').each(function () {
        const qty    = parseFloat($(this).find('.partQty').val()) || 0;
        const price  = parseFloat($(this).find('.partUnitPrice').val()) || 0;
        const amount = qty * price;

        $(this).find('.partAmount').val(amount.toFixed(2));
        total += amount;
    });

    $('#partsTotal').text('TZS ' + total.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));
}

// Trigger on qty or unit price change
$(document).on('input', '#addPartForms .partQty, #addPartForms .partUnitPrice', function () {
    updatePartsTotal();
});

// Trigger on row removal
$(document).on('click', '.btnRemoveRow', function () {
    setTimeout(updatePartsTotal, 100);
});

function updateLabourTotal() {
    let total = 0;

    $('#addLabourForms .labour-row').each(function () {
        total += parseFloat($(this).find('.labourRate').val()) || 0;
    });

    $('#labourTotal').text('TZS ' + total.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));
}

// Trigger on amount input change
$(document).on('input', '#addLabourForms .labourRate', function () {
    updateLabourTotal();
});

// Trigger on row removal
$(document).on('click', '.btnRemoveLabourRow', function () {
    setTimeout(updateLabourTotal, 100);
});

function toggleNotOkPanel(index, show) {
    const panel = document.getElementById('notok_panel_' + index);
    const card = document.getElementById('card_' + index);

    if (show) {
        panel.style.display = 'block';
        card.style.borderColor = '#dc3545 !important';
        card.style.background = '#fff5f5';
    } else {
        panel.style.display = 'none';
        card.style.background = '';
        // Clear image if switching back to OK
        clearImage(index);
    }
}

function previewImage(input, index) {
    const previewBox = document.getElementById('preview_' + index);
    const previewImg = document.getElementById('preview_img_' + index);

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewBox.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage(index) {
    document.getElementById('capture_' + index).value = '';
    document.getElementById('upload_' + index).value = '';
    document.getElementById('preview_' + index).style.display = 'none';
    document.getElementById('preview_img_' + index).src = '';
}

$(function () {
    var base = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";
    var selectedVehicleId = null;
    var selectedSaleType = '';

    //to uppercase 
    $('#vs-reg, #vs-chassis, #vs-engine').on('input', function() {
    var pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});

//$('#checkInDate').val(now.toISOString().slice(0, 10));
//$('#estimatedDelivery').val(tomorrow.toISOString().slice(0, 10));
console.log($('#checkInDate').attr('type'));
console.log($('#estimatedDelivery').attr('type'));

    // Load service types
    $.get(base + '/vehicles/sale-types', function() {}); // warm up
    // Using a simple AJAX to get service types via a trick
    // Service types come from job-cards create; let's load from the parties endpoint as a proxy
    // Direct inline options since API endpoint for service types is not separate
    //var serviceTypes = ['Free Service', 'Paid Service', 'Warranty Repair', 'Goodwill', 'Campaign'];
    // Try loading from backend
  

    $('#serviceTypeId').on('change', function() {
        var val = $(this).find(':selected').text();
        $('#freeCouponWrap').toggle(val === 'Free');
    });

    // Vehicle search
    $('#btnVehicleSearch, #vs-reg, #vs-chassis, #vs-engine').on('keypress', function(e) {
        if (e.type === 'keypress' && e.which !== 13) return;
        if (e.type === 'keypress') $('#btnVehicleSearch').click();
    });

    $('#btnVehicleSearch').on('click', function() {
        var reg = $('#vs-reg').val().trim();
        var chassis = $('#vs-chassis').val().trim();
        var engine = $('#vs-engine').val().trim();
        if (!reg && !chassis && !engine) { alert('Enter at least one search value.'); return; }

        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Searching...');
        $('#vehicleSnapshot').removeClass('show');
        $('#vehicleNotFound').addClass('d-none');

        $.get(base + '/vehicles/search', { registration_no: reg, chassis_no: chassis, engine_no: engine })
            .done(function(res) {
                if (res.success && res.data) {
                    var v = res.data;
                    selectedVehicleId = v.id;
                    selectedSaleType = v.sale_type || '';
                    $('#vehicleId').val(v.id);

                    if (v.chassis_no) {
                      //  console.log('Chassis no:', v.chassis_no);
                $.get('https://apptest.cargen.co.tz/registrationApp/api/registrations', function(regData) {
                      console.log(regData);
                    var match = regData.find(function(r) {
                        return r.chassis_number === v.chassis_no;
                    });
                    if (match) {
                        console.log(match); 
                        console.log("Hello Doto");
                        $('#custSearch').val(match.registered_to);
                        $.get(base + '/parties', { search: match.registered_to }, function(partyRes) {
                            var $sel = $('#customerPartyId');
                            $sel.html('<option value="">Select customer...</option>');
                            if (partyRes.success && partyRes.data.data.length > 0) {
                                partyRes.data.data.forEach(function(p) {
                                    $sel.append('<option value="' + p.id + '">' + p.name + ' (' + (p.phone || p.party_code) + ')</option>');
                                });
                                $sel.show();
                            }
                        });
                    }
                });
            }

                    $('#snap-model').text((v.model || '') + (v.variant ? ' – ' + v.variant : '') || 'Vehicle');
                    var wHtml = v.warranty_status === 'Active' ? '<span class="badge-active-w">Active</span>' : '<span class="badge-expired-w">' + (v.warranty_status || 'N/A') + '</span>';
                    var typeHtml = '<span style="background:#cfe2ff;color:#084298;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:12px;">' + (v.vehicle_type || '') + '</span>';
                    $('#snap-badges').html(typeHtml + ' ' + (v.is_provisional ? '<span style="background:#fff3cd;color:#856404;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:12px;">Provisional</span>' : ''));
                    $('#snap-reg').text(v.registration_no || '—');
                    $('#snap-chassis').text(v.chassis_no || '—');
                    $('#snap-saletype').text(v.sale_type || '—');
                    $('#snap-owner').text(v.current_owner || 'None – map at first service');
                    $('#snap-warranty').html(wHtml);
                    $('#snap-lastservice').text(v.last_service_date || 'No previous service');
                    $('#vehicleSnapshot').addClass('show');

                    // Show ownership hint
                    showOwnershipHint(v.sale_type);
                    // Pre-fill customer if owner exists
                    if (v.current_owner) {
                        $('#custSearch').val(v.current_owner);
                    }
                    showStandardChecks();
                } else {
                    $('#vehicleNotFound').removeClass('d-none');
                }
            })
            .fail(function() { $('#vehicleNotFound').removeClass('d-none'); })
            .always(function() { $('#btnVehicleSearch').prop('disabled', false).html('<i class="bx bx-search me-1"></i>Search Vehicle'); });
    });

    function showOwnershipHint(saleType) {
        var hints = {
            'Dealer': '<strong>Dealer Sale:</strong> No customer code exists. Please create a new customer using the + button.',
            'RBC': '<strong>Retail Finance (Non-Bank):</strong> Legal owner = Finance Company. Create end customer code and map it.',
            'RFB': '<strong>Retail Finance (Bank):</strong> Search for existing customer code and link service history.',
            'Showroom': '<strong>Showroom Cash:</strong> Retail customer. Service history from Day 1.',
            'Institutional': '<strong>Institutional/Tender:</strong> BillTo = Institution. LPO required. Optional driver code per job card.'
        };
        var hint = '';
        for (var key in hints) { if (saleType && saleType.toLowerCase().includes(key.toLowerCase())) { hint = hints[key]; break; } }
        if (!hint) hint = '<strong>Vehicle found.</strong> Confirm customer and billing party details below.';
        $('#ownershipHint').html(hint).addClass('show');

        // Show LPO for institutional
        var isInst = saleType && saleType.toLowerCase().includes('institution');
        $('#lpoSection').toggle(isInst);
    }

   function showStandardChecks() {

    var standard = [
        'Damages', 'Missing Items', 'Mirrors', 'Seat Cover',
        'Bag Hook', 'Accessories', 'Tool Kit', 'Battery',
        'Fuse', 'Gear Box Oil', 'Fuel Quantity', 'Lights/Horn', 'Wheels/Tyres',
        'Brake Fluid Level', 'Oil Leakage'
    ];

    var afterChecks = [
        'Starting', 'Brakes', 'Speedometer',
        'Acceleration', 'Steering', 'Battery Voltage', 'Clutch',
        'Gear Shift', 'Suspension', 'Vibration/Noise', 'Tyre Pressure'
    ];

    var htmlLeft = '';
    var htmlRight = '';

    standard.forEach(function(c) {
        htmlLeft += '<div style="background:#f8f9fa;border-radius:7px;padding:8px 10px;margin-bottom:4px;font-size:.78rem;">' +
            '<div class="d-flex justify-content-between align-items-center">' +
            '<span class="fw-600">' + c + '</span>' +
            '<div class="d-flex gap-2">' +
            '<label style="font-size:.73rem;cursor:pointer;">' +
            '<input type="radio" name="std_' + c + '" value="OK" class="me-1">OK</label>' +
            '<label style="font-size:.73rem;cursor:pointer;color:#c0172b;">' +
            '<input type="radio" name="std_' + c + '" value="Not OK" class="me-1">Not OK</label>' +
            '</div></div></div>';
    });

    afterChecks.forEach(function(c) {
        htmlRight += '<div style="background:#f8f9fa;border-radius:7px;padding:8px 10px;margin-bottom:4px;font-size:.78rem;">' +
            '<div class="d-flex justify-content-between align-items-center">' +
            '<span class="fw-600">' + c + '</span>' +
            '<div class="d-flex gap-2">' +
            '<label style="font-size:.73rem;cursor:pointer;">' +
            '<input type="radio" name="atc_' + c + '" value="OK" class="me-1">OK</label>' +
            '<label style="font-size:.73rem;cursor:pointer;color:#c0172b;">' +
            '<input type="radio" name="atc_' + c + '" value="Not OK" class="me-1">Not OK</label>' +
            '</div></div></div>';
    });

    var finalHtml = `
        <div class="row">
            <div class="col-md-6">
                <h6>Checks Before Trial</h6>
                ${htmlLeft}
            </div>
            <div class="col-md-6">
                <h6>After Trial Checks</h6>
                ${htmlRight}
            </div>
        </div>
    `;

    $('#checksGrid').html(finalHtml);
    $('#checksCard').show();
}
    
    $('#customerPartyId').on('change', function() {
        var val = $(this).val();
        var text = $(this).find(':selected').text();
        $('#customerPartyIdVal').val(val);
        $('#custResult').text(val ? '✓ Selected: ' + text : '');
        if ($('#billToSame').is(':checked')) {
            updateBillTo(val, text);
        }
    });

    $('#billToSame').on('change', function() {
    if ($(this).is(':checked')) {
        var val = $('#customerPartyIdVal').val();
        var text = $('#custSearch').val();
        $('#billToPartyId').val(text);
        $('#billToPartyIdVal').val(val);
        $('#billToPartyId').prop('disabled', true);
    } else {
        $('#billToPartyId').val('');
        $('#billToPartyIdVal').val('');
        $('#billToPartyId').prop('disabled', false);
    }
});

    function updateBillTo(val, text) {
        $('#billToPartyId').html('<option value="' + val + '">' + text + '</option>').val(val);
    }

   
    // Load parties into bill-to on load
    $.get(base + '/parties', { per_page: 50 }, function(res) {
        if (res.success) {
            res.data.data.forEach(function(p) {
                $('#billToPartyId').append('<option value="' + p.id + '">' + p.name + ' (' + (p.party_type?.name || '') + ')</option>');
            });
        }
    });
var standardChecks = {};
var afterTrialChecks = {};
    // Create Job Card - form submission
    
    // Pre-load vehicle if vehicle_id is in URL
    var urlParams = new URLSearchParams(window.location.search);
    var preVehicleId = urlParams.get('vehicle_id');
    if (preVehicleId) {
        $.get(base + '/vehicles/' + preVehicleId, function(res) {
            if (res.success && res.data) {
                var v = res.data;
                selectedVehicleId = v.id;
                $('#vehicleId').val(v.id);
                $('#vs-reg').val(v.registration_no || '');
                $('#snap-model').text((v.model || '') + (v.variant || ''));
                $('#snap-reg').text(v.registration_no || '—');
                $('#snap-chassis').text(v.chassis_no || '—');
                $('#snap-saletype').text(v.sale_type?.name || '—');
                $('#snap-owner').text(v.current_owner?.name || 'None');
                $('#snap-warranty').html(v.warranties?.length ? '<span class="badge-active-w">On file</span>' : '<span class="badge-expired-w">None</span>');
                $('#snap-lastservice').text('—');
                $('#vehicleSnapshot').addClass('show');
                showOwnershipHint(v.sale_type?.name || '');
                showStandardChecks();
            }
        });
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-');
}

function searchCustomer() {
    const phone = $('#customerSearch').val().trim();

    if (!phone) {
        alert('Please enter a phone number to search.');
        return;
    }

    $.ajax({
        url: '{{ route("tvs.customer.search") }}',
        method: 'GET',
        data: { phone: phone },
        success: function(response) {
            if (response.success) {
                const c = response.customer;
                $('#customerName').val(c.first_name + ' ' + c.last_name);
                $('#customerPhone').val(c.phone_number);
                $('#customerEmail').val(c.email);

                let address = [c.address_line1, c.city, c.state]
                    .filter(Boolean).join(', ');
                $('#customerAddress').val(address);
                   $('#customerName, #customerPhone, #customerEmail, #customerAddress')
            .prop('readonly', true);
            } else {
                alert('No customer found with that phone number.');
                clearCustomerFields();
            }
        },
        error: function() {
            alert('Error searching for customer.');
        }
    });
}

function clearCustomerFields() {
    $('#customerName, #customerPhone, #customerEmail, #customerAddress').val('');
}

document.addEventListener('DOMContentLoaded', function () {

flatpickr('#estimatedDelivery', { 
    dateFormat: 'Y-m-d',        // internal format
    altInput: true,
    altFormat: 'd-M-Y',         // ← this gives: 17-Apr-2026
    defaultDate: new Date().fp_incr(1)
});

flatpickr('#delivery_date', {
    altInput: true,
    altFormat: 'd-M-Y',   // 04-May-2026
    dateFormat: 'Y-m-d'
});

    const pads = [
        {
            canvas:      'supervisorSignatureCanvas',
            placeholder: 'supervisorSignaturePlaceholder',
            status:      'supervisorSignatureStatus',
            hidden:      'supervisorSignatureData',
            clearBtn:    'clearSupervisorSignature',
            container:   'supervisorSignatureContainer',
        },
        {
            canvas:      'customerSignatureCanvas',
            placeholder: 'customerSignaturePlaceholder',
            status:      'customerSignatureStatus',
            hidden:      'customerSignatureData',
            clearBtn:    'clearCustomerSignature',
            container:   'customerSignatureContainer',
        },
        {
            canvas:      'gatePassSignatureCanvas',
            placeholder: 'gatePassSignaturePlaceholder',
            status:      'gatePassSignatureStatus',
            hidden:      'gatePassSignatureData',
            clearBtn:    'clearGatePassSignature',
            container:   'gatePassSignatureContainer',
        },
        {
            canvas:      'deliverySignatureCanvas',
            placeholder: 'deliverySignaturePlaceholder',
            status:      'deliverySignatureStatus',
            hidden:      'deliverySignatureData',
            clearBtn:    'clearDeliverySignature',
            container:   'deliverySignatureContainer',
        },
    ];

    pads.forEach(function (cfg) {
        const canvas      = document.getElementById(cfg.canvas);
        const placeholder = document.getElementById(cfg.placeholder);
        const statusEl    = document.getElementById(cfg.status);
        const hiddenInput = document.getElementById(cfg.hidden);
        const clearBtn    = document.getElementById(cfg.clearBtn);
        const container   = document.getElementById(cfg.container);

        if (!canvas) return;

        // Resize canvas to its rendered size
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect  = canvas.getBoundingClientRect();
            canvas.width  = rect.width  * ratio;
            canvas.height = rect.height * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            canvas.style.width  = rect.width  + 'px';
            canvas.style.height = rect.height + 'px';
            pad.clear();
        }

        const pad = new SignaturePad(canvas, {
            penColor:             'rgb(0,0,0)',
            backgroundColor:      'rgba(255,255,255,0)',
            minWidth:             1,
            maxWidth:             3,
            velocityFilterWeight: 0.7,
        });

        setTimeout(resizeCanvas, 100);
        window.addEventListener('resize', () => setTimeout(resizeCanvas, 100));

        // Hide placeholder when signing starts
        pad.addEventListener('beginStroke', function () {
            if (placeholder) placeholder.style.display = 'none';
            container.style.borderColor = '#273d80';
        });

        // Mark as signed when stroke ends
        pad.addEventListener('endStroke', function () {
            if (pad.isEmpty()) return;
            hiddenInput.value          = pad.toDataURL('image/png');
            statusEl.textContent       = 'Signed ✓';
            statusEl.style.color       = '#16a34a';
            container.style.borderColor = '#16a34a';
            container.style.background  = '#f0fdf4';
        });

        // Clear button
        clearBtn.addEventListener('click', function () {
            pad.clear();
            hiddenInput.value           = '';
            statusEl.textContent        = 'Not signed';
            statusEl.style.color        = '#6b7280';
            container.style.borderColor = '#ced4da';
            container.style.background  = '#fafafa';
            if (placeholder) placeholder.style.display = 'block';
        });

        // Restore existing signature on edit view
        const existing = hiddenInput.value;
        if (existing) {
            pad.fromDataURL(existing);
            if (placeholder) placeholder.style.display = 'none';
            statusEl.textContent        = 'Signed ✓';
            statusEl.style.color        = '#16a34a';
            container.style.borderColor = '#16a34a';
            container.style.background  = '#f0fdf4';
        }
    });
});
</script>

@endpush
</x-app-layout>
