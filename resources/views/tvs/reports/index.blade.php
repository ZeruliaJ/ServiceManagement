<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .panel { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .panel-hdr { background:#fff; border-bottom:1px solid #f0f0f0; padding:.7rem 1rem; border-radius:10px 10px 0 0; }
    .panel-body { padding:1rem; background:#fff; border-radius:0 0 10px 10px; }
    .tab-pill { padding:6px 16px; border-radius:7px; font-size:.8rem; font-weight:600; cursor:pointer; border:1px solid #e9ecef; color:#6b7280; background:#f8f9fa; transition:.18s; }
    .tab-pill.active { background:#273d80; color:#fff; border-color:#273d80; }
    .kpi-box { border-radius:10px; padding:14px 16px; text-align:center; border:1px solid #e9ecef; background:#f8f9fa; }
    .kpi-val { font-size:1.4rem; font-weight:800; color:#273d80; line-height:1; }
    .kpi-label { font-size:.72rem; color:#6b7280; font-weight:600; margin-top:5px; }
    .bar-wrap { background:#e9ecef; border-radius:8px; height:8px; overflow:hidden; margin-top:6px; }
    .bar-fill { height:100%; border-radius:8px; background:linear-gradient(90deg,#273d80,#c0172b); transition:width .6s; }
    .report-table th { font-size:.73rem; font-weight:700; color:#6b7280; background:#f8f9fa; padding:7px 10px; }
    .report-table td { font-size:.8rem; padding:7px 10px; vertical-align:middle; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <div style="font-size:.75rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">TVS Service</div>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">Reports & Analytics</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">TAT metrics, branch summaries, warranty mix, vehicle lifetime, customer CLV.</p>
    </div>

    {{-- Date range filters --}}
    <div class="card panel mb-3">
        <div class="panel-body" style="padding:.85rem 1rem;">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-600" style="font-size:.75rem;">Date From</label>
                    <input type="date" id="filterFrom" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600" style="font-size:.75rem;">Date To</label>
                    <input type="date" id="filterTo" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600" style="font-size:.75rem;">Branch</label>
                    <select id="filterBranch" class="form-select form-select-sm">
                        <option value="">All Branches</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="btnApplyFilter" class="btn btn-sm w-100" style="background:#273d80;color:#fff;border-radius:7px;font-size:.83rem;"><i class="bx bx-filter me-1"></i>Apply Filters</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Tabs --}}
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <span class="tab-pill active" data-tab="summary">Daily Summary</span>
        <span class="tab-pill" data-tab="tat">TAT Metrics</span>
        <span class="tab-pill" data-tab="branch">Branch Report</span>
        <span class="tab-pill" data-tab="warranty-mix">Warranty Mix</span>
        <span class="tab-pill" data-tab="free-service">Free Service</span>
        <span class="tab-pill" data-tab="repeat">Repeat Repairs</span>
    </div>

    {{-- DAILY SUMMARY --}}
    <div id="tab-summary">
        <div class="row g-2 mb-3" id="summaryKpis">
            <div class="col text-center py-4"><i class="bx bx-loader bx-spin" style="font-size:1.5rem;color:#273d80;"></i></div>
        </div>
        <div class="panel">
            <div class="panel-hdr"><h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-calendar me-2" style="color:#273d80;"></i>Daily Branch Summary</h6></div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table report-table mb-0">
                        <thead><tr><th>Date</th><th>Branch</th><th>Total JCs</th><th>Pending</th><th>In Progress</th><th>Completed</th><th>Delivered</th><th>Revenue (TZS)</th></tr></thead>
                        <tbody id="summaryTbody"><tr><td colspan="8" class="text-center text-muted py-3">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- TAT METRICS --}}
    <div id="tab-tat" style="display:none;">
        <div class="row g-2 mb-3" id="tatKpis">
            <div class="col text-center py-4"><i class="bx bx-loader bx-spin" style="font-size:1.5rem;color:#273d80;"></i></div>
        </div>
        <div class="panel">
            <div class="panel-hdr"><h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-time me-2" style="color:#f59e0b;"></i>Turn-Around Time (TAT) Breakdown</h6></div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table report-table mb-0">
                        <thead><tr><th>Branch</th><th>Check-In → Job Open (avg)</th><th>Job Open → Completed (avg)</th><th>Completed → Delivered (avg)</th><th>Delivered → Gate Out (avg)</th><th>Total TAT (avg)</th></tr></thead>
                        <tbody id="tatTbody"><tr><td colspan="6" class="text-center text-muted py-3">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- BRANCH REPORT --}}
    <div id="tab-branch" style="display:none;">
        <div class="panel">
            <div class="panel-hdr"><h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-building me-2" style="color:#c0172b;"></i>Branch Performance Report</h6></div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table report-table mb-0">
                        <thead><tr><th>Branch</th><th>Region</th><th>Total JCs</th><th>2W JCs</th><th>3W JCs</th><th>Revenue (TZS)</th><th>Warranty Claims</th><th>Paid Services</th></tr></thead>
                        <tbody id="branchTbody"><tr><td colspan="8" class="text-center text-muted py-3">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- WARRANTY MIX --}}
    <div id="tab-warranty-mix" style="display:none;">
        <div class="row g-2 mb-3">
            <div class="col-md-4"><div class="kpi-box"><div class="kpi-val" id="wm-paid">—</div><div class="kpi-label">Customer Pay Jobs</div></div></div>
            <div class="col-md-4"><div class="kpi-box"><div class="kpi-val" id="wm-warranty">—</div><div class="kpi-label">Warranty Jobs</div></div></div>
            <div class="col-md-4"><div class="kpi-box"><div class="kpi-val" id="wm-goodwill">—</div><div class="kpi-label">Goodwill / Campaign</div></div></div>
        </div>
        <div class="panel">
            <div class="panel-hdr"><h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-pie-chart me-2" style="color:#22c55e;"></i>Warranty vs Customer Pay Mix</h6></div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table report-table mb-0">
                        <thead><tr><th>Branch</th><th>Paid</th><th>Warranty</th><th>Goodwill</th><th>Campaign</th><th>Warranty %</th></tr></thead>
                        <tbody id="wmTbody"><tr><td colspan="6" class="text-center text-muted py-3">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- FREE SERVICE --}}
    <div id="tab-free-service" style="display:none;">
        <div class="row g-2 mb-3">
            <div class="col-md-3"><div class="kpi-box"><div class="kpi-val" id="fs-total">—</div><div class="kpi-label">Total Free Services</div></div></div>
            <div class="col-md-3"><div class="kpi-box"><div class="kpi-val" id="fs-converted">—</div><div class="kpi-label">Converted to Paid</div></div></div>
            <div class="col-md-3"><div class="kpi-box"><div class="kpi-val" id="fs-rate">—%</div><div class="kpi-label">Conversion Rate</div></div></div>
            <div class="col-md-3"><div class="kpi-box"><div class="kpi-val" id="fs-revenue">—</div><div class="kpi-label">Revenue (Converted)</div></div></div>
        </div>
        <div class="panel">
            <div class="panel-hdr"><h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-gift me-2" style="color:#6366f1;"></i>Free Service Conversion Analysis</h6></div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table report-table mb-0">
                        <thead><tr><th>Branch</th><th>Free Services</th><th>Converted</th><th>Conversion %</th><th>Revenue (TZS)</th></tr></thead>
                        <tbody id="fsTbody"><tr><td colspan="5" class="text-center text-muted py-3">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- REPEAT REPAIRS --}}
    <div id="tab-repeat" style="display:none;">
        <div class="panel">
            <div class="panel-hdr">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-700" style="font-size:.84rem;color:#1e2a4a;"><i class="bx bx-refresh me-2" style="color:#c0172b;"></i>Repeat Repair Analysis</h6>
                    <div class="d-flex gap-2">
                        <input type="text" id="repeatVehicleSearch" class="form-control form-control-sm" placeholder="Search by reg / chassis..." style="width:200px;border-radius:7px;font-size:.8rem;">
                        <button id="btnRepeatSearch" class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;">Search</button>
                    </div>
                </div>
            </div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table report-table mb-0">
                        <thead><tr><th>Vehicle (Reg)</th><th>Chassis</th><th>Repeat Issue</th><th>Count</th><th>First Reported</th><th>Last Reported</th><th></th></tr></thead>
                        <tbody id="repeatTbody"><tr><td colspan="7" class="text-center text-muted py-3">Search a vehicle to see repeat repair analysis.</td></tr></tbody>
                    </table>
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

    // Set default date range (last 30 days)
    var today = new Date();
    var from = new Date(); from.setDate(today.getDate() - 30);
    $('#filterTo').val(today.toISOString().slice(0, 10));
    $('#filterFrom').val(from.toISOString().slice(0, 10));

    function fmtMoney(v) { return 'TZS ' + (parseFloat(v) || 0).toLocaleString(undefined, {minimumFractionDigits:0}); }
    function fmtHrs(v) { return v ? (parseFloat(v).toFixed(1) + ' hrs') : '—'; }

    function getFilters() {
        return { from: $('#filterFrom').val(), to: $('#filterTo').val(), branch_id: $('#filterBranch').val() };
    }

    // Tab switching
    $(document).on('click', '.tab-pill', function() {
        $('.tab-pill').removeClass('active');
        $(this).addClass('active');
        var tab = $(this).data('tab');
        $('[id^="tab-"]').hide();
        $('#tab-' + tab).show();
        loadTab(tab);
    });

    function loadTab(tab) {
        var filters = getFilters();
        if (tab === 'summary') loadSummary(filters);
        else if (tab === 'tat') loadTAT(filters);
        else if (tab === 'branch') loadBranch(filters);
        else if (tab === 'warranty-mix') loadWarrantyMix(filters);
        else if (tab === 'free-service') loadFreeService(filters);
    }

    function loadSummary(filters) {
        $.get(base + '/reports/daily-summary', filters, function(res) {
            if (res.success && res.data) {
                var d = res.data;
                var kpis = '<div class="col-md-2 col-6"><div class="kpi-box"><div class="kpi-val">' + (d.total_job_cards || 0) + '</div><div class="kpi-label">Total Job Cards</div></div></div>' +
                    '<div class="col-md-2 col-6"><div class="kpi-box"><div class="kpi-val">' + (d.pending || 0) + '</div><div class="kpi-label">Pending</div></div></div>' +
                    '<div class="col-md-2 col-6"><div class="kpi-box"><div class="kpi-val">' + (d.in_progress || 0) + '</div><div class="kpi-label">In Progress</div></div></div>' +
                    '<div class="col-md-2 col-6"><div class="kpi-box"><div class="kpi-val">' + (d.completed || 0) + '</div><div class="kpi-label">Completed</div></div></div>' +
                    '<div class="col-md-2 col-6"><div class="kpi-box"><div class="kpi-val">' + (d.delivered || 0) + '</div><div class="kpi-label">Delivered</div></div></div>' +
                    '<div class="col-md-2 col-6"><div class="kpi-box"><div class="kpi-val">' + fmtMoney(d.total_revenue) + '</div><div class="kpi-label">Revenue</div></div></div>';
                $('#summaryKpis').html(kpis);

                var rows = d.by_branch || (d.branch_id ? [d] : [d]);
                var html = '';
                (Array.isArray(rows) ? rows : [rows]).forEach(function(r) {
                    html += '<tr>' +
                        '<td class="px-3 py-2">' + (r.date || filters.to || '—') + '</td>' +
                        '<td>' + (r.branch?.name || r.branch_name || 'All Branches') + '</td>' +
                        '<td class="fw-700">' + (r.total_job_cards || 0) + '</td>' +
                        '<td><span style="color:#856404;">' + (r.pending || 0) + '</span></td>' +
                        '<td><span style="color:#084298;">' + (r.in_progress || 0) + '</span></td>' +
                        '<td><span style="color:#0a3622;">' + (r.completed || 0) + '</span></td>' +
                        '<td><span style="color:#432874;">' + (r.delivered || 0) + '</span></td>' +
                        '<td class="fw-700" style="color:#273d80;">' + fmtMoney(r.total_revenue) + '</td>' +
                        '</tr>';
                });
                $('#summaryTbody').html(html || '<tr><td colspan="8" class="text-center text-muted py-3">No data.</td></tr>');
            }
        }).fail(function() {
            $('#summaryKpis').html('<div class="col text-center text-muted py-2">Could not load summary.</div>');
        });
    }

    function loadTAT(filters) {
        $.get(base + '/reports/branch-tat-metrics', filters, function(res) {
            if (res.success && res.data) {
                var rows = Array.isArray(res.data) ? res.data : [res.data];
                var kpis = '';
                var totals = { checkin_to_open: 0, open_to_complete: 0, complete_to_deliver: 0, deliver_to_gate: 0, total: 0 };
                rows.forEach(function(r) {
                    Object.keys(totals).forEach(function(k) { totals[k] += parseFloat(r[k] || 0); });
                });
                var n = rows.length || 1;
                kpis = '<div class="col"><div class="kpi-box"><div class="kpi-val">' + (totals.checkin_to_open / n).toFixed(1) + ' hrs</div><div class="kpi-label">Check-in → Job Open</div></div></div>' +
                    '<div class="col"><div class="kpi-box"><div class="kpi-val">' + (totals.open_to_complete / n).toFixed(1) + ' hrs</div><div class="kpi-label">Job Open → Complete</div></div></div>' +
                    '<div class="col"><div class="kpi-box"><div class="kpi-val">' + (totals.complete_to_deliver / n).toFixed(1) + ' hrs</div><div class="kpi-label">Complete → Deliver</div></div></div>' +
                    '<div class="col"><div class="kpi-box"><div class="kpi-val">' + (totals.deliver_to_gate / n).toFixed(1) + ' hrs</div><div class="kpi-label">Deliver → Gate Out</div></div></div>' +
                    '<div class="col"><div class="kpi-box"><div class="kpi-val fw-900" style="color:#c0172b;">' + (totals.total / n).toFixed(1) + ' hrs</div><div class="kpi-label">Total TAT (avg)</div></div></div>';
                $('#tatKpis').html(kpis);
                var html = '';
                rows.forEach(function(r) {
                    html += '<tr>' +
                        '<td class="px-3 py-2 fw-600">' + (r.branch?.name || r.branch_name || '—') + '</td>' +
                        '<td>' + fmtHrs(r.checkin_to_open) + '</td>' +
                        '<td>' + fmtHrs(r.open_to_complete) + '</td>' +
                        '<td>' + fmtHrs(r.complete_to_deliver) + '</td>' +
                        '<td>' + fmtHrs(r.deliver_to_gate) + '</td>' +
                        '<td class="fw-700" style="color:#c0172b;">' + fmtHrs(r.total) + '</td>' +
                        '</tr>';
                });
                $('#tatTbody').html(html || '<tr><td colspan="6" class="text-center text-muted py-3">No data.</td></tr>');
            }
        }).fail(function() { $('#tatTbody').html('<tr><td colspan="6" class="text-center text-muted py-3">Could not load TAT data.</td></tr>'); });
    }

    function loadBranch(filters) {
        $.get(base + '/reports/branch-report', filters, function(res) {
            if (res.success && res.data) {
                var rows = Array.isArray(res.data) ? res.data : [res.data];
                var html = '';
                rows.forEach(function(r) {
                    html += '<tr>' +
                        '<td class="px-3 py-2 fw-600">' + (r.branch?.name || r.branch_name || '—') + '</td>' +
                        '<td>' + (r.branch?.region || r.region || '—') + '</td>' +
                        '<td class="fw-700">' + (r.total_job_cards || 0) + '</td>' +
                        '<td>' + (r.two_wheeler_jcs || 0) + '</td>' +
                        '<td>' + (r.three_wheeler_jcs || 0) + '</td>' +
                        '<td class="fw-700" style="color:#273d80;">' + fmtMoney(r.total_revenue) + '</td>' +
                        '<td>' + (r.warranty_claims || 0) + '</td>' +
                        '<td>' + (r.paid_services || 0) + '</td>' +
                        '</tr>';
                });
                $('#branchTbody').html(html || '<tr><td colspan="8" class="text-center text-muted py-3">No data.</td></tr>');
            }
        }).fail(function() { $('#branchTbody').html('<tr><td colspan="8" class="text-center text-muted py-3">Could not load branch report.</td></tr>'); });
    }

    function loadWarrantyMix(filters) {
        $.get(base + '/reports/warranty-vs-customer-pay', filters, function(res) {
            if (res.success && res.data) {
                var d = Array.isArray(res.data) ? res.data[0] : res.data;
                var total = (d.paid || 0) + (d.warranty || 0) + (d.goodwill || 0) + (d.campaign || 0);
                $('#wm-paid').text(d.paid || 0);
                $('#wm-warranty').text(d.warranty || 0);
                $('#wm-goodwill').text((d.goodwill || 0) + (d.campaign || 0));
                var rows = Array.isArray(res.data) ? res.data : [d];
                var html = '';
                rows.forEach(function(r) {
                    var wPct = total > 0 ? ((r.warranty / total) * 100).toFixed(1) : 0;
                    html += '<tr>' +
                        '<td class="px-3 py-2 fw-600">' + (r.branch?.name || r.branch_name || 'All') + '</td>' +
                        '<td>' + (r.paid || 0) + '</td>' +
                        '<td>' + (r.warranty || 0) + '</td>' +
                        '<td>' + (r.goodwill || 0) + '</td>' +
                        '<td>' + (r.campaign || 0) + '</td>' +
                        '<td><div style="font-size:.8rem;font-weight:700;color:#273d80;">' + wPct + '%</div><div class="bar-wrap"><div class="bar-fill" style="width:' + wPct + '%;"></div></div></td>' +
                        '</tr>';
                });
                $('#wmTbody').html(html || '<tr><td colspan="6" class="text-center text-muted py-3">No data.</td></tr>');
            }
        }).fail(function() { $('#wmTbody').html('<tr><td colspan="6" class="text-center text-muted py-3">Could not load data.</td></tr>'); });
    }

    function loadFreeService(filters) {
        $.get(base + '/reports/free-service-conversion', filters, function(res) {
            if (res.success && res.data) {
                var d = Array.isArray(res.data) ? res.data[0] : res.data;
                $('#fs-total').text(d.total_free_services || 0);
                $('#fs-converted').text(d.converted || 0);
                var rate = d.total_free_services > 0 ? ((d.converted / d.total_free_services) * 100).toFixed(1) : 0;
                $('#fs-rate').text(rate + '%');
                $('#fs-revenue').text(fmtMoney(d.converted_revenue));
                var rows = Array.isArray(res.data) ? res.data : [d];
                var html = '';
                rows.forEach(function(r) {
                    var pct = r.total_free_services > 0 ? ((r.converted / r.total_free_services) * 100).toFixed(1) : 0;
                    html += '<tr>' +
                        '<td class="px-3 py-2 fw-600">' + (r.branch?.name || r.branch_name || 'All') + '</td>' +
                        '<td>' + (r.total_free_services || 0) + '</td>' +
                        '<td>' + (r.converted || 0) + '</td>' +
                        '<td><span style="font-weight:700;color:#273d80;">' + pct + '%</span></td>' +
                        '<td>' + fmtMoney(r.converted_revenue) + '</td>' +
                        '</tr>';
                });
                $('#fsTbody').html(html || '<tr><td colspan="5" class="text-center text-muted py-3">No data.</td></tr>');
            }
        }).fail(function() { $('#fsTbody').html('<tr><td colspan="5" class="text-center text-muted py-3">Could not load data.</td></tr>'); });
    }

    // Repeat repairs
    $('#btnRepeatSearch').on('click', function() {
        var q = $('#repeatVehicleSearch').val().trim();
        if (!q) return;
        $('#repeatTbody').html('<tr><td colspan="7" class="text-center py-3"><i class="bx bx-loader bx-spin" style="color:#273d80;"></i></td></tr>');
        // Search vehicle first
        $.get(base + '/vehicles/search', { registration_no: q, chassis_no: q }, function(res) {
            if (res.success && res.data) {
                $.get(base + '/reports/repeat-repairs/' + res.data.id, function(rr) {
                    if (rr.success && rr.data && rr.data.length > 0) {
                        var html = '';
                        rr.data.forEach(function(r) {
                            html += '<tr>' +
                                '<td class="px-3 py-2 fw-600" style="color:#273d80;">' + (r.registration_no || res.data.registration_no || '—') + '</td>' +
                                '<td>' + (r.chassis_no || res.data.chassis_no || '—') + '</td>' +
                                '<td>' + (r.issue || r.complaint || '—') + '</td>' +
                                '<td><span class="fw-700" style="color:#c0172b;">' + (r.count || 0) + 'x</span></td>' +
                                '<td>' + (r.first_reported || '—') + '</td>' +
                                '<td>' + (r.last_reported || '—') + '</td>' +
                                '<td><a href="' + webBase + '/vehicles/' + res.data.id + '" class="btn btn-xs" style="background:#273d80;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;">View History</a></td>' +
                                '</tr>';
                        });
                        $('#repeatTbody').html(html);
                    } else {
                        $('#repeatTbody').html('<tr><td colspan="7" class="text-center text-muted py-3">No repeat repairs found for this vehicle.</td></tr>');
                    }
                });
            } else {
                $('#repeatTbody').html('<tr><td colspan="7" class="text-center text-muted py-3">Vehicle not found.</td></tr>');
            }
        });
    });
    $('#repeatVehicleSearch').on('keypress', function(e) { if (e.which === 13) $('#btnRepeatSearch').click(); });

    $('#btnApplyFilter').on('click', function() {
        var tab = $('.tab-pill.active').data('tab');
        loadTab(tab);
    });

    // Load initial
    loadSummary(getFilters());
});
</script>
@endpush

</x-app-layout>
