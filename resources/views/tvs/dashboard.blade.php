<x-app-layout :title="$title">

@push('css')
<style>
    .tvs-hero {
        background: linear-gradient(135deg, #1e2d6b 0%, #273d80 40%, #c0172b 100%);
        border-radius: 12px;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .tvs-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.10) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.10) 1px, transparent 1px);
        background-size: 28px 28px;
        mask-image: radial-gradient(ellipse at 80% 50%, black 20%, transparent 75%);
        pointer-events: none;
    }
    .stat-card { border-radius: 10px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,.07); transition: transform .18s, box-shadow .18s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.12); }
    .status-badge { font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
    .badge-pending    { background: #fff3cd; color: #856404; }
    .badge-inprogress { background: #cfe2ff; color: #084298; }
    .badge-completed  { background: #d1e7dd; color: #0a3622; }
    .badge-delivered  { background: #e2d9f3; color: #432874; }
    .quick-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; background: #f8f9fa; border: 1px solid #e9ecef; text-decoration: none; color: inherit; transition: all .18s; margin-bottom: 8px; }
    .quick-link:hover { background: #fff; border-color: #c0172b; color: #c0172b; transform: translateX(4px); }
    .quick-link-icon { width: 38px; height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>
@endpush

<div class="container-fluid">

    {{-- Hero --}}
    <div class="tvs-hero">
        <div style="font-size:.78rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;">TVS Service Management</div>
        <h2 style="font-size:1.6rem;font-weight:800;color:#fff;margin:0 0 6px;line-height:1.2;">Car & General Tanzania</h2>
        <p style="font-size:.88rem;color:rgba(255,255,255,.72);margin:0;">Centralized workshop management – 2W &amp; 3W vehicles</p>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-2 mb-3" id="statsRow">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100" style="border-top:3px solid #273d80;">
                <div class="card-body" style="padding:.85rem .9rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;background:rgba(39,61,128,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bx bx-file" style="font-size:1.4rem;color:#273d80;"></i>
                        </div>
                        <div>
                            <div class="fw-800 text-dark" style="font-size:1.5rem;line-height:1;" id="stat-total-jc">—</div>
                            <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">Total Job Cards Today</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100" style="border-top:3px solid #f59e0b;">
                <div class="card-body" style="padding:.85rem .9rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;background:rgba(245,158,11,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bx bx-wrench" style="font-size:1.4rem;color:#f59e0b;"></i>
                        </div>
                        <div>
                            <div class="fw-800 text-dark" style="font-size:1.5rem;line-height:1;" id="stat-inprogress">—</div>
                            <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">In Progress</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100" style="border-top:3px solid #22c55e;">
                <div class="card-body" style="padding:.85rem .9rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;background:rgba(34,197,94,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bx bx-check-circle" style="font-size:1.4rem;color:#22c55e;"></i>
                        </div>
                        <div>
                            <div class="fw-800 text-dark" style="font-size:1.5rem;line-height:1;" id="stat-completed">—</div>
                            <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">Completed Today</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100" style="border-top:3px solid #c0172b;">
                <div class="card-body" style="padding:.85rem .9rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;background:rgba(192,23,43,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bx bx-car" style="font-size:1.4rem;color:#c0172b;"></i>
                        </div>
                        <div>
                            <div class="fw-800 text-dark" style="font-size:1.5rem;line-height:1;" id="stat-vehicles">—</div>
                            <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">Vehicles in System</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Recent Job Cards --}}
        <div class="col-xl-8">
            <div class="card" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(0,0,0,.07);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
                    <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-list-ul me-2" style="color:#273d80;"></i>Recent Job Cards</h6>
                    <a href="{{ route('tvs.job-cards') }}" class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;padding:4px 14px;">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:.83rem;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="px-3 py-2 fw-600 text-muted" style="font-size:.75rem;">Job Card #</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Vehicle</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Customer</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Service</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Status</th>
                                    <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Priority</th>
                                </tr>
                            </thead>
                            <tbody id="recentJobCards">
                                <tr><td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem;">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="col-xl-4">
            <div class="card" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(0,0,0,.07);">
                <div class="card-header" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
                    <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-grid-alt me-2" style="color:#c0172b;"></i>Quick Actions</h6>
                </div>
                <div class="card-body" style="padding:1rem;">
                    <a href="{{ route('tvs.job-cards.create') }}" class="quick-link">
                        <div class="quick-link-icon" style="background:rgba(39,61,128,.12);"><i class="bx bx-plus-circle" style="color:#273d80;font-size:1.2rem;"></i></div>
                        <div><div class="fw-600" style="font-size:.85rem;">New Job Card</div><div class="text-muted" style="font-size:.72rem;">Create a reception job card</div></div>
                    </a>
                    <a href="{{ route('tvs.vehicles') }}" class="quick-link">
                        <div class="quick-link-icon" style="background:rgba(192,23,43,.10);"><i class="bx bx-search-alt" style="color:#c0172b;font-size:1.2rem;"></i></div>
                        <div><div class="fw-600" style="font-size:.85rem;">Search Vehicle</div><div class="text-muted" style="font-size:.72rem;">By reg / chassis / engine no</div></div>
                    </a>
                    <a href="{{ route('tvs.parties.create') }}" class="quick-link">
                        <div class="quick-link-icon" style="background:rgba(34,197,94,.10);"><i class="bx bx-user-plus" style="color:#22c55e;font-size:1.2rem;"></i></div>
                        <div><div class="fw-600" style="font-size:.85rem;">Register Customer</div><div class="text-muted" style="font-size:.72rem;">Create new party / customer</div></div>
                    </a>
                    <a href="{{ route('tvs.gate-passes') }}" class="quick-link">
                        <div class="quick-link-icon" style="background:rgba(245,158,11,.10);"><i class="bx bx-door-open" style="color:#f59e0b;font-size:1.2rem;"></i></div>
                        <div><div class="fw-600" style="font-size:.85rem;">Gate Pass</div><div class="text-muted" style="font-size:.72rem;">Scan & release vehicles</div></div>
                    </a>
                    <a href="{{ route('tvs.reports') }}" class="quick-link">
                        <div class="quick-link-icon" style="background:rgba(99,102,241,.10);"><i class="bx bx-bar-chart-alt-2" style="color:#6366f1;font-size:1.2rem;"></i></div>
                        <div><div class="fw-600" style="font-size:.85rem;">Reports & Analytics</div><div class="text-muted" style="font-size:.72rem;">TAT, revenue, CLV</div></div>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
$(function () {
    var base = "{{ url('/api/tvs') }}";

    // Load daily summary stats
    $.get(base + '/reports/daily-summary', function (res) {
        if (res.success && res.data) {
            var d = res.data;
            $('#stat-total-jc').text((d.total_job_cards ?? 0));
            $('#stat-inprogress').text((d.in_progress ?? 0));
            $('#stat-completed').text((d.completed ?? 0));
        }
    }).fail(function () {
        $('#stat-total-jc, #stat-inprogress, #stat-completed').text('–');
    });

    // Load vehicle count
    $.get(base + '/vehicles', function (res) {
        if (res.success) {
            $('#stat-vehicles').text(res.data.total ?? '–');
        }
    });

    // Load recent job cards
    $.get(base + '/job-cards', function (res) {
        var $tbody = $('#recentJobCards');
        if (!res.success || !res.data || !res.data.data || res.data.data.length === 0) {
            $tbody.html('<tr><td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem;">No job cards found.</td></tr>');
            return;
        }
        var html = '';
        res.data.data.slice(0, 8).forEach(function (jc) {
            var badgeClass = {
                'Pending': 'badge-pending',
                'In Progress': 'badge-inprogress',
                'Completed': 'badge-completed',
                'Delivered': 'badge-delivered'
            }[jc.status?.name] || 'badge-pending';
            var priorityColor = { 'Urgent': 'text-warning', 'Emergency': 'text-danger' }[jc.priority] || 'text-muted';
            html += '<tr style="cursor:pointer;" onclick="window.location=\'{{ url(\'tvs/job-cards\') }}/\' + ' + jc.id + '">' +
                '<td class="px-3 py-2"><span class="fw-600" style="color:#273d80;">' + (jc.job_card_no || '—') + '</span></td>' +
                '<td class="py-2">' + (jc.vehicle?.registration_no || jc.vehicle?.chassis_no || '—') + '</td>' +
                '<td class="py-2">' + (jc.customer_party?.name || '—') + '</td>' +
                '<td class="py-2">' + (jc.service_type?.name || '—') + '</td>' +
                '<td class="py-2"><span class="status-badge ' + badgeClass + '">' + (jc.status?.name || '—') + '</span></td>' +
                '<td class="py-2"><span class="fw-600 ' + priorityColor + '">' + (jc.priority || '—') + '</span></td>' +
                '</tr>';
        });
        $tbody.html(html);
    }).fail(function () {
        $('#recentJobCards').html('<tr><td colspan="6" class="text-center text-muted py-4">Could not load data.</td></tr>');
    });
});
</script>
@endpush

</x-app-layout>
