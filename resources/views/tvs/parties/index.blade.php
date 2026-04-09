<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .info-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .party-type-badge { font-size:.7rem; font-weight:700; padding:3px 9px; border-radius:20px; }
    .pt-retail   { background:#d1e7dd; color:#0a3622; }
    .pt-dealer   { background:#cfe2ff; color:#084298; }
    .pt-finance  { background:#fff3cd; color:#856404; }
    .pt-bank     { background:#e2d9f3; color:#432874; }
    .pt-inst     { background:#f8d7da; color:#842029; }
    .pt-default  { background:#e9ecef; color:#495057; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <div style="font-size:.75rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">TVS Service</div>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">Customer / Party Management</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">Manage all customers, dealers, finance companies, banks, and institutions.</p>
    </div>

    <div class="card info-card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.85rem 1.1rem;">
            <h6 class="mb-0 fw-700" style="color:#1e2a4a;"><i class="bx bx-group me-2" style="color:#273d80;"></i>Parties</h6>
            <div class="d-flex gap-2">
                <select id="filterPartyType" class="form-select form-select-sm" style="width:180px;border-radius:7px;font-size:.8rem;">
                    <option value="">All Types</option>
                </select>
                <input type="text" id="searchParty" class="form-control form-control-sm" placeholder="Search name / phone / code..." style="width:220px;border-radius:7px;font-size:.8rem;">
                <a href="{{ route('tvs.parties.create') }}" class="btn btn-sm" style="background:#273d80;color:#fff;border-radius:7px;font-size:.78rem;white-space:nowrap;"><i class="bx bx-plus me-1"></i>New Party</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.83rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="px-3 py-2 fw-600 text-muted" style="font-size:.75rem;">#</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Party Code</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Name</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Type</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Phone</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Email</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">TIN</th>
                            <th class="py-2 fw-600 text-muted" style="font-size:.75rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="partiesTbody">
                        <tr><td colspan="8" class="text-center text-muted py-4">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="partiesPagination" class="d-flex justify-content-between align-items-center px-3 py-2 border-top" style="font-size:.8rem;"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var base = "{{ url('/api/tvs') }}";
    var webBase = "{{ url('/tvs') }}";
    var searchTimer;

    // Load party types
    $.get(base + '/parties/types', function(res) {
        if (res.success) {
            res.data.forEach(function(pt) {
                $('#filterPartyType').append('<option value="' + pt.id + '">' + pt.name + '</option>');
            });
        }
    });

    function getTypeBadge(typeName) {
        var map = { 'Retail Customer': 'pt-retail', 'Dealer': 'pt-dealer', 'Finance Company': 'pt-finance', 'Bank': 'pt-bank', 'Institution': 'pt-inst' };
        var cls = map[typeName] || 'pt-default';
        return '<span class="party-type-badge ' + cls + '">' + (typeName || '—') + '</span>';
    }

    function loadParties(page) {
        page = page || 1;
        var params = { page: page };
        var typeVal = $('#filterPartyType').val();
        var searchVal = $('#searchParty').val().trim();
        if (typeVal) params.party_type_id = typeVal;
        if (searchVal) params.search = searchVal;

        $.get(base + '/parties', params, function(res) {
            var $tbody = $('#partiesTbody');
            if (!res.success || !res.data.data || res.data.data.length === 0) {
                $tbody.html('<tr><td colspan="8" class="text-center text-muted py-4" style="font-size:.82rem;">No parties found.</td></tr>');
                $('#partiesPagination').hide();
                return;
            }
            var html = '';
            res.data.data.forEach(function(p, i) {
                html += '<tr>' +
                    '<td class="px-3 py-2 text-muted" style="font-size:.75rem;">' + ((page-1)*15 + i+1) + '</td>' +
                    '<td class="py-2 fw-600" style="color:#273d80;">' + (p.party_code || '—') + '</td>' +
                    '<td class="py-2 fw-600">' + (p.name || '—') + '</td>' +
                    '<td class="py-2">' + getTypeBadge(p.party_type?.name) + '</td>' +
                    '<td class="py-2">' + (p.phone || '—') + '</td>' +
                    '<td class="py-2">' + (p.email || '—') + '</td>' +
                    '<td class="py-2">' + (p.tin_no || '—') + '</td>' +
                    '<td class="py-2"><a href="' + webBase + '/parties/' + p.id + '" class="btn btn-xs" style="background:#273d80;color:#fff;border-radius:6px;font-size:.72rem;padding:2px 10px;"><i class="bx bx-show me-1"></i>View</a></td>' +
                    '</tr>';
            });
            $tbody.html(html);

            var d = res.data;
            if (d.last_page > 1) {
                var pHtml = '<span class="text-muted">Page ' + d.current_page + ' of ' + d.last_page + ' (' + d.total + ' records)</span><div class="d-flex gap-1">';
                if (d.current_page > 1) pHtml += '<button class="btn btn-xs btn-outline-secondary page-btn" data-page="' + (d.current_page-1) + '" style="border-radius:6px;font-size:.75rem;padding:2px 10px;">Prev</button>';
                if (d.current_page < d.last_page) pHtml += '<button class="btn btn-xs btn-outline-secondary page-btn" data-page="' + (d.current_page+1) + '" style="border-radius:6px;font-size:.75rem;padding:2px 10px;">Next</button>';
                pHtml += '</div>';
                $('#partiesPagination').html(pHtml).show();
            } else {
                $('#partiesPagination').hide();
            }
        });
    }

    loadParties();
    $('#filterPartyType').on('change', function() { loadParties(1); });
    $('#searchParty').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() { loadParties(1); }, 400);
    });
    $(document).on('click', '.page-btn', function() { loadParties($(this).data('page')); });
});
</script>
@endpush

</x-app-layout>
