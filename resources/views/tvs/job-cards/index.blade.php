<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .info-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .status-pill { font-size:.7rem; font-weight:700; padding:3px 10px; border-radius:20px; }
    .sp-pending    { background:#fff3cd; color:#856404; }
    .sp-inprogress { background:#cfe2ff; color:#084298; }
    .sp-completed  { background:#d1e7dd; color:#0a3622; }
    .sp-delivered  { background:#e2d9f3; color:#432874; }
    .sp-default    { background:#e9ecef; color:#495057; }
    .filter-tab { padding:6px 16px; border-radius:7px; font-size:.8rem; font-weight:600; cursor:pointer; border:1px solid #e9ecef; color:#6b7280; background:#f8f9fa; transition:.18s; }
    .filter-tab.active, .filter-tab:hover { background:#273d80; color:#fff; border-color:#273d80; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <div style="font-size:.75rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">TVS Service</div>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">Job Cards</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">Full workflow – reception, workshop, supervisor, accounts, gate.</p>
    </div>

    {{-- Status filter tabs --}}
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <span class="filter-tab active" data-status="">All</span>
        <span class="filter-tab" data-status="pending">Pending</span>
        <span class="filter-tab" data-status="in_progress">In Progress</span>
        <span class="filter-tab" data-status="completed">Completed</span>
        <span class="filter-tab" data-status="delivered">Delivered</span>
    </div>

    <div class="card info-card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
            <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-file me-2" style="color:#273d80;"></i>Job Card List</h6>
            <div class="d-flex gap-2">
                <input type="text" id="searchJc" class="form-control form-control-sm" placeholder="Search job card / vehicle / customer..." style="width:260px;border-radius:7px;font-size:.8rem;">
                <a href="{{ route('tvs.job-cards.create') }}" class="btn btn-sm" style="background:#c0172b;color:#fff;border-radius:7px;font-size:.78rem;white-space:nowrap;"><i class="bx bx-plus me-1"></i>New Job Card</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.82rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="px-3 py-2 fw-600 text-muted" style="font-size:.73rem;">Job Card #</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Vehicle</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Customer</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Service Type</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Check-In</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Est. Delivery</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.73rem;">Status</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.73rem;"></th>
                        </tr>
                    </thead>
                    <tbody id="jcTbody">
                        <tr><td colspan="8" class="text-center text-muted py-4">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="jcPagination" class="d-flex justify-content-between align-items-center px-3 py-2 border-top" style="font-size:.8rem;display:none!important;"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var base    = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";
    var currentStatus = '';
    var searchTimer;

    function statusPillClass(s) {
        if (!s) return 'sp-default';
        var v = s.toLowerCase().replace(' ', '_');
        var map = { 'pending': 'sp-pending', 'in_progress': 'sp-inprogress', 'completed': 'sp-completed', 'delivered': 'sp-delivered' };
        return map[v] || 'sp-default';
    }

    function statusLabel(s) {
        if (!s) return '—';
        var map = { 'pending': 'Pending', 'in_progress': 'In Progress', 'completed': 'Completed', 'delivered': 'Delivered' };
        return map[s.toLowerCase()] || s;
    }

    function formatDate(d) {
        if (!d) return '—';
        return d.substring(0, 10);
    }

    function loadJc(page) {
        page = page || 1;
        var params = { page: page };
        if (currentStatus) params.status = currentStatus;
        var s = $('#searchJc').val().trim();
        if (s) params.search = s;

        $('#jcTbody').html('<tr><td colspan="8" class="text-center text-muted py-4">Loading...</td></tr>');

        $.get(base + '/job-cards', params, function(res) {
            var $tbody = $('#jcTbody');

            if (!res.success || !res.data.data || res.data.data.length === 0) {
                $tbody.html('<tr><td colspan="8" class="text-center text-muted py-4" style="font-size:.82rem;">No job cards found.</td></tr>');
                $('#jcPagination').hide();
                return;
            }

            var html = '';
            res.data.data.forEach(function(jc) {
                // Vehicle: use registration_no from loaded relation, fallback to chassis_no
                var vehicle   = (jc.vehicle && (jc.vehicle.registration_no || jc.vehicle.chassis_no)) || '—';
                // Customer: loaded via customerParty relation (foreign key: customer_id)
                var customer  = (jc.customer_party && jc.customer_party.name) || '—';
                // service_type is a plain string column
                var svcType   = jc.service_type || '—';
                // status is a plain string column
                var status    = jc.status || '';
                // dates
                var checkIn   = formatDate(jc.created_at);
                var estDel    = formatDate(jc.estimated_delivery);

                html += '<tr style="cursor:pointer;" onclick="window.location=\'' + webBase + '/job-cards/' + jc.id + '\'">' +
                    '<td class="px-3 py-2 fw-700" style="color:#273d80;">' + (jc.job_card_number || '—') + '</td>' +
                    '<td class="py-2">' + vehicle + '</td>' +
                    '<td class="py-2">' + customer + '</td>' +
                    '<td class="py-2">' + svcType + '</td>' +
                    '<td class="py-2 text-muted" style="font-size:.78rem;">' + checkIn + '</td>' +
                    '<td class="py-2 text-muted" style="font-size:.78rem;">' + estDel + '</td>' +
                    '<td class="py-2"><span class="status-pill ' + statusPillClass(status) + '">' + statusLabel(status) + '</span></td>' +
                    '<td class="py-2" onclick="event.stopPropagation();"><a href="' + webBase + '/job-cards/' + jc.id + '" class="btn btn-xs" style="background:#273d80;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;"><i class="bx bx-show me-1"></i>Open</a></td>' +
                    '</tr>';
            });
            $tbody.html(html);

            var d = res.data;
            if (d.last_page > 1) {
                var pHtml = '<span class="text-muted">Page ' + d.current_page + ' of ' + d.last_page + ' (' + d.total + ' records)</span><div class="d-flex gap-1">';
                if (d.current_page > 1) pHtml += '<button class="btn btn-xs btn-outline-secondary page-btn" data-page="' + (d.current_page - 1) + '" style="border-radius:6px;font-size:.75rem;padding:2px 10px;">Prev</button>';
                if (d.current_page < d.last_page) pHtml += '<button class="btn btn-xs btn-outline-secondary page-btn" data-page="' + (d.current_page + 1) + '" style="border-radius:6px;font-size:.75rem;padding:2px 10px;">Next</button>';
                pHtml += '</div>';
                $('#jcPagination').html(pHtml).show();
            } else {
                $('#jcPagination').hide();
            }
        }).fail(function(xhr) {
            $('#jcTbody').html('<tr><td colspan="8" class="text-center text-danger py-4" style="font-size:.82rem;">Error loading job cards. Please refresh.</td></tr>');
            console.error('Job cards API error:', xhr.responseText);
        });
    }

    loadJc();

    $(document).on('click', '.filter-tab', function() {
        $('.filter-tab').removeClass('active');
        $(this).addClass('active');
        currentStatus = $(this).data('status');
        loadJc(1);
    });

    $('#searchJc').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() { loadJc(1); }, 400);
    });

    $(document).on('click', '.page-btn', function() {
        loadJc($(this).data('page'));
    });
});
</script>
@endpush

</x-app-layout>