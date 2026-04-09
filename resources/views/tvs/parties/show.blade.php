<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .info-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .info-card .card-header { background:#fff; border-bottom:1px solid #f0f0f0; padding:.7rem 1rem; }
    .info-row { display:flex; align-items:flex-start; gap:8px; padding:7px 0; border-bottom:1px solid #f9f9f9; font-size:.83rem; }
    .info-row:last-child { border-bottom:none; }
    .info-label { color:#6b7280; font-weight:600; min-width:140px; font-size:.75rem; flex-shrink:0; }
    .info-val { color:#1e2a4a; font-weight:500; }
    .clv-box { border-radius:9px; padding:14px; text-align:center; background:#f8f9fa; border:1px solid #e9ecef; }
    .clv-val { font-size:1.3rem; font-weight:800; color:#273d80; line-height:1; }
    .clv-label { font-size:.72rem; color:#6b7280; font-weight:600; margin-top:4px; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <a href="{{ route('tvs.parties') }}" class="btn btn-sm mb-2" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;font-size:.78rem;"><i class="bx bx-arrow-back me-1"></i>Back to Parties</a>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;" id="hero-title">Party Detail</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;" id="hero-sub">Loading...</p>
    </div>

    <div id="loadingState" class="text-center py-5"><i class="bx bx-loader bx-spin" style="font-size:2rem;color:#273d80;"></i></div>
    <div id="partyContent" style="display:none;">
        <div class="row g-3">
            <div class="col-xl-4">
                <div class="card info-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;"><i class="bx bx-user me-2" style="color:#273d80;"></i>Party Info</h6>
                        <span id="p-typeBadge"></span>
                    </div>
                    <div class="card-body p-3">
                        <div class="info-row"><span class="info-label">Party Code</span><span class="info-val fw-700" id="p-code">—</span></div>
                        <div class="info-row"><span class="info-label">Name</span><span class="info-val fw-600" id="p-name">—</span></div>
                        <div class="info-row"><span class="info-label">Phone</span><span class="info-val" id="p-phone">—</span></div>
                        <div class="info-row"><span class="info-label">Email</span><span class="info-val" id="p-email">—</span></div>
                        <div class="info-row"><span class="info-label">TIN</span><span class="info-val" id="p-tin">—</span></div>
                        <div class="info-row"><span class="info-label">Address</span><span class="info-val" id="p-address">—</span></div>
                    </div>
                </div>

                {{-- CLV --}}
                <div class="card info-card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;"><i class="bx bx-trending-up me-2" style="color:#c0172b;"></i>Customer Lifetime Value</h6>
                    </div>
                    <div class="card-body p-3" id="clvBlock">
                        <div class="text-muted" style="font-size:.82rem;">Loading CLV...</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                {{-- Job Card History --}}
                <div class="card info-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;"><i class="bx bx-file me-2" style="color:#f59e0b;"></i>Job Card History</h6>
                        <span id="jcCount" class="badge" style="background:#273d80;font-size:.72rem;"></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size:.82rem;">
                                <thead style="background:#f8f9fa;">
                                    <tr>
                                        <th class="px-3 py-2 fw-600 text-muted" style="font-size:.73rem;">Job Card #</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Vehicle</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Service Type</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Date</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Amount</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Status</th>
                                        <th class="py-2 fw-600 text-muted" style="font-size:.73rem;"></th>
                                    </tr>
                                </thead>
                                <tbody id="jcTbody">
                                    <tr><td colspan="7" class="text-center text-muted py-4">Loading...</td></tr>
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
    var partyId = {{ $partyId }};
    var base = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";

    $.get(base + '/parties/' + partyId, function(res) {
        $('#loadingState').hide();
        if (!res.success) { $('#partyContent').html('<div class="alert alert-danger">Party not found.</div>').show(); return; }
        var p = res.data;
        $('#partyContent').show();

        $('#hero-title').text(p.name || 'Party Detail');
        $('#hero-sub').text('Code: ' + (p.party_code || '—') + '  |  Type: ' + (p.party_type?.name || '—'));

        $('#p-code').text(p.party_code || '—');
        $('#p-name').text(p.name || '—');
        $('#p-phone').text(p.phone || '—');
        $('#p-email').text(p.email || '—');
        $('#p-tin').text(p.tin_no || '—');
        $('#p-address').text(p.address || '—');

        var typeColors = { 'Retail Customer': '#d1e7dd;color:#0a3622', 'Dealer': '#cfe2ff;color:#084298', 'Finance Company': '#fff3cd;color:#856404', 'Bank': '#e2d9f3;color:#432874', 'Institution': '#f8d7da;color:#842029' };
        var typeName = p.party_type?.name || '';
        var tc = typeColors[typeName] || '#e9ecef;color:#495057';
        $('#p-typeBadge').html('<span style="background:' + tc + ';font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;">' + typeName + '</span>');
    });

    // CLV
    $.get(base + '/reports/customer-lifetime/' + partyId, function(res) {
        if (res.success && res.data) {
            var d = res.data;
            var html = '<div class="row g-2">' +
                '<div class="col-6"><div class="clv-box"><div class="clv-val">' + (d.total_visits || 0) + '</div><div class="clv-label">Total Visits</div></div></div>' +
                '<div class="col-6"><div class="clv-box"><div class="clv-val">TZS ' + ((d.total_revenue || 0)).toLocaleString() + '</div><div class="clv-label">Total Revenue</div></div></div>' +
                '<div class="col-6"><div class="clv-box"><div class="clv-val">' + (d.warranty_visits || 0) + '</div><div class="clv-label">Warranty Visits</div></div></div>' +
                '<div class="col-6"><div class="clv-box"><div class="clv-val">' + (d.paid_visits || 0) + '</div><div class="clv-label">Paid Visits</div></div></div>' +
                '</div>';
            $('#clvBlock').html(html);
        } else {
            $('#clvBlock').html('<div class="text-muted" style="font-size:.82rem;">No CLV data yet.</div>');
        }
    });

    // Job cards for this party
    $.get(base + '/job-cards', { customer_party_id: partyId }, function(res) {
        var $tbody = $('#jcTbody');
        if (!res.success || !res.data.data || res.data.data.length === 0) {
            $tbody.html('<tr><td colspan="7" class="text-center text-muted py-4">No job cards found.</td></tr>');
            return;
        }
        var list = res.data.data;
        $('#jcCount').text(res.data.total + ' records');
        var html = '';
        var statusColors = { 'Pending': '#fff3cd;color:#856404', 'In Progress': '#cfe2ff;color:#084298', 'Completed': '#d1e7dd;color:#0a3622', 'Delivered': '#e2d9f3;color:#432874' };
        list.forEach(function(jc) {
            var sc = statusColors[jc.status?.name] || '#e9ecef;color:#495057';
            html += '<tr>' +
                '<td class="px-3 py-2 fw-600" style="color:#273d80;">' + (jc.job_card_no || '—') + '</td>' +
                '<td class="py-2">' + (jc.vehicle?.registration_no || '—') + '</td>' +
                '<td class="py-2">' + (jc.service_type?.name || '—') + '</td>' +
                '<td class="py-2">' + (jc.check_in_date || '—') + '</td>' +
                '<td class="py-2">TZS ' + ((jc.payment?.total_amount || 0)).toLocaleString() + '</td>' +
                '<td class="py-2"><span style="background:' + sc + ';font-size:.7rem;font-weight:700;padding:3px 9px;border-radius:20px;">' + (jc.status?.name || '—') + '</span></td>' +
                '<td class="py-2"><a href="' + webBase + '/job-cards/' + jc.id + '" class="btn btn-xs" style="background:#273d80;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;">View</a></td>' +
                '</tr>';
        });
        $tbody.html(html);
    });
});
</script>
@endpush

</x-app-layout>
