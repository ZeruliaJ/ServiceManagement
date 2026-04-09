<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .info-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .badge-active   { background:#d1e7dd; color:#0a3622; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .badge-expired  { background:#f8d7da; color:#842029; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .badge-nowarr   { background:#e9ecef; color:#495057; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .badge-pending  { background:#fff3cd; color:#856404; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .badge-approved { background:#d1e7dd; color:#0a3622; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .badge-rejected { background:#f8d7da; color:#842029; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .tab-pill { padding:6px 16px; border-radius:7px; font-size:.8rem; font-weight:600; cursor:pointer; border:1px solid #e9ecef; color:#6b7280; background:#f8f9fa; transition:.18s; }
    .tab-pill.active { background:#273d80; color:#fff; border-color:#273d80; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <div style="font-size:.75rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">TVS Service</div>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">Warranty Management</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">Manage warranties, validate coverage, and process warranty claims.</p>
    </div>

    {{-- Section tabs --}}
    <div class="d-flex gap-2 mb-3">
        <span class="tab-pill active" data-tab="warranties">Warranties</span>
        <span class="tab-pill" data-tab="claims">Warranty Claims</span>
    </div>

    {{-- WARRANTIES SECTION --}}
    <div id="section-warranties">
        <div class="card info-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
                <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-shield-quarter me-2" style="color:#22c55e;"></i>Warranty Records</h6>
                <div class="d-flex gap-2">
                    <select id="warrantyStatusFilter" class="form-select form-select-sm" style="width:150px;border-radius:7px;font-size:.8rem;">
                        <option value="">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Expired">Expired</option>
                        <option value="Not Available">Not Available</option>
                    </select>
                    <input type="text" id="warrantySearch" class="form-control form-control-sm" placeholder="Search chassis / warranty no..." style="width:220px;border-radius:7px;font-size:.8rem;">
                    <button class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;" data-bs-toggle="modal" data-bs-target="#addWarrantyModal">
                        <i class="bx bx-plus me-1"></i>Add Warranty
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:.82rem;">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th class="px-3 py-2 fw-600 text-muted" style="font-size:.73rem;">Warranty No</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Vehicle (Chassis)</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Registration No</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Start Date</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">End Date</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Km Limit</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Status</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;"></th>
                            </tr>
                        </thead>
                        <tbody id="warrantyTbody">
                            <tr><td colspan="8" class="text-center text-muted py-4">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="warrantyPagination" class="d-flex justify-content-between align-items-center px-3 py-2 border-top" style="font-size:.8rem;"></div>
            </div>
        </div>
    </div>

    {{-- CLAIMS SECTION --}}
    <div id="section-claims" style="display:none;">
        <div class="card info-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
                <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-file-blank me-2" style="color:#c0172b;"></i>Warranty Claims</h6>
                <div class="d-flex gap-2">
                    <select id="claimStatusFilter" class="form-select form-select-sm" style="width:150px;border-radius:7px;font-size:.8rem;">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:.82rem;">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th class="px-3 py-2 fw-600 text-muted" style="font-size:.73rem;">Claim No</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Job Card #</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Vehicle</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Claim Amount</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Claim Date</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Status</th>
                                <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="claimsTbody">
                            <tr><td colspan="7" class="text-center text-muted py-4">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Warranty Modal --}}
<div class="modal fade" id="addWarrantyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e2d6b,#273d80);border-radius:12px 12px 0 0;border:none;">
                <h6 class="modal-title text-white fw-700"><i class="bx bx-shield-quarter me-2"></i>Add Warranty Record</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-600" style="font-size:.8rem;">Vehicle (search by chassis) <span class="text-danger">*</span></label>
                    <input type="text" id="wVehicleSearch" class="form-control form-control-sm" placeholder="Search chassis / registration...">
                    <select id="wVehicleId" class="form-select form-select-sm mt-1" style="display:none;"><option value="">Select vehicle...</option></select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600" style="font-size:.8rem;">Warranty No <span class="text-danger">*</span></label>
                    <input type="text" id="wNo" class="form-control form-control-sm" placeholder="e.g. WR-2024-001">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-600" style="font-size:.8rem;">Start Date <span class="text-danger">*</span></label>
                        <input type="date" id="wStart" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-600" style="font-size:.8rem;">End Date <span class="text-danger">*</span></label>
                        <input type="date" id="wEnd" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="mt-2">
                    <label class="form-label fw-600" style="font-size:.8rem;">Km Limit</label>
                    <input type="number" id="wKmLimit" class="form-control form-control-sm" placeholder="e.g. 30000">
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0;">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:7px;">Cancel</button>
                <button type="button" id="btnSaveWarranty" class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;">Save Warranty</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var base = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";
    var searchTimer;

    // Tab switching
    $(document).on('click', '.tab-pill', function() {
        $('.tab-pill').removeClass('active');
        $(this).addClass('active');
        var tab = $(this).data('tab');
        $('#section-warranties, #section-claims').hide();
        $('#section-' + tab).show();
        if (tab === 'claims') loadClaims();
        else loadWarranties();
    });

    function badgeW(name) {
        var m = { 'Active': 'badge-active', 'Expired': 'badge-expired', 'Not Available': 'badge-nowarr' };
        return '<span class="' + (m[name] || 'badge-nowarr') + '">' + (name || '—') + '</span>';
    }
    function badgeC(name) {
        var m = { 'Pending': 'badge-pending', 'Approved': 'badge-approved', 'Rejected': 'badge-rejected' };
        return '<span class="' + (m[name] || 'badge-nowarr') + '">' + (name || '—') + '</span>';
    }

    function loadWarranties(page) {
        page = page || 1;
        var params = { page: page };
        var status = $('#warrantyStatusFilter').val();
        var search = $('#warrantySearch').val().trim();
        if (status) params.status = status;
        if (search) params.search = search;

        $.get(base + '/warranties', params, function(res) {
            var $tbody = $('#warrantyTbody');
            if (!res.success || !res.data.data || res.data.data.length === 0) {
                $tbody.html('<tr><td colspan="8" class="text-center text-muted py-4" style="font-size:.82rem;">No warranty records found.</td></tr>');
                $('#warrantyPagination').hide();
                return;
            }
            var html = '';
            res.data.data.forEach(function(w) {
                html += '<tr>' +
                    '<td class="px-3 py-2 fw-600" style="color:#273d80;">' + (w.warranty_no || '—') + '</td>' +
                    '<td class="py-2">' + (w.vehicle?.chassis_no || '—') + '</td>' +
                    '<td class="py-2">' + (w.vehicle?.registration_no || '—') + '</td>' +
                    '<td class="py-2">' + (w.warranty_start_date || '—') + '</td>' +
                    '<td class="py-2">' + (w.warranty_end_date || '—') + '</td>' +
                    '<td class="py-2">' + (w.warranty_km_limit ? w.warranty_km_limit.toLocaleString() + ' km' : '—') + '</td>' +
                    '<td class="py-2">' + badgeW(w.warranty_status?.name) + '</td>' +
                    '<td class="py-2"><a href="' + webBase + '/vehicles/' + w.vehicle_id + '" class="btn btn-xs" style="background:#273d80;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;">Vehicle</a></td>' +
                    '</tr>';
            });
            $tbody.html(html);

            var d = res.data;
            if (d.last_page > 1) {
                var pHtml = '<span class="text-muted">Page ' + d.current_page + ' of ' + d.last_page + '</span><div class="d-flex gap-1">';
                if (d.current_page > 1) pHtml += '<button class="btn btn-xs btn-outline-secondary w-page-btn" data-page="' + (d.current_page-1) + '" style="border-radius:6px;font-size:.75rem;padding:2px 10px;">Prev</button>';
                if (d.current_page < d.last_page) pHtml += '<button class="btn btn-xs btn-outline-secondary w-page-btn" data-page="' + (d.current_page+1) + '" style="border-radius:6px;font-size:.75rem;padding:2px 10px;">Next</button>';
                pHtml += '</div>';
                $('#warrantyPagination').html(pHtml).show();
            } else {
                $('#warrantyPagination').hide();
            }
        });
    }

    function loadClaims(page) {
        page = page || 1;
        var params = { page: page };
        var status = $('#claimStatusFilter').val();
        if (status) params.status = status;

        $.get(base + '/warranties/claims', params, function(res) {
            var $tbody = $('#claimsTbody');
            if (!res.success || !res.data || res.data.length === 0) {
                $tbody.html('<tr><td colspan="7" class="text-center text-muted py-4">No claims found.</td></tr>');
                return;
            }
            var html = '';
            (res.data.data || res.data).forEach(function(c) {
                html += '<tr>' +
                    '<td class="px-3 py-2 fw-600" style="color:#273d80;">' + (c.claim_no || c.id || '—') + '</td>' +
                    '<td class="py-2">' + (c.job_card?.job_card_no || '—') + '</td>' +
                    '<td class="py-2">' + (c.warranty?.vehicle?.registration_no || '—') + '</td>' +
                    '<td class="py-2">TZS ' + ((c.claim_amount || 0)).toLocaleString() + '</td>' +
                    '<td class="py-2">' + (c.claim_date || c.created_at || '—') + '</td>' +
                    '<td class="py-2">' + badgeC(c.status) + '</td>' +
                    '<td class="py-2 d-flex gap-1">' +
                        (c.status === 'Pending' ? '<button class="btn btn-xs approve-claim" data-id="' + c.id + '" style="background:#22c55e;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;">Approve</button>' +
                        '<button class="btn btn-xs reject-claim" data-id="' + c.id + '" style="background:#c0172b;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;">Reject</button>' : '—') +
                    '</td>' +
                    '</tr>';
            });
            $tbody.html(html);
        }).fail(function() {
            $('#claimsTbody').html('<tr><td colspan="7" class="text-center text-muted py-4">Could not load claims.</td></tr>');
        });
    }

    loadWarranties();
    $('#warrantyStatusFilter').on('change', function() { loadWarranties(1); });
    $('#warrantySearch').on('input', function() { clearTimeout(searchTimer); searchTimer = setTimeout(function() { loadWarranties(1); }, 400); });
    $(document).on('click', '.w-page-btn', function() { loadWarranties($(this).data('page')); });
    $('#claimStatusFilter').on('change', function() { loadClaims(1); });

    // Approve / Reject claims
    $(document).on('click', '.approve-claim', function() {
        var id = $(this).data('id');
        if (!confirm('Approve this warranty claim?')) return;
        $.post(base + '/warranties/claims/' + id + '/approve', { _token: '{{ csrf_token() }}' })
            .done(function(res) { if (res.success) loadClaims(); else alert(res.message); })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error.'); });
    });
    $(document).on('click', '.reject-claim', function() {
        var id = $(this).data('id');
        var reason = prompt('Enter rejection reason:');
        if (!reason) return;
        $.post(base + '/warranties/claims/' + id + '/reject', { reason: reason, _token: '{{ csrf_token() }}' })
            .done(function(res) { if (res.success) loadClaims(); else alert(res.message); })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error.'); });
    });

    // Vehicle search in modal
    var vSearchTimer;
    $('#wVehicleSearch').on('input', function() {
        clearTimeout(vSearchTimer);
        var q = $(this).val().trim();
        if (q.length < 2) return;
        vSearchTimer = setTimeout(function() {
            $.get(base + '/vehicles', { search: q }, function(res) {
                var $sel = $('#wVehicleId');
                $sel.html('<option value="">Select vehicle...</option>');
                if (res.success && res.data.data.length > 0) {
                    res.data.data.forEach(function(v) {
                        $sel.append('<option value="' + v.id + '">' + (v.registration_no || v.chassis_no) + ' – ' + (v.variant?.model_name || '') + '</option>');
                    });
                    $sel.show();
                }
            });
        }, 400);
    });

    // Save warranty
    $('#btnSaveWarranty').on('click', function() {
        var data = {
            vehicle_id: $('#wVehicleId').val(),
            warranty_no: $('#wNo').val().trim(),
            warranty_start_date: $('#wStart').val(),
            warranty_end_date: $('#wEnd').val(),
            warranty_km_limit: $('#wKmLimit').val() || null,
            warranty_status_id: 1,
            _token: '{{ csrf_token() }}'
        };
        if (!data.vehicle_id || !data.warranty_no || !data.warranty_start_date || !data.warranty_end_date) {
            alert('All required fields must be filled.'); return;
        }
        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Saving...');
        $.post(base + '/warranties', data)
            .done(function(res) {
                if (res.success) { $('#addWarrantyModal').modal('hide'); loadWarranties(); } else { alert(res.message); }
            })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error saving warranty.'); })
            .always(function() { $('#btnSaveWarranty').prop('disabled', false).html('Save Warranty'); });
    });
});
</script>
@endpush

</x-app-layout>
