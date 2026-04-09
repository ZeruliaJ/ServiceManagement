<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .info-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .scan-box { border:2px dashed #c7d8f8; border-radius:12px; padding:30px; text-align:center; background:#f0f4ff; transition:.2s; }
    .scan-box:hover { border-color:#273d80; }
    .gp-card { border-radius:10px; border:1px solid #e2e8f0; padding:14px 18px; background:#fff; margin-bottom:10px; transition:.18s; }
    .gp-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
    .status-pill { font-size:.7rem; font-weight:700; padding:3px 10px; border-radius:20px; }
    .sp-generated { background:#cfe2ff; color:#084298; }
    .sp-used      { background:#d1e7dd; color:#0a3622; }
    .sp-cancelled { background:#f8d7da; color:#842029; }
    .sp-default   { background:#e9ecef; color:#495057; }
    .gate-no-big { font-size:1.4rem; font-weight:900; color:#273d80; letter-spacing:2px; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <div style="font-size:.75rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">TVS Service</div>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">Gate Pass Management</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">Scan or enter gate pass number to verify and release vehicles at the gate.</p>
    </div>

    <div class="row g-3">

        {{-- Scan / Lookup Panel --}}
        <div class="col-xl-5">
            <div class="card info-card">
                <div class="card-header" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
                    <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-qr-scan me-2" style="color:#273d80;"></i>Gate Scan / Lookup</h6>
                </div>
                <div class="card-body">
                    <div class="scan-box mb-3">
                        <i class="bx bx-qr" style="font-size:2.5rem;color:#273d80;margin-bottom:10px;"></i>
                        <div class="fw-600" style="font-size:.9rem;color:#1e2a4a;">Scan QR Code</div>
                        <div class="text-muted" style="font-size:.78rem;">or enter gate pass number below</div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" id="gpSearch" class="form-control" placeholder="Enter Gate Pass No (e.g. GP-2024-001)" style="border-radius:8px 0 0 8px;font-size:.85rem;">
                        <button id="btnLookup" class="btn" style="background:#273d80;color:#fff;border-radius:0 8px 8px 0;font-size:.85rem;padding:0 18px;"><i class="bx bx-search me-1"></i>Lookup</button>
                    </div>

                    <div id="gpResult" style="display:none;">
                        <div style="border-radius:10px;border:1px solid #c7d8f8;background:#f0f4ff;padding:16px;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="gate-no-big" id="gp-no">—</div>
                                <span id="gp-statusBadge" class="status-pill sp-default">—</span>
                            </div>
                            <div style="font-size:.8rem;">
                                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Job Card #</span><strong id="gp-jcno">—</strong></div>
                                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Registration No</span><strong id="gp-reg">—</strong></div>
                                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Chassis No</span><strong id="gp-chassis">—</strong></div>
                                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Customer</span><strong id="gp-cust">—</strong></div>
                                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Authorized By</span><strong id="gp-auth">—</strong></div>
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Generated At</span><strong id="gp-time">—</strong></div>
                            </div>
                            <div class="mt-3">
                                <button id="btnRelease" class="btn w-100" style="background:#22c55e;color:#fff;border-radius:8px;font-size:.85rem;font-weight:600;padding:10px;" disabled>
                                    <i class="bx bx-check-shield me-2"></i>Confirm Vehicle Release
                                </button>
                                <div class="text-muted mt-2 text-center" style="font-size:.74rem;" id="gpHint">—</div>
                            </div>
                        </div>
                    </div>

                    <div id="gpNotFound" class="alert alert-warning d-none mt-2" style="border-radius:8px;font-size:.82rem;">
                        <i class="bx bx-error-circle me-1"></i>Gate pass not found. Verify the number and try again.
                    </div>
                </div>
            </div>
        </div>

        {{-- Gate Pass List --}}
        <div class="col-xl-7">
            <div class="card info-card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
                    <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-list-ul me-2" style="color:#273d80;"></i>Today's Gate Passes</h6>
                    <div class="d-flex gap-2">
                        <select id="gpStatusFilter" class="form-select form-select-sm" style="width:150px;border-radius:7px;font-size:.8rem;">
                            <option value="">All Statuses</option>
                            <option value="Generated">Generated</option>
                            <option value="Used">Used</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:.82rem;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="px-3 py-2 fw-600 text-muted" style="font-size:.73rem;">Gate Pass #</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Job Card #</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Reg No</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Customer</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Generated At</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Released At</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Status</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.73rem;"></th>
                                </tr>
                            </thead>
                            <tbody id="gpTbody">
                                <tr><td colspan="8" class="text-center text-muted py-4">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="gpPagination" class="d-flex justify-content-between align-items-center px-3 py-2 border-top" style="font-size:.8rem;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var base = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";
    var foundGpId = null;

    function statusClass(name) {
        var m = { 'Generated': 'sp-generated', 'Used': 'sp-used', 'Cancelled': 'sp-cancelled' };
        return m[name] || 'sp-default';
    }

    // Lookup
    function doLookup() {
        var q = $('#gpSearch').val().trim();
        if (!q) { alert('Enter a gate pass number.'); return; }
        $('#gpResult').hide();
        $('#gpNotFound').addClass('d-none');
        $('#btnLookup').prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Searching...');

        // Search via job cards gate passes (direct GP lookup not available as separate API; search in list)
        $.get(base + '/job-cards', { gate_pass_no: q }, function(res) {
            if (res.success && res.data.data && res.data.data.length > 0) {
                var jc = res.data.data.find(function(j) { return j.gate_pass && j.gate_pass.gate_pass_no === q; });
                if (jc && jc.gate_pass) {
                    var gp = jc.gate_pass;
                    foundGpId = gp.id;
                    populateGpResult(gp, jc);
                } else {
                    $('#gpNotFound').removeClass('d-none');
                }
            } else {
                $('#gpNotFound').removeClass('d-none');
            }
        }).fail(function() { $('#gpNotFound').removeClass('d-none'); })
          .always(function() { $('#btnLookup').prop('disabled', false).html('<i class="bx bx-search me-1"></i>Lookup'); });
    }

    function populateGpResult(gp, jc) {
        var statusName = gp.gate_pass_status?.name || '—';
        $('#gp-no').text(gp.gate_pass_no || '—');
        $('#gp-statusBadge').text(statusName).removeClass().addClass('status-pill ' + statusClass(statusName));
        $('#gp-jcno').text(jc.job_card_no || '—');
        $('#gp-reg').text(jc.vehicle?.registration_no || '—');
        $('#gp-chassis').text(jc.vehicle?.chassis_no || '—');
        $('#gp-cust').text(jc.customer_party?.name || '—');
        $('#gp-auth').text(gp.authorized_by || '—');
        $('#gp-time').text(gp.created_at || '—');

        var canRelease = statusName === 'Generated';
        $('#btnRelease').prop('disabled', !canRelease);
        $('#gpHint').text(canRelease ? 'Verify vehicle and rider visually before confirming release.' : (statusName === 'Used' ? 'Vehicle already released.' : 'Gate pass is not in releasable status.'));
        $('#gpResult').show();
    }

    $('#btnLookup').on('click', doLookup);
    $('#gpSearch').on('keypress', function(e) { if (e.which === 13) doLookup(); });

    $('#btnRelease').on('click', function() {
        if (!foundGpId) { alert('No gate pass selected.'); return; }
        if (!confirm('Confirm vehicle visual check and authorize release?')) return;
        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Releasing...');
        $.post(base + '/gate-passes/' + foundGpId + '/release', { _token: '{{ csrf_token() }}' })
            .done(function(res) {
                if (res.success) {
                    alert('Vehicle released successfully. Gate Pass marked as USED.');
                    $('#gpResult').hide();
                    $('#gpSearch').val('');
                    loadGatePasses();
                } else { alert(res.message); }
            })
            .fail(function(xhr) { alert(xhr.responseJSON?.message || 'Error releasing vehicle.'); })
            .always(function() { $('#btnRelease').prop('disabled', false).html('<i class="bx bx-check-shield me-2"></i>Confirm Vehicle Release'); });
    });

    // Load gate passes list
    function loadGatePasses(page) {
        page = page || 1;
        var params = { page: page };
        var status = $('#gpStatusFilter').val();
        if (status) params.gate_pass_status = status;

        // Get all job cards with gate passes
        $.get(base + '/job-cards', params, function(res) {
            var $tbody = $('#gpTbody');
            var items = (res.data?.data || []).filter(function(jc) { return !!jc.gate_pass; });
            if (items.length === 0) {
                $tbody.html('<tr><td colspan="8" class="text-center text-muted py-4" style="font-size:.82rem;">No gate passes found.</td></tr>');
                return;
            }
            var html = '';
            items.forEach(function(jc) {
                var gp = jc.gate_pass;
                var statusName = gp.gate_pass_status?.name || '—';
                html += '<tr>' +
                    '<td class="px-3 py-2 fw-700" style="color:#273d80;">' + (gp.gate_pass_no || '—') + '</td>' +
                    '<td class="py-2">' + (jc.job_card_no || '—') + '</td>' +
                    '<td class="py-2">' + (jc.vehicle?.registration_no || '—') + '</td>' +
                    '<td class="py-2">' + (jc.customer_party?.name || '—') + '</td>' +
                    '<td class="py-2 text-muted" style="font-size:.75rem;">' + (gp.created_at || '—') + '</td>' +
                    '<td class="py-2 text-muted" style="font-size:.75rem;">' + (gp.exit_time || '—') + '</td>' +
                    '<td class="py-2"><span class="status-pill ' + statusClass(statusName) + '">' + statusName + '</span></td>' +
                    '<td class="py-2"><a href="' + webBase + '/job-cards/' + jc.id + '" class="btn btn-xs" style="background:#273d80;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;">View JC</a></td>' +
                    '</tr>';
            });
            $tbody.html(html);
        });
    }

    loadGatePasses();
    $('#gpStatusFilter').on('change', function() { loadGatePasses(1); });
});
</script>
@endpush

</x-app-layout>
