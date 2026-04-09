<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .search-card { border-radius: 10px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
    .badge-provisional { background: #fff3cd; color: #856404; font-size: .7rem; padding: 3px 9px; border-radius: 20px; font-weight: 700; }
    .badge-active-w { background: #d1e7dd; color: #0a3622; font-size: .7rem; padding: 3px 9px; border-radius: 20px; font-weight: 700; }
    .badge-expired-w { background: #f8d7da; color: #842029; font-size: .7rem; padding: 3px 9px; border-radius: 20px; font-weight: 700; }
    .badge-2w { background: #cfe2ff; color: #084298; font-size: .7rem; padding: 3px 9px; border-radius: 20px; font-weight: 700; }
    .badge-3w { background: #e2d9f3; color: #432874; font-size: .7rem; padding: 3px 9px; border-radius: 20px; font-weight: 700; }
    .search-result-card { border-radius: 10px; border: 1px solid #e2e8f0; padding: 16px 20px; background: #fff; display: none; margin-top: 16px; }
    .search-result-card.show { display: block; }
    .info-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: .83rem; }
    .info-label { color: #6b7280; font-weight: 600; min-width: 140px; font-size: .75rem; }
    .info-val { color: #1e2a4a; font-weight: 500; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <div style="font-size:.75rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">TVS Service</div>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">Vehicle Management</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">Search, register and manage all 2W &amp; 3W vehicles.</p>
    </div>

    {{-- Search Panel --}}
    <div class="card search-card mb-3">
        <div class="card-header" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
            <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-search-alt me-2" style="color:#273d80;"></i>Vehicle Search</h6>
        </div>
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-600" style="font-size:.8rem;color:#374151;">Registration No</label>
                    <input type="text" id="searchReg" class="form-control form-control-sm" placeholder="e.g. T123ABC">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600" style="font-size:.8rem;color:#374151;">Chassis No</label>
                    <input type="text" id="searchChassis" class="form-control form-control-sm" placeholder="e.g. MD625A...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600" style="font-size:.8rem;color:#374151;">Engine No</label>
                    <input type="text" id="searchEngine" class="form-control form-control-sm" placeholder="Engine number">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button id="btnSearch" class="btn btn-sm w-100" style="background:#273d80;color:#fff;border-radius:7px;font-size:.83rem;">
                        <i class="bx bx-search me-1"></i>Search
                    </button>
                    <button id="btnClear" class="btn btn-sm btn-outline-secondary" style="border-radius:7px;font-size:.83rem;">Clear</button>
                </div>
            </div>

            {{-- Search Result --}}
            <div id="searchResult" class="search-result-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-700 mb-1" style="color:#1e2a4a;" id="res-model">—</h6>
                        <div id="res-badges"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <a id="res-viewBtn" href="#" class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;"><i class="bx bx-show me-1"></i>Full Detail</a>
                        <a id="res-jobCardBtn" href="#" class="btn btn-sm" style="background:#c0172b;color:#fff;border-radius:7px;font-size:.78rem;"><i class="bx bx-plus me-1"></i>New Job Card</a>
                    </div>
                </div>
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="info-row"><span class="info-label">Registration No</span><span class="info-val" id="res-reg">—</span></div>
                        <div class="info-row"><span class="info-label">Chassis No</span><span class="info-val" id="res-chassis">—</span></div>
                        <div class="info-row"><span class="info-label">Engine No</span><span class="info-val" id="res-engine">—</span></div>
                        <div class="info-row"><span class="info-label">Color</span><span class="info-val" id="res-color">—</span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row"><span class="info-label">Sale Type</span><span class="info-val" id="res-saletype">—</span></div>
                        <div class="info-row"><span class="info-label">Sale Date</span><span class="info-val" id="res-saledate">—</span></div>
                        <div class="info-row"><span class="info-label">Current Owner</span><span class="info-val" id="res-owner">—</span></div>
                        <div class="info-row"><span class="info-label">Warranty Status</span><span class="info-val" id="res-warranty">—</span></div>
                        <div class="info-row"><span class="info-label">Last Service</span><span class="info-val" id="res-lastservice">—</span></div>
                    </div>
                </div>
            </div>

            <div id="searchNotFound" class="alert alert-warning mt-3 d-none" style="border-radius:8px;font-size:.83rem;">
                <i class="bx bx-info-circle me-2"></i>Vehicle not found.
                <a href="#provisionalModal" data-bs-toggle="modal" class="fw-600">Register as provisional vehicle?</a>
            </div>
        </div>
    </div>

    {{-- Vehicle List Table --}}
    <div class="card search-card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
            <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-list-ul me-2" style="color:#273d80;"></i>All Vehicles</h6>
            <div class="d-flex gap-2">
                <select id="filterSaleType" class="form-select form-select-sm" style="width:160px;border-radius:7px;font-size:.8rem;">
                    <option value="">All Sale Types</option>
                </select>
                <button class="btn btn-sm" style="background:#c0172b;color:#fff;border-radius:7px;font-size:.78rem;" data-bs-toggle="modal" data-bs-target="#provisionalModal">
                    <i class="bx bx-plus me-1"></i>Provisional
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="vehiclesTable" class="table table-hover mb-0" style="font-size:.83rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="px-3 py-2 fw-600 text-muted" style="font-size:.75rem;">#</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Registration No</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Chassis No</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Model / Variant</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Type</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Sale Type</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Status</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vehiclesTbody">
                        <tr><td colspan="8" class="text-center text-muted py-4">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="vehiclesPagination" class="d-flex justify-content-between align-items-center px-3 py-2 border-top" style="font-size:.8rem;display:none!important;"></div>
        </div>
    </div>
</div>

{{-- Provisional Vehicle Modal --}}
<div class="modal fade" id="provisionalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e2d6b,#c0172b);border-radius:12px 12px 0 0;border:none;">
                <h6 class="modal-title text-white fw-700"><i class="bx bx-plus-circle me-2"></i>Register Provisional Vehicle</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning" style="border-radius:8px;font-size:.82rem;"><i class="bx bx-info-circle me-2"></i>Provisional vehicles are flagged for central validation. Warranty claims may be restricted until validated.</div>
                <div class="mb-3">
                    <label class="form-label fw-600" style="font-size:.8rem;">Registration No <span class="text-danger">*</span></label>
                    <input type="text" id="prov-reg" class="form-control form-control-sm" placeholder="T123ABC">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600" style="font-size:.8rem;">Chassis No <span class="text-danger">*</span></label>
                    <input type="text" id="prov-chassis" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600" style="font-size:.8rem;">Engine No <span class="text-danger">*</span></label>
                    <input type="text" id="prov-engine" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600" style="font-size:.8rem;">Vehicle Type <span class="text-danger">*</span></label>
                    <select id="prov-type" class="form-select form-select-sm">
                        <option value="">Select type...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0;">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:7px;">Cancel</button>
                <button type="button" id="btnSaveProvisional" class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;">Save Provisional</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var base = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";

    // Load sale types filter
    $.get(base + '/vehicles/sale-types', function(res) {
        if (res.success) {
            res.data.forEach(function(st) {
                $('#filterSaleType').append('<option value="' + st.id + '">' + st.name + '</option>');
            });
        }
    });

    // Load vehicle types for provisional modal
    $.get(base + '/vehicles/types', function(res) {
        if (res.success) {
            res.data.forEach(function(t) {
                $('#prov-type').append('<option value="' + t.id + '">' + t.name + '</option>');
            });
        }
    });

    // Load vehicles table
    function loadVehicles(page) {
        page = page || 1;
        var saleType = $('#filterSaleType').val();
        var params = { page: page };
        if (saleType) params.sale_type_id = saleType;

        $.get(base + '/vehicles', params, function(res) {
            var $tbody = $('#vehiclesTbody');
            if (!res.success || !res.data.data || res.data.data.length === 0) {
                $tbody.html('<tr><td colspan="8" class="text-center text-muted py-4" style="font-size:.82rem;">No vehicles found.</td></tr>');
                return;
            }
            var html = '';
            res.data.data.forEach(function(v, i) {
                var typeClass = v.vehicle_type?.name === '3W' ? 'badge-3w' : 'badge-2w';
                var statusBadge = v.is_provisional ? '<span class="badge-provisional">Provisional</span>' : '';
                html += '<tr>' +
                    '<td class="px-3 py-2 text-muted" style="font-size:.75rem;">' + ((page-1)*15 + i+1) + '</td>' +
                    '<td class="py-2 fw-600" style="color:#273d80;">' + (v.registration_no || '—') + '</td>' +
                    '<td class="py-2">' + (v.chassis_no || '—') + '</td>' +
                    '<td class="py-2">' + (v.variant?.model_name || '—') + (v.variant?.variant_name ? ' / ' + v.variant.variant_name : '') + '</td>' +
                    '<td class="py-2"><span class="' + typeClass + '">' + (v.vehicle_type?.name || '—') + '</span></td>' +
                    '<td class="py-2">' + (v.sale_type?.name || '—') + '</td>' +
                    '<td class="py-2">' + statusBadge + '</td>' +
                    '<td class="py-2"><a href="' + webBase + '/vehicles/' + v.id + '" class="btn btn-xs" style="background:#273d80;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;"><i class="bx bx-show me-1"></i>View</a></td>' +
                    '</tr>';
            });
            $tbody.html(html);

            // Pagination
            var d = res.data;
            if (d.last_page > 1) {
                var pHtml = '<span class="text-muted">Page ' + d.current_page + ' of ' + d.last_page + '</span><div class="d-flex gap-1">';
                if (d.current_page > 1) pHtml += '<button class="btn btn-xs btn-outline-secondary page-btn" data-page="' + (d.current_page-1) + '" style="border-radius:6px;font-size:.75rem;padding:2px 10px;">Prev</button>';
                if (d.current_page < d.last_page) pHtml += '<button class="btn btn-xs btn-outline-secondary page-btn" data-page="' + (d.current_page+1) + '" style="border-radius:6px;font-size:.75rem;padding:2px 10px;">Next</button>';
                pHtml += '</div>';
                $('#vehiclesPagination').html(pHtml).show();
            } else {
                $('#vehiclesPagination').hide();
            }
        });
    }

    loadVehicles();
    $('#filterSaleType').on('change', function() { loadVehicles(1); });
    $(document).on('click', '.page-btn', function() { loadVehicles($(this).data('page')); });

    // Search
    $('#btnSearch').on('click', function() {
        var reg = $('#searchReg').val().trim();
        var chassis = $('#searchChassis').val().trim();
        var engine = $('#searchEngine').val().trim();
        if (!reg && !chassis && !engine) { alert('Please enter at least one search value.'); return; }

        $('#searchResult').removeClass('show');
        $('#searchNotFound').addClass('d-none');
        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Searching...');

        $.get(base + '/vehicles/search', { registration_no: reg, chassis_no: chassis, engine_no: engine })
            .done(function(res) {
                if (res.success && res.data) {
                    var v = res.data;
                    $('#res-model').text((v.model || '—') + (v.variant ? ' – ' + v.variant : ''));
                    var badges = '<span class="' + (v.vehicle_type === '3W' ? 'badge-3w' : 'badge-2w') + ' me-1">' + (v.vehicle_type || '') + '</span>';
                    if (v.is_provisional) badges += '<span class="badge-provisional">Provisional</span>';
                    $('#res-badges').html(badges);
                    $('#res-reg').text(v.registration_no || '—');
                    $('#res-chassis').text(v.chassis_no || '—');
                    $('#res-engine').text(v.engine_no || '—');
                    $('#res-color').text(v.color || '—');
                    $('#res-saletype').text(v.sale_type || '—');
                    $('#res-saledate').text(v.sale_date || '—');
                    $('#res-owner').text(v.current_owner || '—');
                    var wStatus = v.warranty_status || '—';
                    $('#res-warranty').html(wStatus === 'Active' ? '<span class="badge-active-w">Active</span>' : '<span class="badge-expired-w">' + wStatus + '</span>');
                    $('#res-lastservice').text(v.last_service_date || 'No previous service');
                    $('#res-viewBtn').attr('href', webBase + '/vehicles/' + v.id);
                    $('#res-jobCardBtn').attr('href', webBase + '/job-cards/create?vehicle_id=' + v.id);
                    $('#searchResult').addClass('show');
                } else {
                    $('#searchNotFound').removeClass('d-none');
                }
            })
            .fail(function() { $('#searchNotFound').removeClass('d-none'); })
            .always(function() { $('#btnSearch').prop('disabled', false).html('<i class="bx bx-search me-1"></i>Search'); });
    });

    $('#btnClear').on('click', function() {
        $('#searchReg, #searchChassis, #searchEngine').val('');
        $('#searchResult').removeClass('show');
        $('#searchNotFound').addClass('d-none');
    });

    // Save provisional
    $('#btnSaveProvisional').on('click', function() {
        var data = {
            registration_no: $('#prov-reg').val().trim(),
            chassis_no: $('#prov-chassis').val().trim(),
            engine_no: $('#prov-engine').val().trim(),
            vehicle_type_id: $('#prov-type').val(),
            _token: '{{ csrf_token() }}'
        };
        if (!data.registration_no || !data.chassis_no || !data.engine_no || !data.vehicle_type_id) {
            alert('All fields are required.'); return;
        }
        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Saving...');
        $.post(base + '/vehicles/provisional', data)
            .done(function(res) {
                if (res.success) {
                    $('#provisionalModal').modal('hide');
                    loadVehicles(1);
                    alert('Provisional vehicle registered successfully.');
                } else {
                    alert(res.message || 'Error saving vehicle.');
                }
            })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error saving vehicle.'); })
            .always(function() { $('#btnSaveProvisional').prop('disabled', false).html('Save Provisional'); });
    });

    // Allow Enter key on search fields
    $('#searchReg, #searchChassis, #searchEngine').on('keypress', function(e) {
        if (e.which === 13) $('#btnSearch').click();
    });
});
</script>
@endpush

</x-app-layout>
