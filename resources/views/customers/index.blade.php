<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero {
        background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%);
        border-radius: 12px;
        padding: 22px 28px;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
    }

    .page-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,.09) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.09) 1px, transparent 1px);
        background-size: 28px 28px;
        mask-image: radial-gradient(ellipse at 80% 50%, black 20%, transparent 75%);
        pointer-events: none;
    }

    .info-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
    }

    .table thead th {
        font-size: .75rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        border-bottom: 1px solid #e5e7eb;
        padding: 14px 12px;
        white-space: nowrap;
    }

    .table tbody td {
        font-size: .83rem;
        color: #1e2a4a;
        padding: 14px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .table tbody tr:hover {
        background: #f9fafb;
    }

    .badge-active {
        background: #d1e7dd;
        color: #0a3622;
        font-size: .7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
    }

    .badge-inactive {
        background: #f8d7da;
        color: #842029;
        font-size: .7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: white;
        font-size: .9rem;
    }

    .btn-view { background: #273d80; }
    .btn-edit { background: #22c55e; }
    .btn-delete { background: #c0172b; }
</style>
@endpush

<div class="container-fluid">

    {{-- Hero --}}
    <div class="page-hero">
        <a href="{{ route('dashboard') }}"
           class="btn btn-sm mb-2"
           style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;font-size:.78rem;">
            <i class="bx bx-arrow-back me-1"></i>Back
        </a>

        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">
            Customers Management
        </h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">
            View and manage registered customers
        </p>
    </div>

    {{-- Table Card --}}
    <div class="card info-card">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
            <h6 class="mb-0 fw-700" style="color:#1e2a4a;font-size:.85rem;">
                <i class="bx bx-user me-2" style="color:#c0172b;"></i>Customers List
            </h6>

            <a href="{{ route('customers.create') }}"
               class="btn"
               style="background:#c0172b;color:#fff;border-radius:8px;font-size:.8rem;font-weight:600;">
                <i class="bx bx-plus me-1"></i>Add Customer
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Branch</th>
                            <th>Registration</th>
                            <th>Vehicles</th>
                            <th>Service History</th>
                            <th>Status</th>
                            <th width="130">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                               <td>{{ $customer->customer_code }}</td>
<td class="fw-700">{{ $customer->first_name }} {{ $customer->last_name }}</td>
<td>{{ $customer->phone_number }}</td>
<td>{{ $customer->city }}, {{ $customer->state }}</td>
<td>{{ $customer->customer_type }}</td>
<td>{{ $customer->registration_date ? \Carbon\Carbon::parse($customer->registration_date)->format('d-M-Y') : '—' }}</td>
<td>
    @if($customer->vehicles_count > 0)
        <span title="{{ $customer->vehicles->pluck('vehicle_model')->join(', ') }}">
            {{ $customer->vehicles_count }} 
            ({{ $customer->vehicles->pluck('vehicle_model')->join(', ') }})
        </span>
    @else
        <span class="text-muted">0</span>
    @endif
</td>

{{-- Last Service column --}}
<td>
    @php
        $lastService = $customer->vehicles->whereNotNull('last_service_date')->sortByDesc('last_service_date')->first();
    @endphp
    {{ $lastService ? \Carbon\Carbon::parse($lastService->last_service_date)->format('d-M-Y') : '—' }}
</td>
<td>
    @if($customer->status == 'active')
        <span class="badge-active">ACTIVE</span>
    @else
        <span class="badge-inactive">INACTIVE</span>
    @endif
</td>
                                <td>
                                   <a href="javascript:void(0)"
   class="action-btn btn-view"
   onclick="viewCustomer({{ $customer->id }})">
    <i class="bx bx-show"></i>
</a>
                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                       class="action-btn btn-edit">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    <form action="{{ route('customers.destroy', $customer->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    No customers found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e2d6b,#c0172b);border-radius:12px 12px 0 0;">
                <h6 class="modal-title text-white fw-bold">
                    <i class="bx bx-user me-2"></i>Customer Details
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="customerModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function viewCustomer(id) {
    $('#customerModalBody').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    `);
    $('#customerModal').modal('show');

    $.ajax({
        url: '/customers/' + id,
        type: 'GET',
        success: function (response) {
            $('#customerModalBody').html(response);
        },
        error: function () {
            $('#customerModalBody').html('<p class="text-danger text-center">Failed to load customer.</p>');
        }
    });
}
</script>

</x-app-layout>