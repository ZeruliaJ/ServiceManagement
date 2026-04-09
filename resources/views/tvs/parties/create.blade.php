<x-app-layout :title="$title">

@push('css')
<style>
    .page-hero { background: linear-gradient(135deg, #1e2d6b 0%, #273d80 60%, #c0172b 100%); border-radius: 12px; padding: 22px 28px; margin-bottom: 1.25rem; position: relative; overflow: hidden; }
    .page-hero::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,.09) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.09) 1px,transparent 1px); background-size:28px 28px; mask-image:radial-gradient(ellipse at 80% 50%,black 20%,transparent 75%); pointer-events:none; }
    .form-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .form-section-title { font-size:.8rem; font-weight:700; color:#273d80; text-transform:uppercase; letter-spacing:.5px; margin-bottom:12px; padding-bottom:8px; border-bottom:2px solid #e9ecef; }
    .ownership-rule { border-radius:8px; padding:12px 14px; font-size:.82rem; background:#f0f4ff; border-left:3px solid #273d80; margin-bottom:10px; display:none; }
    .ownership-rule.show { display:block; }
</style>
@endpush

<div class="container-fluid">

    <div class="page-hero">
        <a href="{{ route('tvs.parties') }}" class="btn btn-sm mb-2" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;font-size:.78rem;"><i class="bx bx-arrow-back me-1"></i>Back to Parties</a>
        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">New Customer / Party</h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">Register a new customer, dealer, finance company, bank, or institution.</p>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card form-card">
                <div class="card-body p-4">

                    <div class="form-section-title">Party Type & Classification</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.8rem;">Party Type <span class="text-danger">*</span></label>
                            <select id="partyType" class="form-select form-select-sm">
                                <option value="">Select party type...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.8rem;">Party Code</label>
                            <input type="text" id="partyCode" class="form-control form-control-sm" placeholder="Auto-generated if left blank">
                        </div>
                    </div>

                    {{-- Ownership rule hint --}}
                    <div id="ruleDealer" class="ownership-rule">
                        <strong>Dealer Sales:</strong> Customer code will be created at first service visit. Ensure dealer code is already in the system.
                    </div>
                    <div id="ruleFinance" class="ownership-rule">
                        <strong>Finance Company (Non-Bank):</strong> Legal owner = Finance Company. End customer mapping must be done at first service.
                    </div>
                    <div id="ruleBank" class="ownership-rule">
                        <strong>Bank Finance:</strong> Search existing customer code. Service history maintained against end customer.
                    </div>
                    <div id="ruleRetail" class="ownership-rule" style="background:#f0fff4;border-left-color:#22c55e;">
                        <strong>Retail / Showroom Cash:</strong> Service history maintained from Day 1 under this customer code.
                    </div>
                    <div id="ruleInstitution" class="ownership-rule" style="background:#fff8f0;border-left-color:#f59e0b;">
                        <strong>Institution / Tender:</strong> Bill To = Institution (mandatory). Optional driver/representative code per job card. LPO required for service.
                    </div>

                    <div class="form-section-title">Basic Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.8rem;">Full Name / Company Name <span class="text-danger">*</span></label>
                            <input type="text" id="partyName" class="form-control form-control-sm" placeholder="Enter full name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.8rem;">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" id="partyPhone" class="form-control form-control-sm" placeholder="+255...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.8rem;">Email</label>
                            <input type="email" id="partyEmail" class="form-control form-control-sm" placeholder="email@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.8rem;">TIN No</label>
                            <input type="text" id="partyTin" class="form-control form-control-sm" placeholder="Tax Identification Number">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600" style="font-size:.8rem;">Address</label>
                            <textarea id="partyAddress" class="form-control form-control-sm" rows="2" placeholder="Physical address"></textarea>
                        </div>
                    </div>

                    <div id="financeSection" style="display:none;">
                        <div class="form-section-title">Finance / Contract Details</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:.8rem;">Finance Company Name</label>
                                <input type="text" id="financeCompany" class="form-control form-control-sm" placeholder="e.g. Watu, Bluerock/Mogo">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:.8rem;">Contract No</label>
                                <input type="text" id="contractNo" class="form-control form-control-sm" placeholder="Finance contract number">
                            </div>
                        </div>
                    </div>

                    <div id="institutionSection" style="display:none;">
                        <div class="form-section-title">Institution Details</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:.8rem;">Institution Type</label>
                                <select id="institutionType" class="form-select form-select-sm">
                                    <option value="">Select...</option>
                                    <option>Government Ministry</option>
                                    <option>NGO</option>
                                    <option>Fleet Operator</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:.8rem;">LPO Required</label>
                                <select class="form-select form-select-sm">
                                    <option value="1">Yes (Mandatory)</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button id="btnSaveParty" class="btn" style="background:#273d80;color:#fff;border-radius:8px;font-size:.85rem;padding:8px 24px;font-weight:600;">
                            <i class="bx bx-save me-2"></i>Register Party
                        </button>
                        <a href="{{ route('tvs.parties') }}" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.85rem;padding:8px 20px;">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(0,0,0,.07);">
                <div class="card-header" style="background:#fff;border-bottom:1px solid #f0f0f0;padding:.7rem 1rem;">
                    <h6 class="mb-0 fw-700" style="font-size:.85rem;color:#1e2a4a;"><i class="bx bx-info-circle me-2" style="color:#273d80;"></i>Ownership Rules</h6>
                </div>
                <div class="card-body p-3" style="font-size:.82rem;">
                    <div class="mb-3">
                        <div class="fw-700 mb-1" style="color:#273d80;">Dealer Sales</div>
                        <div class="text-muted">No customer code at sale. Create code at first service. Service history starts from first visit.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-700 mb-1" style="color:#f59e0b;">Finance (Non-Bank)</div>
                        <div class="text-muted">Legal owner = Finance Company. Map end customer at first service.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-700 mb-1" style="color:#6366f1;">Finance (Bank)</div>
                        <div class="text-muted">Customer code exists. Search and link at first service.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-700 mb-1" style="color:#22c55e;">Showroom Cash</div>
                        <div class="text-muted">Service history from Day 1 under retail customer code.</div>
                    </div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#c0172b;">Institutional</div>
                        <div class="text-muted">BillTo = Institution. LPO mandatory. Optional driver code per job card.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    var base = "{{ url('/api/tvs') }}";

    $.get(base + '/parties/types', function(res) {
        if (res.success) {
            res.data.forEach(function(pt) {
                $('#partyType').append('<option value="' + pt.id + '" data-name="' + pt.name + '">' + pt.name + '</option>');
            });
        }
    });

    $('#partyType').on('change', function() {
        var name = $(this).find(':selected').data('name') || '';
        $('.ownership-rule').removeClass('show');
        $('#financeSection, #institutionSection').hide();
        if (name.includes('Dealer')) { $('#ruleDealer').addClass('show'); }
        else if (name.includes('Finance')) { $('#ruleFinance').addClass('show'); $('#financeSection').show(); }
        else if (name.includes('Bank')) { $('#ruleBank').addClass('show'); $('#financeSection').show(); }
        else if (name.includes('Retail')) { $('#ruleRetail').addClass('show'); }
        else if (name.includes('Institution')) { $('#ruleInstitution').addClass('show'); $('#institutionSection').show(); }
    });

    $('#btnSaveParty').on('click', function() {
        var data = {
            party_type_id: $('#partyType').val(),
            party_code: $('#partyCode').val().trim() || null,
            name: $('#partyName').val().trim(),
            phone: $('#partyPhone').val().trim(),
            email: $('#partyEmail').val().trim() || null,
            tin_no: $('#partyTin').val().trim() || null,
            address: $('#partyAddress').val().trim() || null,
            _token: '{{ csrf_token() }}'
        };

        if (!data.party_type_id || !data.name || !data.phone) {
            alert('Party Type, Name, and Phone are required.'); return;
        }

        $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-2"></i>Registering...');

        $.post(base + '/parties', data)
            .done(function(res) {
                if (res.success) {
                    window.location.href = "{{ url('/tvs/parties') }}/" + res.data.id;
                } else {
                    alert(res.message || 'Error creating party.');
                    $('#btnSaveParty').prop('disabled', false).html('<i class="bx bx-save me-2"></i>Register Party');
                }
            })
            .fail(function(xhr) {
                var msg = xhr.responseJSON?.message || 'Error creating party.';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                alert(msg);
                $('#btnSaveParty').prop('disabled', false).html('<i class="bx bx-save me-2"></i>Register Party');
            });
    });
});
</script>
@endpush

</x-app-layout>
