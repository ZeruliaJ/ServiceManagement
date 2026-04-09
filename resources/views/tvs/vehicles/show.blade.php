<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .info-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .info-card .card-header { background:#fff; border-bottom:1px solid #f0f0f0; padding:.7rem 1rem; }
    .info-row { display:flex; align-items:flex-start; gap:8px; padding:7px 0; border-bottom:1px solid #f9f9f9; font-size:.83rem; }
    .info-row:last-child { border-bottom:none; }
    .info-label { color:#6b7280; font-weight:600; min-width:160px; font-size:.75rem; flex-shrink:0; }
    .info-val { color:#1e2a4a; font-weight:500; }
    .badge-active-w { background:#d1e7dd; color:#0a3622; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .badge-expired-w { background:#f8d7da; color:#842029; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .badge-provisional { background:#fff3cd; color:#856404; font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:700; }
    .timeline-item { display:flex; gap:14px; padding:10px 0; border-bottom:1px solid #f5f5f5; }
    .timeline-item:last-child { border-bottom:none; }
    .timeline-dot { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <a href="{{ route('tvs.vehicles') }}" class="btn btn-sm mb-2" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;font-size:.78rem;"><i class="bx bx-arrow-back me-1"></i>Back to Vehicles</a>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;" id="hero-title">Vehicle Detail</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;" id="hero-sub">Loading...</p>
    </div>

    <div id="loadingState" class="text-center py-5"><i class="bx bx-loader bx-spin" style="font-size:2rem;color:#273d80;"></i></div>

    <div id="vehicleContent" style="display:none;">
        <div class="row g-3">

            {{-- Left: Vehicle Info --}}
            <div class="col-xl-4">
                <div class="card info-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-700" style="color:#1e2a4a;font-size:.85rem;"><i class="bx bx-car me-2" style="color:#273d80;"></i>Vehicle Details</h6>
                        <span id="veh-typeBadge"></span>
                    </div>
                    <div class="card-body p-3">
                        <div class="info-row"><span class="info-label">Registration No</span><span class="info-val fw-700" id="veh-reg">—</span></div>
                        <div class="info-row"><span class="info-label">Chassis No</span><span class="info-val" id="veh-chassis">—</span></div>
                        <div class="info-row"><span class="info-label">Engine No</span><span class="info-val" id="veh-engine">—</span></div>
                        <div class="info-row"><span class="info-label">Model</span><span class="info-val" id="veh-model">—</span></div>
                        <div class="info-row"><span class="info-label">Variant</span><span class="info-val" id="veh-variant">—</span></div>
                        <div class="info-row"><span class="info-label">Color</span><span class="info-val" id="veh-color">—</span></div>
                        <div class="info-row"><span class="info-label">Sale Type</span><span class="info-val" id="veh-saletype">—</span></div>
                        <div class="info-row"><span class="info-label">Sale Date</span><span class="info-val" id="veh-saledate">—</span></div>
                        <div class="info-row"><span class="info-label">Provisional</span><span class="info-val" id="veh-provisional">—</span></div>
                    </div>
                </div>

                {{-- Warranty --}}
                <div class="card info-card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-700" style="color:#1e2a4a;font-size:.85rem;"><i class="bx bx-shield-quarter me-2" style="color:#22c55e;"></i>Warranty</h6>
                    </div>
                    <div class="card-body p-3" id="warrantyBlock">
                        <div class="text-muted" style="font-size:.82rem;">Loading...</div>
                    </div>
                </div>

                <a href="{{ url('/tvs/job-cards/create') }}?vehicle_id={{ $vehicleId }}" class="btn w-100 mb-2" style="background:#c0172b;color:#fff;border-radius:8px;font-weight:600;"><i class="bx bx-plus me-2"></i>Create Job Card</a>
            </div>

            {{-- Right: Owner + History --}}
            <div class="col-xl-8">
                {{-- Current Owner --}}
                <div class="card info-card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-700" style="color:#1e2a4a;font-size:.85rem;"><i class="bx bx-user me-2" style="color:#c0172b;"></i>Current Owner / Party</h6>
                    </div>
                    <div class="card-body p-3" id="ownerBlock">
                        <div class="text-muted" style="font-size:.82rem;">Loading...</div>
                    </div>
                </div>

                {{-- Service History --}}
                <div class="card info-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-700" style="color:#1e2a4a;font-size:.85rem;"><i class="bx bx-history me-2" style="color:#f59e0b;"></i>Service History</h6>
                        <span id="historyCount" class="badge" style="background:#273d80;font-size:.72rem;"></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size:.82rem;">
                                <thead style="background:#f8f9fa;">
                                    <tr>
                                        <th class="px-3 py-2 fw-600 text-muted" style="font-size:.73rem;">Job Card #</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Date</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Service Type</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Odometer In</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Status</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;"></th>
                                    </tr>
                                </thead>
                                <tbody id="serviceHistoryTbody">
                                    <tr><td colspan="6" class="text-center text-muted py-4">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var vehicleId = {{ $vehicleId }};
    var base = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";

    $.get(base + '/vehicles/' + vehicleId, function(res) {
        $('#loadingState').hide();
        if (!res.success) { $('#vehicleContent').html('<div class="alert alert-danger">Vehicle not found.</div>').show(); return; }
        var v = res.data;
        $('#vehicleContent').show();

        $('#hero-title').text((v.model || '') + (v.variant ? ' – ' + v.variant : '') || 'Vehicle Detail');
        $('#hero-sub').text('Reg: ' + (v.registration_no || '—') + '  |  Chassis: ' + (v.chassis_no || '—'));

        $('#veh-reg').text(v.registration_no || '—');
        $('#veh-chassis').text(v.chassis_no || '—');
        $('#veh-engine').text(v.engine_no || '—');
        $('#veh-model').text(v.model || '—');
        $('#veh-variant').text(v.variant || '—');
        $('#veh-color').text(v.color || '—');
        $('#veh-saletype').text(v.sale_type?.name || '—');
        $('#veh-saledate').text(v.sale_date || '—');
        $('#veh-provisional').html(v.is_provisional ? '<span class="badge-provisional">Yes – Pending Validation</span>' : '<span class="badge-active-w">Validated</span>');

        var typeClass = v.vehicle_type?.name === '3W' ? 'background:#e2d9f3;color:#432874;' : 'background:#cfe2ff;color:#084298;';
        $('#veh-typeBadge').html('<span style="' + typeClass + 'font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;">' + (v.vehicle_type?.name || '') + '</span>');

        // Warranty
        var wHtml = '';
        if (v.warranties && v.warranties.length > 0) {
            v.warranties.forEach(function(w) {
                var isActive = w.warranty_status?.name === 'Active';
                wHtml += '<div class="info-row"><span class="info-label">Warranty No</span><span class="info-val fw-600">' + (w.warranty_no || '—') + '</span></div>' +
                    '<div class="info-row"><span class="info-label">Status</span><span class="info-val">' + (isActive ? '<span class="badge-active-w">Active</span>' : '<span class="badge-expired-w">' + (w.warranty_status?.name || '—') + '</span>') + '</span></div>' +
                    '<div class="info-row"><span class="info-label">Start Date</span><span class="info-val">' + (w.warranty_start_date || '—') + '</span></div>' +
                    '<div class="info-row"><span class="info-label">End Date</span><span class="info-val">' + (w.warranty_end_date || '—') + '</span></div>' +
                    '<div class="info-row"><span class="info-label">Km Limit</span><span class="info-val">' + (w.warranty_km_limit ? w.warranty_km_limit.toLocaleString() + ' km' : '—') + '</span></div>';
            });
        } else {
            wHtml = '<div class="text-muted" style="font-size:.82rem;"><i class="bx bx-info-circle me-1"></i>No warranty records found.</div>';
        }
        $('#warrantyBlock').html(wHtml);

        // Owner
        var oHtml = '';
        if (v.current_owner) {
            var o = v.current_owner;
            oHtml = '<div class="row g-2">' +
                '<div class="col-md-6"><div class="info-row"><span class="info-label">Name</span><span class="info-val fw-600">' + (o.name || '—') + '</span></div>' +
                '<div class="info-row"><span class="info-label">Type</span><span class="info-val">' + (o.party_type?.name || '—') + '</span></div>' +
                '<div class="info-row"><span class="info-label">Code</span><span class="info-val">' + (o.party_code || '—') + '</span></div></div>' +
                '<div class="col-md-6"><div class="info-row"><span class="info-label">Phone</span><span class="info-val">' + (o.phone || '—') + '</span></div>' +
                '<div class="info-row"><span class="info-label">Email</span><span class="info-val">' + (o.email || '—') + '</span></div>' +
                '<div class="info-row"><span class="info-label">TIN</span><span class="info-val">' + (o.tin_no || '—') + '</span></div></div></div>';
        } else {
            oHtml = '<div class="text-muted" style="font-size:.82rem;"><i class="bx bx-info-circle me-1"></i>No owner mapping found.</div>';
        }
        $('#ownerBlock').html(oHtml);

        // Service history
        var hist = v.service_history || [];
        $('#historyCount').text(hist.length + ' visits');
        if (hist.length === 0) {
            $('#serviceHistoryTbody').html('<tr><td colspan="6" class="text-center text-muted py-4">No service history found.</td></tr>');
        } else {
            var hHtml = '';
            hist.forEach(function(h) {
                hHtml += '<tr>' +
                    '<td class="px-3 py-2 fw-600" style="color:#273d80;">' + (h.job_card_no || '—') + '</td>' +
                    '<td class="py-2">' + (h.service_date || '—') + '</td>' +
                    '<td class="py-2">' + (h.service_type || '—') + '</td>' +
                    '<td class="py-2">' + (h.odometer_in ? h.odometer_in.toLocaleString() + ' km' : '—') + '</td>' +
                    '<td class="py-2"><span style="background:#d1e7dd;color:#0a3622;font-size:.7rem;padding:3px 9px;border-radius:20px;font-weight:700;">' + (h.status || '—') + '</span></td>' +
                    '<td class="py-2"><a href="' + webBase + '/job-cards/' + h.job_card_id + '" class="btn btn-xs" style="background:#273d80;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;">View</a></td>' +
                    '</tr>';
            });
            $('#serviceHistoryTbody').html(hHtml);
        }
    }).fail(function() {
        $('#loadingState').hide();
        $('#vehicleContent').html('<div class="alert alert-danger">Could not load vehicle data.</div>').show();
    });
});
</script>
@endpush

</x-app-layout>
