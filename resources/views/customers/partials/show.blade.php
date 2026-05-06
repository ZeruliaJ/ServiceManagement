<div class="row g-3 p-2">
    <div class="col-6">
        <small class="text-muted d-block">Customer Code</small>
        <strong>{{ $customer->customer_code }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Customer Type</small>
        <strong>{{ $customer->customer_type }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">First Name</small>
        <strong>{{ $customer->first_name }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Last Name</small>
        <strong>{{ $customer->last_name }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Phone Number</small>
        <strong>{{ $customer->phone_number }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Alternate Phone</small>
        <strong>{{ $customer->alternate_phone ?? '—' }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Email</small>
        <strong>{{ $customer->email ?? '—' }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Status</small>
        <strong>{{ $customer->status }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Region</small>
        <strong>{{ $customer->state }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">District</small>
        <strong>{{ $customer->pincode }}</strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Town</small>
        <strong>{{ $customer->city }}</strong>
    </div>
    <div class="col-12">
        <small class="text-muted d-block">Address</small>
        <strong>{{ $customer->address_line1 }}{{ $customer->address_line2 ? ', '.$customer->address_line2 : '' }}</strong>
    </div>
    <div class="col-12">
        <small class="text-muted d-block">Notes</small>
        <strong>{{ $customer->notes ?? '—' }}</strong>
    </div>
</div>