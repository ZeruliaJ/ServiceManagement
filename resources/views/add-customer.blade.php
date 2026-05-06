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
                                <td>#{{ $customer->id }}</td>
                                <td class="fw-700">{{ $customer->name }}</td>
                                <td>{{ $customer->contact }}</td>
                                <td>{{ $customer->location }}</td>
                                <td>{{ $customer->branch }}</td>
                                <td>{{ $customer->registration_no ?? '—' }}</td>
                                <td>{{ $customer->vehicles_count ?? 0 }}</td>
                                <td>{{ $customer->service_history_count ?? 0 }}</td>
                                <td>
                                    @if($customer->status == 'active')
                                        <span class="badge-active">ACTIVE</span>
                                    @else
                                        <span class="badge-inactive">INACTIVE</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('customers.show', $customer->id) }}"
                                       class="action-btn btn-view">
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
</x-app-layout>