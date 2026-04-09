<x-app-layout :title="$title">

@push('css')
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
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">New Job Card – Reception</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">Step 1: Vehicle & customer validation → Step 2: Service details → Step 3: Assign technician</p>
    </div>

    <div class="row g-3">

        {{-- Left column: Vehicle Search + Customer --}}
        <div class="col-xl-5">

            {{-- Step 1: Vehicle Search --}}
            <div class="card form-card">
                <div class="card-header">
                    <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
                        <span class="step-badge">1</span>Vehicle Search & Validation
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:.75rem;">Registration No</label>
                            <input type="text" id="vs-reg" class="form-control form-control-sm" placeholder="T123ABC" value="{{ request('vehicle_id') ? '' : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:.75rem;">Chassis No</label>
                            <input type="text" id="vs-chassis" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:.75rem;">Engine No</label>
                            <input type="text" id="vs-engine" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="mt-2 d-flex gap-2">
                        <button id="btnVehicleSearch" class="btn btn-sm w-100" style="background:#273d80;color:#fff;border-radius:7px;font-size:.8rem;"><i class="bx bx-search me-1"></i>Search Vehicle</button>
                    </div>

                    <div id="vehicleSnapshot" class="vehicle-snapshot">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong style="font-size:.85rem;color:#1e2a4a;" id="snap-model">—</strong>
                            <div id="snap-badges"></div>
                        </div>
                        <div class="snap-item"><span class="snap-label">Reg No</span><span class="snap-val" id="snap-reg">—</span></div>
                        <div class="snap-item"><span class="snap-label">Chassis</span><span class="snap-val" id="snap-chassis">—</span></div>
                        <div class="snap-item"><span class="snap-label">Sale Type</span><span class="snap-val" id="snap-saletype">—</span></div>
                        <div class="snap-item"><span class="snap-label">Current Owner</span><span class="snap-val" id="snap-owner">—</span></div>
                        <div class="snap-item"><span class="snap-label">Warranty</span><span class="snap-val" id="snap-warranty">—</span></div>
                        <div class="snap-item"><span class="snap-label">Last Service</span><span class="snap-val" id="snap-lastservice">—</span></div>
                        <input type="hidden" id="vehicleId">
                    </div>

                    <div id="vehicleNotFound" class="alert alert-warning mt-2 d-none" style="border-radius:8px;font-size:.8rem;">
                        <i class="bx bx-error-circle me-1"></i>Vehicle not found. <a href="{{ route('tvs.vehicles') }}" class="fw-600">Register provisional?</a>
                    </div>
                </div>
            </div>

            {{-- Step 2: Customer / Party --}}
            <div class="card form-card">
                <div class="card-header">
                    <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
                        <span class="step-badge">2</span>Customer & Billing Party
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div id="ownershipHint" class="ownership-hint"></div>

                    <div class="mb-2">
                        <label class="form-label fw-600" style="font-size:.75rem;">Service Contact (Customer) <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <input type="text" id="custSearch" class="form-control form-control-sm" placeholder="Search by name or phone...">
                            <button id="btnCustSearch" class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;white-space:nowrap;">Search</button>
                            <a href="{{ route('tvs.parties.create') }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius:7px;font-size:.78rem;white-space:nowrap;"><i class="bx bx-plus"></i></a>
                        </div>
                        <select id="customerPartyId" class="form-select form-select-sm mt-1" style="display:none;">
                            <option value="">Select customer...</option>
                        </select>
                        <input type="hidden" id="customerPartyIdVal">
                        <div id="custResult" style="font-size:.78rem;color:#22c55e;margin-top:4px;"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-600" style="font-size:.75rem;">Bill To Party <span class="text-danger">*</span></label>
                        <div class="form-check mb-1" style="font-size:.78rem;">
                            <input class="form-check-input" type="checkbox" id="billToSame">
                            <label class="form-check-label" for="billToSame">Same as service contact</label>
                        </div>
                        <select id="billToPartyId" class="form-select form-select-sm">
                            <option value="">Select billing party...</option>
                        </select>
                    </div>

                    <div id="lpoSection" style="display:none;">
                        <div class="mb-2">
                            <label class="form-label fw-600" style="font-size:.75rem;">LPO Number <span class="text-danger">*</span></label>
                            <input type="text" id="lpoNo" class="form-control form-control-sm" placeholder="Local Purchase Order number">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right column: Service Details --}}
        <div class="col-xl-7">

            {{-- Step 3: Service Details --}}
            <div class="card form-card">
                <div class="card-header">
                    <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;">
                        <span class="step-badge">3</span>Service Details
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.75rem;">Service Type <span class="text-danger">*</span></label>
                            <select id="serviceTypeId" class="form-select form-select-sm">
                                <option value="">Select service type...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.75rem;">Priority <span class="text-danger">*</span></label>
                            <select id="priority" class="form-select form-select-sm">
                                <option value="Normal">Normal</option>
                                <option value="Urgent">Urgent</option>
                                <option value="Emergency">Emergency</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="freeCouponWrap" style="display:none;">
                            <label class="form-label fw-600" style="font-size:.75rem;">Free Service Coupon No</label>
                            <input type="text" id="freeCoupon" class="form-control form-control-sm" placeholder="Coupon number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.75rem;">Check-In Date & Time</label>
                            <input type="datetime-local" id="checkInDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.75rem;">Estimated Delivery <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="estimatedDelivery" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:.75rem;">Odometer In (km)</label>
                            <input type="number" id="odometerIn" class="form-control form-control-sm" placeholder="e.g. 12500">
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-12">
                            <label class="form-label fw-600" style="font-size:.75rem;">Customer Complaints / Requirements <span class="text-danger">*</span></label>
                            <textarea id="complaints" class="form-control form-control-sm" rows="3" placeholder="Describe customer complaints, issues, and service requirements..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Standard Checks preload preview --}}
            <div class="card form-card" id="checksCard" style="display:none;">
                <div class="card-header">
                    <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;"><i class="bx bx-list-check me-2" style="color:#22c55e;"></i>Standard Checks (Pre-loaded)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2" id="checksGrid"></div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-flex gap-2 mt-2">
                <button id="btnCreateJobCard" class="btn flex-grow-1" style="background:#c0172b;color:#fff;border-radius:8px;font-size:.88rem;font-weight:700;padding:10px;">
                    <i class="bx bx-plus-circle me-2"></i>Create Job Card
                </button>
                <a href="{{ route('tvs.job-cards') }}" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.88rem;padding:10px 20px;">Cancel</a>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
$(function () {
    var base = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";
    var selectedVehicleId = null;
    var selectedSaleType = '';

    // Set default datetime
    var now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    $('#checkInDate').val(now.toISOString().slice(0, 16));
    var def = new Date(now);
    def.setDate(def.getDate() + 1);
    def.setMinutes(def.getMinutes() - def.getTimezoneOffset());
    $('#estimatedDelivery').val(def.toISOString().slice(0, 16));

    // Load service types
    $.get(base + '/vehicles/sale-types', function() {}); // warm up
    // Using a simple AJAX to get service types via a trick
    // Service types come from job-cards create; let's load from the parties endpoint as a proxy
    // Direct inline options since API endpoint for service types is not separate
    var serviceTypes = ['Free Service', 'Paid Service', 'Warranty Repair', 'Goodwill', 'Campaign'];
    // Try loading from backend
    $.get(base + '/job-cards?per_page=1', function(res) {}).always(function() {
        // Fallback: add hardcoded options (API doesn't have a separate service-types endpoint in current impl)
        ['Free', 'Paid', 'Warranty', 'Goodwill', 'Campaign'].forEach(function(t, i) {
            $('#serviceTypeId').append('<option value="' + (i+1) + '">' + t + '</option>');
        });
    });

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
        var checks = ['Engine Oil Level', 'Brake Fluid', 'Chain/Belt Tension', 'Tyre Pressure (Front)', 'Tyre Pressure (Rear)', 'Battery', 'Lights (Head)', 'Lights (Tail)', 'Horn', 'Mirrors', 'Handle Grip', 'Brake (Front)', 'Brake (Rear)', 'Air Filter', 'Fuel Level', 'Clutch Play'];
        var html = '';
        checks.forEach(function(c, i) {
            html += '<div class="col-md-6"><div style="background:#f8f9fa;border-radius:7px;padding:8px 10px;margin-bottom:4px;font-size:.78rem;">' +
                '<div class="d-flex justify-content-between align-items-center">' +
                '<span class="fw-600">' + c + '</span>' +
                '<div class="d-flex gap-2">' +
                '<label style="font-size:.73rem;cursor:pointer;"><input type="radio" name="chk_' + i + '" value="OK" class="me-1">OK</label>' +
                '<label style="font-size:.73rem;cursor:pointer;color:#c0172b;"><input type="radio" name="chk_' + i + '" value="Not OK" class="me-1">Not OK</label>' +
                '</div></div></div></div>';
        });
        $('#checksGrid').html(html);
        $('#checksCard').show();
    }

    // Customer search
    $('#btnCustSearch').on('click', function() {
        var q = $('#custSearch').val().trim();
        if (!q) return;
        $.get(base + '/parties', { search: q }, function(res) {
            var $sel = $('#customerPartyId');
            $sel.html('<option value="">Select customer...</option>');
            if (res.success && res.data.data.length > 0) {
                res.data.data.forEach(function(p) {
                    $sel.append('<option value="' + p.id + '">' + p.name + ' (' + (p.phone || p.party_code) + ')</option>');
                });
                $sel.show();
            } else {
                alert('No parties found. Use + to create new customer.');
            }
        });
    });

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
            var text = $('#customerPartyId').find(':selected').text();
            updateBillTo(val, text);
            $('#billToPartyId').prop('disabled', true);
        } else {
            $('#billToPartyId').prop('disabled', false).val('');
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

    // Create Job Card
    $('#btnCreateJobCard').on('click', function() {
        var vehicleId = $('#vehicleId').val();
        var custId = $('#customerPartyIdVal').val() || $('#customerPartyId').val();
        var billToId = $('#billToPartyId').val();
        var serviceType = $('#serviceTypeId').val();
        var delivery = $('#estimatedDelivery').val();
        var priority = $('#priority').val();
        var complaints = $('#complaints').val().trim();

        if (!vehicleId) { alert('Please search and select a vehicle first.'); return; }
        if (!custId) { alert('Please select a service contact (customer).'); return; }
        if (!billToId) { alert('Please select a billing party.'); return; }
        if (!serviceType) { alert('Please select a service type.'); return; }
        if (!delivery) { alert('Please set estimated delivery date.'); return; }
        if (!complaints) { alert('Please enter customer complaints / requirements.'); return; }

        var data = {
            vehicle_id: vehicleId,
            customer_party_id: custId,
            bill_to_party_id: billToId,
            service_type_id: serviceType,
            check_in_date: $('#checkInDate').val() || null,
            odometer_in: $('#odometerIn').val() || null,
            fuel_level_in: $('#fuelLevel').val() || null,
            customer_complaints: complaints,
            estimated_delivery_date: delivery,
            priority: priority,
            _token: '{{ csrf_token() }}'
        };

        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-2"></i>Creating...');

        $.post(base + '/job-cards', data)
            .done(function(res) {
                if (res.success) {
                    window.location.href = webBase + '/job-cards/' + res.data.id;
                } else {
                    alert(res.message || 'Error creating job card.');
                    $('#btnCreateJobCard').prop('disabled', false).html('<i class="bx bx-plus-circle me-2"></i>Create Job Card');
                }
            })
            .fail(function(xhr) {
                var msg = xhr.responseJSON?.message || 'Error creating job card.';
                if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                alert(msg);
                $('#btnCreateJobCard').prop('disabled', false).html('<i class="bx bx-plus-circle me-2"></i>Create Job Card');
            });
    });

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
@endpush

</x-app-layout>
