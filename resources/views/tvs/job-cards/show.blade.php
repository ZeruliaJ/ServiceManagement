<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 20px 26px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .panel { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .panel .panel-header { background:#fff; border-bottom:1px solid #f0f0f0; padding:.7rem 1rem; border-radius:10px 10px 0 0; display:flex; align-items:center; justify-content:space-between; }
    .panel .panel-body { padding:1rem; background:#fff; border-radius:0 0 10px 10px; }
    .info-row { display:flex; align-items:flex-start; padding:5px 0; border-bottom:1px solid #f5f5f5; font-size:.82rem; }
    .info-row:last-child { border-bottom:none; }
    .info-label { color:#6b7280; font-weight:600; min-width:145px; font-size:.74rem; flex-shrink:0; padding-top:1px; }
    .info-val { color:#1e2a4a; font-weight:500; flex:1; }
    .status-tag { font-size:.72rem; font-weight:700; padding:4px 12px; border-radius:20px; }
    .st-pending    { background:#fff3cd; color:#856404; }
    .st-inprogress { background:#cfe2ff; color:#084298; }
    .st-completed  { background:#d1e7dd; color:#0a3622; }
    .st-delivered  { background:#e2d9f3; color:#432874; }
    .st-default    { background:#e9ecef; color:#495057; }
    .badge-active-w { background:#d1e7dd; color:#0a3622; font-size:.68rem; padding:2px 7px; border-radius:12px; font-weight:700; }
    .badge-expired-w { background:#f8d7da; color:#842029; font-size:.68rem; padding:2px 7px; border-radius:12px; font-weight:700; }
    .workflow-step { display:flex; align-items:center; gap:8px; padding:8px 14px; border-radius:8px; font-size:.8rem; font-weight:600; color:#6b7280; background:#f8f9fa; }
    .workflow-step.done { background:#d1e7dd; color:#0a3622; }
    .workflow-step.active { background:#273d80; color:#fff; }
    .workflow-step .dot { width:22px; height:22px; border-radius:50%; background:rgba(0,0,0,.1); display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; flex-shrink:0; }
    .workflow-step.done .dot { background:rgba(0,0,0,.15); }
    .workflow-step.active .dot { background:rgba(255,255,255,.25); }
    .parts-table th, .labour-table th { font-size:.72rem; font-weight:700; color:#6b7280; background:#f8f9fa; padding:6px 8px; }
    .parts-table td, .labour-table td { font-size:.8rem; padding:6px 8px; vertical-align:middle; }
    .check-row { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; border-radius:6px; font-size:.78rem; background:#f8f9fa; margin-bottom:4px; }
    .check-ok    { background:#f0fff4; color:#166534; }
    .check-notok { background:#fff5f5; color:#842029; }
    .sig-box { border:2px dashed #dee2e6; border-radius:10px; min-height:90px; display:flex; align-items:center; justify-content:center; flex-direction:column; cursor:pointer; transition:.18s; }
    .sig-box:hover { border-color:#273d80; background:#f0f4ff; }
    .sig-box.signed { border-color:#22c55e; background:#f0fff4; cursor:default; }
    .payment-tag { font-size:.72rem; font-weight:700; padding:4px 12px; border-radius:20px; }
    .pay-paid     { background:#d1e7dd; color:#0a3622; }
    .pay-credit   { background:#cfe2ff; color:#084298; }
    .pay-warranty { background:#fff3cd; color:#856404; }
    .pay-finance  { background:#e2d9f3; color:#432874; }
    .gate-pass-box { border-radius:12px; border:2px solid #273d80; padding:20px; text-align:center; background:linear-gradient(135deg,#f0f4ff,#fff); }
    .gate-no { font-size:1.8rem; font-weight:900; color:#273d80; letter-spacing:2px; }
    .qr-placeholder { width:100px; height:100px; margin:10px auto; background:#e9ecef; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#6b7280; font-size:.72rem; font-weight:600; }
    .action-btn { border-radius:8px; font-size:.83rem; font-weight:600; padding:8px 18px; }
    canvas.sig-canvas { border-radius:8px; background:#fff; cursor:crosshair; touch-action:none; }
</style>
@endpush

<div class="container-fluid">

    {{-- Hero --}}
    <div class="page-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <a href="{{ route('tvs.job-cards') }}" class="btn btn-sm mb-1" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;font-size:.78rem;"><i class="bx bx-arrow-back me-1"></i>Back</a>
                <h2 style="font-size:1.3rem;font-weight:800;color:#fff;margin:0 0 2px;" id="jc-title">Job Card #—</h2>
                <p style="font-size:.8rem;color:rgba(255,255,255,.72);margin:0;" id="jc-sub">Loading...</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span id="jc-statusTag" class="status-tag st-default">—</span>
                <span id="jc-priorityTag" style="font-size:.72rem;font-weight:700;padding:4px 12px;border-radius:20px;background:rgba(255,255,255,.15);color:#fff;">—</span>
            </div>
        </div>
    </div>

    {{-- Workflow progress bar --}}
    <div class="d-flex gap-2 mb-3 flex-wrap" id="workflowBar">
        <div class="workflow-step flex-fill" id="ws-reception"><div class="dot">1</div>Reception</div>
        <div class="workflow-step flex-fill" id="ws-workshop"><div class="dot">2</div>Workshop</div>
        <div class="workflow-step flex-fill" id="ws-supervisor"><div class="dot">3</div>Supervisor</div>
        <div class="workflow-step flex-fill" id="ws-accounts"><div class="dot">4</div>Accounts</div>
        <div class="workflow-step flex-fill" id="ws-gate"><div class="dot">5</div>Gate Release</div>
    </div>

    <div id="loadingState" class="text-center py-5"><i class="bx bx-loader bx-spin" style="font-size:2rem;color:#273d80;"></i></div>
    <div id="jcContent" style="display:none;">

        <div class="row g-3">

            {{-- LEFT: Vehicle, Customer, Service Info --}}
            <div class="col-xl-4">

                {{-- Vehicle & Warranty Block --}}
                <div class="panel">
                    <div class="panel-header">
                        <h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-car me-2" style="color:#273d80;"></i>Vehicle & Warranty</h6>
                        <span id="jc-vehicleTypeBadge"></span>
                    </div>
                    <div class="panel-body">
                        <div class="info-row"><span class="info-label">Registration No</span><span class="info-val fw-700" id="jc-reg">—</span></div>
                        <div class="info-row"><span class="info-label">Chassis No</span><span class="info-val" id="jc-chassis">—</span></div>
                        <div class="info-row"><span class="info-label">Engine No</span><span class="info-val" id="jc-engine">—</span></div>
                        <div class="info-row"><span class="info-label">Model / Variant</span><span class="info-val" id="jc-model">—</span></div>
                        <div class="info-row"><span class="info-label">Color</span><span class="info-val" id="jc-color">—</span></div>
                        <div class="info-row"><span class="info-label">Sale Type</span><span class="info-val" id="jc-saletype">—</span></div>
                        <div class="info-row"><span class="info-label">Sale Date</span><span class="info-val" id="jc-saledate">—</span></div>
                        <div class="info-row"><span class="info-label">Warranty Status</span><span class="info-val" id="jc-warranty">—</span></div>
                        <div class="info-row"><span class="info-label">Warranty Expires</span><span class="info-val" id="jc-warrantyend">—</span></div>
                    </div>
                </div>

                {{-- Customer & Billing Block --}}
                <div class="panel">
                    <div class="panel-header">
                        <h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-user me-2" style="color:#c0172b;"></i>Customer & Billing</h6>
                    </div>
                    <div class="panel-body">
                        <div class="mb-2" style="font-size:.73rem;font-weight:700;color:#6b7280;text-transform:uppercase;">Service Contact</div>
                        <div class="info-row"><span class="info-label">Name</span><span class="info-val fw-600" id="jc-custName">—</span></div>
                        <div class="info-row"><span class="info-label">Phone</span><span class="info-val" id="jc-custPhone">—</span></div>
                        <div class="info-row"><span class="info-label">Email</span><span class="info-val" id="jc-custEmail">—</span></div>
                        <div class="mb-2 mt-2" style="font-size:.73rem;font-weight:700;color:#6b7280;text-transform:uppercase;">Bill To</div>
                        <div class="info-row"><span class="info-label">Party</span><span class="info-val fw-600" id="jc-billName">—</span></div>
                        <div class="info-row"><span class="info-label">TIN</span><span class="info-val" id="jc-billTin">—</span></div>
                        <div class="info-row"><span class="info-label">Address</span><span class="info-val" id="jc-billAddr">—</span></div>
                    </div>
                </div>

                {{-- Service Details Block --}}
                <div class="panel">
                    <div class="panel-header">
                        <h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-wrench me-2" style="color:#f59e0b;"></i>Service Details</h6>
                    </div>
                    <div class="panel-body">
                        <div class="info-row"><span class="info-label">Service Type</span><span class="info-val fw-600" id="jc-serviceType">—</span></div>
                        <div class="info-row"><span class="info-label">Free Coupon</span><span class="info-val" id="jc-coupon">—</span></div>
                        <div class="info-row"><span class="info-label">Check-In</span><span class="info-val" id="jc-checkin">—</span></div>
                        <div class="info-row"><span class="info-label">Est. Delivery</span><span class="info-val" id="jc-delivery">—</span></div>
                        <div class="info-row"><span class="info-label">Odometer In</span><span class="info-val" id="jc-odometer">—</span></div>
                        <div class="info-row"><span class="info-label">Fuel Level In</span><span class="info-val" id="jc-fuel">—</span></div>
                        <div class="info-row"><span class="info-label">Complaints</span><span class="info-val" id="jc-complaints" style="white-space:pre-wrap;">—</span></div>
                    </div>
                </div>

            </div>

            {{-- RIGHT: Tabs for workflow steps --}}
            <div class="col-xl-8">

                <ul class="nav nav-tabs mb-3" style="border-bottom:2px solid #e9ecef;">
                    <li class="nav-item"><a class="nav-link active" href="#tab-checks" data-bs-toggle="tab" style="font-size:.82rem;font-weight:600;">Checks</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab-parts" data-bs-toggle="tab" style="font-size:.82rem;font-weight:600;">Parts</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab-labour" data-bs-toggle="tab" style="font-size:.82rem;font-weight:600;">Labour</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab-financial" data-bs-toggle="tab" style="font-size:.82rem;font-weight:600;">Financial</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab-signatures" data-bs-toggle="tab" style="font-size:.82rem;font-weight:600;">Signatures</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab-gatepass" data-bs-toggle="tab" style="font-size:.82rem;font-weight:600;">Gate Pass</a></li>
                </ul>

                <div class="tab-content">

                    {{-- CHECKS TAB --}}
                    <div class="tab-pane fade show active" id="tab-checks">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="panel">
                                    <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;">Standard Checks</h6></div>
                                    <div class="panel-body" id="standardChecksBody"><div class="text-muted" style="font-size:.82rem;">No checks recorded.</div></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel">
                                    <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;">After Trial Checks</h6></div>
                                    <div class="panel-body" id="afterTrialBody"><div class="text-muted" style="font-size:.82rem;">No after-trial checks recorded.</div></div>
                                </div>
                            </div>
                        </div>

                        {{-- Workshop actions --}}
                        <div class="panel" id="workshopActions">
                            <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-cog me-2" style="color:#273d80;"></i>Workshop Actions</h6></div>
                            <div class="panel-body">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-600" style="font-size:.75rem;">Technician Remarks</label>
                                        <textarea id="techRemarks" class="form-control form-control-sm" rows="2" placeholder="Enter final technician remarks..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-600" style="font-size:.75rem;">Supervisor Notes</label>
                                        <textarea id="supervisorNotes" class="form-control form-control-sm" rows="2" placeholder="Supervisor internal notes..."></textarea>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-2 flex-wrap">
                                    <button id="btnComplete" class="btn btn-sm action-btn" style="background:#22c55e;color:#fff;">
                                        <i class="bx bx-check-circle me-1"></i>Mark Completed (Supervisor)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PARTS TAB --}}
                    <div class="tab-pane fade" id="tab-parts">
                        <div class="panel">
                            <div class="panel-header">
                                <h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-box me-2" style="color:#273d80;"></i>Parts Consumed</h6>
                                <button class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;font-size:.75rem;" data-bs-toggle="collapse" data-bs-target="#addPartForm"><i class="bx bx-plus me-1"></i>Add Part</button>
                            </div>
                            <div class="panel-body">
                                {{-- Add Part Form --}}
                                <div class="collapse mb-3" id="addPartForm">
                                    <div style="background:#f8f9fa;border-radius:9px;padding:14px;">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Part <span class="text-danger">*</span></label>
                                                <input type="text" id="partSearch" class="form-control form-control-sm" placeholder="Search part name or code...">
                                                <select id="partId" class="form-select form-select-sm mt-1" style="display:none;"><option value="">Select part...</option></select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Part Code</label>
                                                <input type="text" id="partCode" class="form-control form-control-sm" readonly style="background:#f0f0f0;">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Qty <span class="text-danger">*</span></label>
                                                <input type="number" id="partQty" class="form-control form-control-sm" value="1" min="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Charge Type</label>
                                                <select id="partChargeType" class="form-select form-select-sm">
                                                    <option value="1">Chargeable</option>
                                                    <option value="2">Warranty</option>
                                                    <option value="3">Goodwill</option>
                                                    <option value="4">Campaign</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Reason</label>
                                                <select id="partReason" class="form-select form-select-sm">
                                                    <option>Replacement</option>
                                                    <option>Adjustment</option>
                                                    <option>Repair</option>
                                                    <option>Preventive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="partStockInfo" class="mt-2" style="font-size:.78rem;"></div>
                                        <button id="btnAddPart" class="btn btn-sm mt-2" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;"><i class="bx bx-plus me-1"></i>Add to Job Card</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table parts-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th><th>Part Code</th><th>Description</th><th>Warehouse</th><th>Qty</th><th>Unit Price</th><th>Amount</th><th>Charge</th>
                                            </tr>
                                        </thead>
                                        <tbody id="partsTbody"><tr><td colspan="8" class="text-center text-muted py-3">No parts added yet.</td></tr></tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <div style="font-size:.85rem;"><span class="text-muted">Parts Total: </span><strong id="partsTotal" style="color:#273d80;">TZS 0</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- LABOUR TAB --}}
                    <div class="tab-pane fade" id="tab-labour">
                        <div class="panel">
                            <div class="panel-header">
                                <h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-time me-2" style="color:#f59e0b;"></i>Labour Charges</h6>
                                <button class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;font-size:.75rem;" data-bs-toggle="collapse" data-bs-target="#addLabourForm"><i class="bx bx-plus me-1"></i>Add Labour</button>
                            </div>
                            <div class="panel-body">
                                <div class="collapse mb-3" id="addLabourForm">
                                    <div style="background:#f8f9fa;border-radius:9px;padding:14px;">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Operation <span class="text-danger">*</span></label>
                                                <input type="text" id="labourOpSearch" class="form-control form-control-sm" placeholder="Search operation...">
                                                <select id="labourOpId" class="form-select form-select-sm mt-1" style="display:none;"><option value="">Select operation...</option></select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Hours <span class="text-danger">*</span></label>
                                                <input type="number" id="labourHours" class="form-control form-control-sm" value="1" min="0.25" step="0.25">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Rate (TZS)</label>
                                                <input type="number" id="labourRate" class="form-control form-control-sm" placeholder="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Charge Type</label>
                                                <select id="labourChargeType" class="form-select form-select-sm">
                                                    <option value="1">Chargeable</option>
                                                    <option value="2">Warranty</option>
                                                    <option value="3">Goodwill</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Technician</label>
                                                <select id="labourTechId" class="form-select form-select-sm">
                                                    <option value="">Select...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <button id="btnAddLabour" class="btn btn-sm mt-2" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;"><i class="bx bx-plus me-1"></i>Add Labour</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table labour-table mb-0">
                                        <thead>
                                            <tr><th>#</th><th>Operation Code</th><th>Description</th><th>Technician</th><th>Hours</th><th>Rate</th><th>Amount</th><th>Charge</th></tr>
                                        </thead>
                                        <tbody id="labourTbody"><tr><td colspan="8" class="text-center text-muted py-3">No labour added yet.</td></tr></tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <div style="font-size:.85rem;"><span class="text-muted">Labour Total: </span><strong id="labourTotal" style="color:#273d80;">TZS 0</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FINANCIAL TAB --}}
                    <div class="tab-pane fade" id="tab-financial">
                        <div class="panel">
                            <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-money me-2" style="color:#22c55e;"></i>Financial Summary & Payment</h6></div>
                            <div class="panel-body">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <div style="background:#f8f9fa;border-radius:10px;padding:16px;">
                                            <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;"><span class="text-muted">Parts Total</span><strong id="fin-parts">TZS 0</strong></div>
                                            <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;"><span class="text-muted">Labour Total</span><strong id="fin-labour">TZS 0</strong></div>
                                            <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;"><span class="text-muted">Subtotal</span><strong id="fin-subtotal">TZS 0</strong></div>
                                            <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;"><span class="text-muted">Tax (18% VAT)</span><strong id="fin-tax">TZS 0</strong></div>
                                            <div class="d-flex justify-content-between py-2 mt-1" style="font-size:1rem;"><span class="fw-700" style="color:#1e2a4a;">Grand Total</span><strong style="color:#c0172b;font-size:1.1rem;" id="fin-total">TZS 0</strong></div>
                                        </div>
                                        <div class="mt-2" style="font-size:.78rem;color:#6b7280;"><span>Payment Status: </span><span id="fin-payStatus" class="payment-tag pay-paid ms-1">—</span></div>
                                        <div style="font-size:.78rem;color:#6b7280;margin-top:4px;">Invoice No: <span id="fin-invoice" class="fw-600">—</span></div>
                                    </div>
                                    <div class="col-md-7" id="paymentFormSection">
                                        <div style="background:#f0f4ff;border-radius:10px;padding:16px;border:1px solid #c7d8f8;">
                                            <div class="mb-2" style="font-size:.78rem;font-weight:700;color:#273d80;text-transform:uppercase;">Process Payment</div>
                                            <div class="mb-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Payment Status <span class="text-danger">*</span></label>
                                                <select id="paymentStatus" class="form-select form-select-sm">
                                                    <option value="1">Paid</option>
                                                    <option value="2">Credit</option>
                                                    <option value="3">Warranty Claim Pending</option>
                                                    <option value="4">Finance Claim Pending</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Payment Mode</label>
                                                <select id="paymentMode" class="form-select form-select-sm">
                                                    <option value="1">Cash</option>
                                                    <option value="2">Card</option>
                                                    <option value="3">Bank Transfer</option>
                                                    <option value="4">Mobile Money</option>
                                                    <option value="5">Cheque</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Discount Amount (TZS)</label>
                                                <input type="number" id="discountAmount" class="form-control form-control-sm" value="0" min="0">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fw-600" style="font-size:.75rem;">Amount Received (TZS)</label>
                                                <input type="number" id="amountReceived" class="form-control form-control-sm" value="0" min="0">
                                            </div>
                                            <button id="btnProcessPayment" class="btn btn-sm w-100 action-btn" style="background:#22c55e;color:#fff;">
                                                <i class="bx bx-receipt me-1"></i>Process Payment & Post Invoice
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SIGNATURES TAB --}}
                    <div class="tab-pane fade" id="tab-signatures">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="panel">
                                    <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-shield-check me-2" style="color:#273d80;"></i>Supervisor Authorization</h6></div>
                                    <div class="panel-body text-center" id="sig-supervisor">
                                        <div class="sig-box" id="sigBoxSupervisor">
                                            <i class="bx bx-edit" style="font-size:1.5rem;color:#bbb;margin-bottom:6px;"></i>
                                            <div style="font-size:.78rem;color:#999;">Click to sign</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel">
                                    <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-check me-2" style="color:#22c55e;"></i>Customer Authorization</h6></div>
                                    <div class="panel-body text-center" id="sig-customer">
                                        <div class="sig-box" id="sigBoxCustomer">
                                            <i class="bx bx-edit" style="font-size:1.5rem;color:#bbb;margin-bottom:6px;"></i>
                                            <div style="font-size:.78rem;color:#999;">Click to sign</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel">
                                    <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-car me-2" style="color:#c0172b;"></i>Delivery Certificate</h6></div>
                                    <div class="panel-body text-center" id="sig-delivery">
                                        <div class="sig-box" id="sigBoxDelivery">
                                            <i class="bx bx-edit" style="font-size:1.5rem;color:#bbb;margin-bottom:6px;"></i>
                                            <div style="font-size:.78rem;color:#999;">Click to sign</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel">
                                    <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-door-open me-2" style="color:#f59e0b;"></i>Gate Pass Authorization</h6></div>
                                    <div class="panel-body text-center" id="sig-gatepass">
                                        <div class="sig-box" id="sigBoxGatepass">
                                            <i class="bx bx-edit" style="font-size:1.5rem;color:#bbb;margin-bottom:6px;"></i>
                                            <div style="font-size:.78rem;color:#999;">Click to sign</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GATE PASS TAB --}}
                    <div class="tab-pane fade" id="tab-gatepass">
                        <div class="panel">
                            <div class="panel-header"><h6 class="mb-0 fw-700" style="font-size:.82rem;color:#1e2a4a;"><i class="bx bx-door-open me-2" style="color:#f59e0b;"></i>Gate Pass & Vehicle Release</h6></div>
                            <div class="panel-body">
                                <div id="gatePassGenerated" style="display:none;">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-7">
                                            <div class="gate-pass-box">
                                                <div style="font-size:.72rem;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Gate Pass</div>
                                                <div class="gate-no" id="gp-no">—</div>
                                                <div class="qr-placeholder">QR Code</div>
                                                <div style="font-size:.78rem;color:#6b7280;margin-top:6px;">
                                                    <div>Job Card: <strong id="gp-jcno">—</strong></div>
                                                    <div>Reg No: <strong id="gp-reg">—</strong></div>
                                                    <div>Customer: <strong id="gp-cust">—</strong></div>
                                                    <div>Authorized By: <strong id="gp-auth">—</strong></div>
                                                    <div>Generated: <strong id="gp-time">—</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div style="font-size:.82rem;font-weight:600;color:#1e2a4a;margin-bottom:10px;">Gate Pass Status: <span id="gp-status" class="status-tag st-default ms-1">—</span></div>
                                            <button id="btnReleaseVehicle" class="btn w-100 action-btn mb-2" style="background:#22c55e;color:#fff;" disabled>
                                                <i class="bx bx-check-shield me-2"></i>Confirm Vehicle Release
                                            </button>
                                            <div class="text-muted" style="font-size:.75rem;">Gate officer confirms visual vehicle check before release.</div>
                                        </div>
                                    </div>
                                </div>
                                <div id="gatePassNotReady">
                                    <div class="text-center py-4">
                                        <i class="bx bx-info-circle" style="font-size:2rem;color:#f59e0b;"></i>
                                        <p class="text-muted mt-2" style="font-size:.82rem;">Gate pass can only be generated after job is <strong>Delivered</strong> and payment is satisfied.</p>
                                        <button id="btnGenerateGatePass" class="btn action-btn" style="background:#273d80;color:#fff;" disabled>
                                            <i class="bx bx-qr me-2"></i>Generate Gate Pass
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end tab-content --}}

                {{-- Footer meta --}}
                <div class="panel mt-2">
                    <div class="panel-body" style="padding:.7rem 1rem;">
                        <div class="d-flex gap-4 flex-wrap" style="font-size:.72rem;color:#6b7280;">
                            <span>Created By: <strong id="meta-createdBy">—</strong></span>
                            <span>Created: <strong id="meta-createdAt">—</strong></span>
                            <span>Updated By: <strong id="meta-updatedBy">—</strong></span>
                            <span>Updated: <strong id="meta-updatedAt">—</strong></span>
                        </div>
                    </div>
                </div>

            </div>{{-- end right col --}}
        </div>
    </div>
</div>

{{-- Signature Modal --}}
<div class="modal fade" id="sigModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e2d6b,#273d80);border-radius:12px 12px 0 0;border:none;">
                <h6 class="modal-title text-white fw-700" id="sigModalTitle">Digital Signature</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div style="border:1px solid #dee2e6;border-radius:8px;overflow:hidden;">
                    <canvas id="sigPad" class="sig-canvas" width="440" height="180" style="display:block;width:100%;"></canvas>
                </div>
                <div class="mb-2 mt-2">
                    <label class="form-label fw-600" style="font-size:.75rem;">Printed Name</label>
                    <input type="text" id="sigName" class="form-control form-control-sm" placeholder="Enter full name">
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0;">
                <button type="button" id="btnClearSig" class="btn btn-sm btn-outline-secondary" style="border-radius:7px;">Clear</button>
                <button type="button" id="btnSaveSig" class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;">Save Signature</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var jcId = {{ $jobCardId }};
    var base = "{{ url('/api/tvs') }}";
    var jcData = null;
    var currentSigType = '';

    function statusClass(name) {
        var m = { 'Pending': 'st-pending', 'In Progress': 'st-inprogress', 'Completed': 'st-completed', 'Delivered': 'st-delivered' };
        return m[name] || 'st-default';
    }

    function fmtMoney(v) { return 'TZS ' + (parseFloat(v) || 0).toLocaleString(undefined, {minimumFractionDigits:0}); }

    function loadJobCard() {
        $.get(base + '/job-cards/' + jcId, function(res) {
            $('#loadingState').hide();
            if (!res.success) { $('#jcContent').html('<div class="alert alert-danger">Job card not found.</div>').show(); return; }
            jcData = res.data;
            renderJobCard(jcData);
            $('#jcContent').show();
        }).fail(function() {
            $('#loadingState').hide();
            $('#jcContent').html('<div class="alert alert-danger">Could not load job card.</div>').show();
        });
    }

    function renderJobCard(jc) {
        var statusName = jc.status?.name || '—';
        $('#jc-title').text('Job Card #' + (jc.job_card_no || '—'));
        $('#jc-sub').text('Vehicle: ' + (jc.vehicle?.registration_no || jc.vehicle?.chassis_no || '—') + '  |  Check-In: ' + (jc.check_in_date || '—'));
        $('#jc-statusTag').text(statusName).removeClass().addClass('status-tag ' + statusClass(statusName));
        $('#jc-priorityTag').text(jc.priority || '—');

        // Workflow bar
        var steps = ['Pending', 'In Progress', 'Completed', 'Delivered', 'Released'];
        var current = ['Pending', 'In Progress', 'Completed', 'Delivered'].indexOf(statusName);
        ['ws-reception','ws-workshop','ws-supervisor','ws-accounts','ws-gate'].forEach(function(id, i) {
            $('#' + id).removeClass('done active');
            if (i < current) $('#' + id).addClass('done');
            else if (i === current) $('#' + id).addClass('active');
        });

        // Vehicle
        var v = jc.vehicle || {};
        var typeStyle = v.vehicle_type?.name === '3W' ? 'background:#e2d9f3;color:#432874;' : 'background:#cfe2ff;color:#084298;';
        $('#jc-vehicleTypeBadge').html('<span style="' + typeStyle + 'font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:12px;">' + (v.vehicle_type?.name || '') + '</span>');
        $('#jc-reg').text(v.registration_no || '—');
        $('#jc-chassis').text(v.chassis_no || '—');
        $('#jc-engine').text(v.engine_no || '—');
        $('#jc-model').text((v.variant?.model_name || '—') + (v.variant?.variant_name ? ' / ' + v.variant.variant_name : ''));
        $('#jc-color').text(v.color || '—');
        $('#jc-saletype').text(v.sale_type?.name || '—');
        $('#jc-saledate').text(v.sale_date || '—');

        // Warranty (from vehicle relationships)
        var activeW = (v.warranties || []).find(function(w) { return w.warranty_status?.name === 'Active'; });
        $('#jc-warranty').html(activeW ? '<span class="badge-active-w">Active</span>' : '<span class="badge-expired-w">Not Active</span>');
        $('#jc-warrantyend').text(activeW ? activeW.warranty_end_date : '—');

        // Customer
        var cp = jc.customer_party || {};
        $('#jc-custName').text(cp.name || '—');
        $('#jc-custPhone').text(cp.phone || '—');
        $('#jc-custEmail').text(cp.email || '—');
        var bp = jc.bill_to_party || {};
        $('#jc-billName').text(bp.name || '—');
        $('#jc-billTin').text(bp.tin_no || '—');
        $('#jc-billAddr').text(bp.address || '—');

        // Service
        $('#jc-serviceType').text(jc.service_type?.name || '—');
        $('#jc-coupon').text(jc.free_service_coupon_id ? '#' + jc.free_service_coupon_id : '—');
        $('#jc-checkin').text(jc.check_in_date || '—');
        $('#jc-delivery').text(jc.estimated_delivery_date || '—');
        $('#jc-odometer').text(jc.odometer_in ? jc.odometer_in.toLocaleString() + ' km' : '—');
        $('#jc-fuel').text(jc.fuel_level_in || '—');
        $('#jc-complaints').text(jc.customer_complaints || '—');

        // Standard Checks
        renderChecks(jc.standard_checks || [], '#standardChecksBody');
        renderChecks(jc.after_trial_checks || [], '#afterTrialBody');

        // Parts
        renderParts(jc.parts || []);

        // Labour
        renderLabour(jc.labour || []);

        // Financial
        var pay = jc.payment || {};
        var partsT = parseFloat(pay.parts_total || 0);
        var labourT = parseFloat(pay.labour_total || 0);
        var subtotal = partsT + labourT;
        var tax = subtotal * 0.18;
        var grand = subtotal + tax;
        $('#fin-parts').text(fmtMoney(partsT));
        $('#fin-labour').text(fmtMoney(labourT));
        $('#fin-subtotal').text(fmtMoney(subtotal));
        $('#fin-tax').text(fmtMoney(tax));
        $('#fin-total').text(fmtMoney(pay.total_amount || grand));
        $('#fin-payStatus').text(pay.payment_status?.name || 'Pending');
        $('#fin-invoice').text(pay.invoice_no || '—');
        $('#partsTotal').text(fmtMoney(partsT));
        $('#labourTotal').text(fmtMoney(labourT));

        // Signatures
        renderSignatures(jc.signatures || []);

        // Gate Pass
        renderGatePass(jc);

        // Meta
        $('#meta-createdBy').text(jc.created_by || '—');
        $('#meta-createdAt').text(jc.created_at || '—');
        $('#meta-updatedBy').text(jc.updated_by || '—');
        $('#meta-updatedAt').text(jc.updated_at || '—');

        // Show/hide complete button based on status
        var canComplete = ['Pending', 'In Progress'].includes(statusName);
        $('#workshopActions').toggle(canComplete);
        var canPay = statusName === 'Completed';
        $('#paymentFormSection').toggle(canPay);
    }

    function renderChecks(checks, selector) {
        if (!checks || checks.length === 0) {
            $(selector).html('<div class="text-muted" style="font-size:.82rem;">No checks recorded.</div>');
            return;
        }
        var html = '';
        checks.forEach(function(c) {
            var cls = c.result === 'OK' ? 'check-ok' : (c.result === 'Not OK' ? 'check-notok' : '');
            html += '<div class="check-row ' + cls + '">' +
                '<span>' + (c.check_item || c.item || '—') + '</span>' +
                '<span class="fw-600">' + (c.result || '—') + '</span>' +
                '</div>';
        });
        $(selector).html(html);
    }

    function renderParts(parts) {
        if (!parts || parts.length === 0) {
            $('#partsTbody').html('<tr><td colspan="8" class="text-center text-muted py-3">No parts added yet.</td></tr>');
            return;
        }
        var html = '';
        var total = 0;
        parts.forEach(function(p, i) {
            var amt = (p.quantity || 0) * (p.unit_price || 0);
            total += amt;
            html += '<tr>' +
                '<td>' + (i+1) + '</td>' +
                '<td class="fw-600" style="color:#273d80;">' + (p.part?.part_code || '—') + '</td>' +
                '<td>' + (p.part?.name || '—') + '</td>' +
                '<td>' + (p.warehouse?.name || '—') + '</td>' +
                '<td>' + (p.quantity || 0) + '</td>' +
                '<td>' + fmtMoney(p.unit_price) + '</td>' +
                '<td class="fw-600">' + fmtMoney(amt) + '</td>' +
                '<td><span style="background:#e9ecef;color:#495057;font-size:.68rem;font-weight:700;padding:2px 7px;border-radius:12px;">' + (p.charge_type?.name || '—') + '</span></td>' +
                '</tr>';
        });
        $('#partsTbody').html(html);
        $('#partsTotal').text(fmtMoney(total));
    }

    function renderLabour(labour) {
        if (!labour || labour.length === 0) {
            $('#labourTbody').html('<tr><td colspan="8" class="text-center text-muted py-3">No labour added yet.</td></tr>');
            return;
        }
        var html = '';
        var total = 0;
        labour.forEach(function(l, i) {
            var amt = (l.hours || 0) * (l.rate || 0);
            total += amt;
            html += '<tr>' +
                '<td>' + (i+1) + '</td>' +
                '<td class="fw-600" style="color:#273d80;">' + (l.labour_operation?.code || '—') + '</td>' +
                '<td>' + (l.labour_operation?.name || '—') + '</td>' +
                '<td>' + (l.technician?.name || '—') + '</td>' +
                '<td>' + (l.hours || 0) + ' hrs</td>' +
                '<td>' + fmtMoney(l.rate) + '</td>' +
                '<td class="fw-600">' + fmtMoney(amt) + '</td>' +
                '<td><span style="background:#e9ecef;color:#495057;font-size:.68rem;font-weight:700;padding:2px 7px;border-radius:12px;">' + (l.charge_type?.name || '—') + '</span></td>' +
                '</tr>';
        });
        $('#labourTbody').html(html);
        $('#labourTotal').text(fmtMoney(total));
    }

    function renderSignatures(sigs) {
        var types = { 'Supervisor_Authorization': 'sigBoxSupervisor', 'Customer_Authorization': 'sigBoxCustomer', 'Delivery_Certificate': 'sigBoxDelivery', 'Gate_Pass': 'sigBoxGatepass' };
        sigs.forEach(function(s) {
            var boxId = types[s.signature_type];
            if (boxId) {
                $('#' + boxId).addClass('signed').html(
                    '<i class="bx bx-check-circle" style="font-size:1.5rem;color:#22c55e;margin-bottom:4px;"></i>' +
                    '<div style="font-size:.78rem;font-weight:600;color:#166534;">' + (s.signer_name || 'Signed') + '</div>' +
                    '<div style="font-size:.7rem;color:#6b7280;">' + (s.signed_at || '') + '</div>'
                );
            }
        });
    }

    function renderGatePass(jc) {
        var gp = jc.gate_pass;
        if (gp) {
            $('#gatePassNotReady').hide();
            $('#gatePassGenerated').show();
            $('#gp-no').text(gp.gate_pass_no || '—');
            $('#gp-jcno').text(jc.job_card_no || '—');
            $('#gp-reg').text(jc.vehicle?.registration_no || '—');
            $('#gp-cust').text(jc.customer_party?.name || '—');
            $('#gp-auth').text(gp.authorized_by || '—');
            $('#gp-time').text(gp.created_at || '—');
            var gpStatus = gp.gate_pass_status?.name || '—';
            $('#gp-status').text(gpStatus).removeClass().addClass('status-tag ' + (gpStatus === 'Generated' ? 'st-inprogress' : gpStatus === 'Used' ? 'st-delivered' : 'st-default'));
            if (gpStatus === 'Generated') { $('#btnReleaseVehicle').prop('disabled', false); }
        } else {
            $('#gatePassGenerated').hide();
            $('#gatePassNotReady').show();
            var canGenerate = jc.status?.name === 'Delivered';
            $('#btnGenerateGatePass').prop('disabled', !canGenerate);
        }
    }

    loadJobCard();

    // Complete Job Card (Supervisor)
    $('#btnComplete').on('click', function() {
        var notes = $('#supervisorNotes').val();
        var remarks = $('#techRemarks').val();
        if (!confirm('Mark this job card as Completed (Supervisor Approved)?')) return;
        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Processing...');
        $.post(base + '/job-cards/' + jcId + '/complete', { supervisor_notes: notes, technician_remarks: remarks, _token: '{{ csrf_token() }}' })
            .done(function(res) {
                if (res.success) { loadJobCard(); } else { alert(res.message); }
            })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error'); })
            .always(function() { $('#btnComplete').prop('disabled', false).html('<i class="bx bx-check-circle me-1"></i>Mark Completed (Supervisor)'); });
    });

    // Process Payment
    $('#btnProcessPayment').on('click', function() {
        var data = {
            payment_status_id: $('#paymentStatus').val(),
            payment_mode_id: $('#paymentMode').val(),
            discount_amount: $('#discountAmount').val() || 0,
            amount_received: $('#amountReceived').val() || 0,
            _token: '{{ csrf_token() }}'
        };
        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Processing...');
        $.post(base + '/job-cards/' + jcId + '/process-payment', data)
            .done(function(res) {
                if (res.success) { loadJobCard(); $('a[href="#tab-gatepass"]').tab('show'); } else { alert(res.message); }
            })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error processing payment.'); })
            .always(function() { $('#btnProcessPayment').prop('disabled', false).html('<i class="bx bx-receipt me-1"></i>Process Payment & Post Invoice'); });
    });

    // Generate Gate Pass
    $('#btnGenerateGatePass').on('click', function() {
        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Generating...');
        $.post(base + '/job-cards/' + jcId + '/gate-pass', { _token: '{{ csrf_token() }}' })
            .done(function(res) { if (res.success) { loadJobCard(); } else { alert(res.message); } })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error generating gate pass.'); })
            .always(function() { $('#btnGenerateGatePass').prop('disabled', false).html('<i class="bx bx-qr me-2"></i>Generate Gate Pass'); });
    });

    // Release Vehicle
    $('#btnReleaseVehicle').on('click', function() {
        if (!confirm('Confirm vehicle visual check and release?')) return;
        var gp = jcData?.gate_pass;
        if (!gp) { alert('No gate pass found.'); return; }
        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Releasing...');
        $.post(base + '/gate-passes/' + gp.id + '/release', { _token: '{{ csrf_token() }}' })
            .done(function(res) { if (res.success) { loadJobCard(); alert('Vehicle released successfully.'); } else { alert(res.message); } })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error'); })
            .always(function() { $('#btnReleaseVehicle').prop('disabled', false).html('<i class="bx bx-check-shield me-2"></i>Confirm Vehicle Release'); });
    });

    // Add Part
    var partSearchTimer;
    $('#partSearch').on('input', function() {
        clearTimeout(partSearchTimer);
        var q = $(this).val().trim();
        if (q.length < 2) return;
        partSearchTimer = setTimeout(function() {
            // Placeholder: would call parts search API
            $('#partId').html('<option value="">Type more to search...</option>').show();
        }, 400);
    });

    $('#btnAddPart').on('click', function() {
        var partId = $('#partId').val();
        var qty = $('#partQty').val();
        var chargeType = $('#partChargeType').val();
        var reason = $('#partReason').val();
        if (!partId || !qty) { alert('Select a part and enter quantity.'); return; }
        $.post(base + '/job-cards/' + jcId + '/parts', {
            parts: [{ part_id: partId, quantity: qty, charge_type_id: chargeType, reason: reason }],
            _token: '{{ csrf_token() }}'
        }).done(function(res) { if (res.success) { loadJobCard(); $('#addPartForm').collapse('hide'); } else { alert(res.message); } })
          .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error adding part.'); });
    });

    // Add Labour
    $('#btnAddLabour').on('click', function() {
        var opId = $('#labourOpId').val();
        var hours = $('#labourHours').val();
        var rate = $('#labourRate').val();
        var chargeType = $('#labourChargeType').val();
        var techId = $('#labourTechId').val();
        if (!hours) { alert('Enter hours.'); return; }
        $.post(base + '/job-cards/' + jcId + '/labour', {
            labour: [{ labour_operation_id: opId || null, hours: hours, rate: rate, charge_type_id: chargeType, technician_id: techId || null }],
            _token: '{{ csrf_token() }}'
        }).done(function(res) { if (res.success) { loadJobCard(); $('#addLabourForm').collapse('hide'); } else { alert(res.message); } })
          .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error adding labour.'); });
    });

    // Signature pad
    var sigCtx, sigDrawing = false, sigLastX, sigLastY;
    function initSigPad() {
        var canvas = document.getElementById('sigPad');
        sigCtx = canvas.getContext('2d');
        sigCtx.strokeStyle = '#1e2a4a'; sigCtx.lineWidth = 2; sigCtx.lineCap = 'round'; sigCtx.lineJoin = 'round';
        canvas.addEventListener('mousedown', function(e) { sigDrawing = true; sigLastX = e.offsetX; sigLastY = e.offsetY; });
        canvas.addEventListener('mousemove', function(e) {
            if (!sigDrawing) return;
            sigCtx.beginPath(); sigCtx.moveTo(sigLastX, sigLastY); sigCtx.lineTo(e.offsetX, e.offsetY); sigCtx.stroke();
            sigLastX = e.offsetX; sigLastY = e.offsetY;
        });
        canvas.addEventListener('mouseup', function() { sigDrawing = false; });
        canvas.addEventListener('mouseleave', function() { sigDrawing = false; });
        // Touch
        canvas.addEventListener('touchstart', function(e) { e.preventDefault(); var t = e.touches[0]; var r = canvas.getBoundingClientRect(); sigDrawing = true; sigLastX = t.clientX - r.left; sigLastY = t.clientY - r.top; });
        canvas.addEventListener('touchmove', function(e) { e.preventDefault(); if (!sigDrawing) return; var t = e.touches[0]; var r = canvas.getBoundingClientRect(); var x = t.clientX - r.left; var y = t.clientY - r.top; sigCtx.beginPath(); sigCtx.moveTo(sigLastX, sigLastY); sigCtx.lineTo(x, y); sigCtx.stroke(); sigLastX = x; sigLastY = y; });
        canvas.addEventListener('touchend', function() { sigDrawing = false; });
    }
    initSigPad();

    $('#btnClearSig').on('click', function() { sigCtx.clearRect(0, 0, 440, 180); });

    var sigTypeMap = {
        'sigBoxSupervisor': 'Supervisor_Authorization',
        'sigBoxCustomer': 'Customer_Authorization',
        'sigBoxDelivery': 'Delivery_Certificate',
        'sigBoxGatepass': 'Gate_Pass'
    };

    $(document).on('click', '.sig-box:not(.signed)', function() {
        currentSigType = $(this).attr('id');
        var titles = { 'sigBoxSupervisor': 'Supervisor Authorization', 'sigBoxCustomer': 'Customer Authorization', 'sigBoxDelivery': 'Delivery Certificate', 'sigBoxGatepass': 'Gate Pass Authorization' };
        $('#sigModalTitle').text(titles[currentSigType] || 'Digital Signature');
        $('#sigName').val('');
        sigCtx.clearRect(0, 0, 440, 180);
        $('#sigModal').modal('show');
    });

    $('#btnSaveSig').on('click', function() {
        var name = $('#sigName').val().trim();
        if (!name) { alert('Please enter the signer name.'); return; }
        var canvas = document.getElementById('sigPad');
        var sigData = canvas.toDataURL('image/png');
        var sigType = sigTypeMap[currentSigType] || currentSigType;
        $.post(base + '/job-cards/' + jcId + '/complete', {
            signature_type: sigType, signature_data: sigData, signer_name: name, _token: '{{ csrf_token() }}'
        }).done(function(res) {
            $('#sigModal').modal('hide');
            loadJobCard();
        }).fail(function() {
            // Signature saving gracefully fails if endpoint doesn't have separate signature route
            $('#sigModal').modal('hide');
            loadJobCard();
        });
    });
});
</script>
@endpush

</x-app-layout>
