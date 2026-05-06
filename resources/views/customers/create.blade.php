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
        margin-bottom: 1.25rem;
    }

    .section-label {
        font-size: .7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        margin-bottom: .75rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e5e7eb;
    }

    .form-label {
        font-size: .78rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 5px;
    }

    .form-control,
    .form-select {
        font-size: .83rem;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 8px 12px;
        color: #1e2a4a;
        transition: border-color .2s, box-shadow .2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #273d80;
        box-shadow: 0 0 0 3px rgba(39,61,128,.1);
        outline: none;
    }

    .form-control::placeholder {
        color: #9ca3af;
        font-size: .78rem;
    }

    .form-hint {
        font-size: .7rem;
        color: #9ca3af;
        margin-top: 4px;
    }

    /* Customer ID readonly */
    .customer-id-field {
        background: #f3f4f6;
        border: 1px dashed #d1d5db;
        color: #6b7280;
        font-family: monospace;
        font-size: .82rem;
        border-radius: 8px;
        padding: 8px 12px;
    }

    /* Toggle button groups */
    .btn-toggle-group {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .btn-toggle {
        font-size: .78rem;
        font-weight: 600;
        border-radius: 8px;
        padding: 7px 18px;
        border: 1.5px solid #d1d5db;
        background: #fff;
        color: #6b7280;
        cursor: pointer;
        transition: all .18s;
    }

    .btn-toggle:hover {
        border-color: #273d80;
        color: #273d80;
    }

    .btn-toggle.active-type {
        background: #273d80;
        border-color: #273d80;
        color: #fff;
    }

    .btn-toggle.active-status-active {
        background: #16a34a;
        border-color: #16a34a;
        color: #fff;
    }

    .btn-toggle.active-status-inactive {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }

    .btn-toggle.active-status-premium {
        background: #d97706;
        border-color: #d97706;
        color: #fff;
    }

    /* Submit / Cancel */
    .btn-submit {
        background: #c0172b;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .83rem;
        font-weight: 700;
        padding: 10px 28px;
        transition: background .18s;
    }

    .btn-submit:hover {
        background: #a3121f;
        color: #fff;
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: .83rem;
        font-weight: 600;
        padding: 10px 22px;
        transition: background .18s;
        text-decoration: none;
    }

    .btn-cancel:hover {
        background: #e5e7eb;
        color: #111827;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }
</style>
@endpush

<div class="container-fluid">

    {{-- Hero --}}
    <div class="page-hero">
        <a href="{{ route('customers.index') }}"
           class="btn btn-sm mb-2"
           style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;font-size:.78rem;">
            <i class="bx bx-arrow-back me-1"></i>Back
        </a>

        <h2 style="font-size:1.45rem;font-weight:800;color:#fff;margin:0 0 4px;">
            Add New Customer
        </h2>
        <p style="font-size:.83rem;color:rgba(255,255,255,.72);margin:0;">
            Head Office &mdash; All Branches
        </p>
    </div>

    <form action="{{ route('customers.store') }}" method="POST" id="customerForm">
        @csrf

        {{-- Hidden toggle inputs --}}
        <input type="hidden" name="status" id="status_field" value="Active">

        {{-- ── Customer Information ── --}}
        <div class="card info-card">
            <div class="card-body p-4">

                <div class="section-label">
                    <i class="bx bx-id-card" style="color:#c0172b;font-size:1rem;"></i>
                    Customer Information
                </div>

                <div class="row g-3">
                    {{-- Customer ID --}}
                   <div class="col-12 col-md-6">
    <label class="form-label">Customer ID</label>

    <input type="text" 
           id="customerCode" 
           name="customer_code"
           class="form-control form-control-sm"
           placeholder="Auto-generated based on location and branch"
           readonly>

    <div class="form-hint">
        <i class="bx bx-info-circle me-1"></i>
        Will be assigned automatically upon saving
    </div>
</div>

                    {{-- Customer Type --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label">Customer Type</label>
                        <select name="customer_type" class="form-select @error('customer_type') is-invalid @enderror">
                            <option value="">Select Type</option>
                            <option value="Individual" {{ old('customer_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                            <option value="Dealer"     {{ old('customer_type') == 'Dealer'     ? 'selected' : '' }}>Dealer</option>
                            <option value="Institute"  {{ old('customer_type') == 'Institute'  ? 'selected' : '' }}>Institute</option>
                        </select>
                        @error('customer_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Personal Information ── --}}
        <div class="card info-card">
            <div class="card-body p-4">

                <div class="section-label">
                    <i class="bx bx-user-circle" style="color:#c0172b;font-size:1rem;"></i>
                    Personal Information
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name') }}" placeholder="Enter first name" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name') }}" placeholder="Enter last name" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                  <div class="col-12 col-md-6">
    <label class="form-label">Phone Number <span class="text-danger">*</span></label>

    <div class="input-group">
        <span class="input-group-text">+255</span>

        <input type="text"
               id="phone"
               name="phone_number"
               class="form-control"
               placeholder="XXXXXXXXX"
               maxlength="9"
               inputmode="numeric"
               required>
    </div>

    <small id="phoneHelp" class="form-text"></small>
</div>

                  <div class="col-12 col-md-6">
    <label class="form-label">Alternate Phone</label>

    <div class="input-group">
        <span class="input-group-text">+255</span>

        <input type="text"
               id="alternate_phone"
               name="alternate_phone"
               class="form-control @error('alternate_phone') is-invalid @enderror"
               placeholder="XXXXXXXXX"
               maxlength="9"
               inputmode="numeric">

    </div>

    <small id="altPhoneHelp" class="form-text"></small>

    @error('alternate_phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="email@example.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Status</label>
                         <select id="statusGroup" class="form-select form-select-sm" onchange="setStatus(this.value)">
        <option value="Active" selected>Active</option>
        <option value="Inactive">Inactive</option>
        <option value="Premium">Premium</option>
    </select>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Address Information ── --}}
        <div class="card info-card">
            <div class="card-body p-4">

                <div class="section-label">
                    <i class="bx bx-map" style="color:#c0172b;font-size:1rem;"></i>
                    Address Information
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" name="address_line1" class="form-control @error('address_line1') is-invalid @enderror"
                               value="{{ old('address_line1') }}" placeholder="Street, building or plot number" required>
                        @error('address_line1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" name="address_line2" class="form-control @error('address_line2') is-invalid @enderror"
                               value="{{ old('address_line2') }}" placeholder="Apartment, suite, ward, etc.">
                        @error('address_line2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Region <span class="text-danger">*</span></label>
                        <select name="region" id="regionSelect" class="form-select @error('region') is-invalid @enderror" required>
                            <option value="">Select Region</option>
                            @foreach([
                                'Arusha','Dar es Salaam','Dodoma','Geita','Iringa','Kagera',
                                'Katavi','Kigoma','Kilimanjaro','Lindi','Manyara','Mara',
                                'Mbeya','Morogoro','Mtwara','Mwanza','Njombe',
                                'Pemba North','Pemba South','Pwani','Rukwa','Ruvuma',
                                'Shinyanga','Simiyu','Singida','Songwe','Tabora','Tanga',
                                'Unguja North','Unguja South'
                            ] as $region)
                                <option value="{{ $region }}" {{ old('region') == $region ? 'selected' : '' }}>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>
                        @error('region')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select name="district" id="districtSelect" class="form-select @error('district') is-invalid @enderror" required>
                            <option value="">Select District</option>
                        </select>
                        @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Town <span class="text-danger">*</span></label>
                        <select name="town" id="townSelect" class="form-select @error('town') is-invalid @enderror" required>
                            <option value="">Select Town</option>
                        </select>
                        @error('town')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Additional Information ── --}}
        <div class="card info-card">
            <div class="card-body p-4">

                <div class="section-label">
                    <i class="bx bx-file" style="color:#c0172b;font-size:1rem;"></i>
                    Additional Information
                </div>

                <div class="row g-3">
                   <div class="col-12 col-md-6">
    <label class="form-label">Registration Date</label>
    <input type="text" name="registration_date" id="registration_date"
           class="form-control @error('registration_date') is-invalid @enderror"
           value="{{ old('registration_date', date('d-M-Y')) }}"
           placeholder="DD-Mon-YYYY" readonly>
    @error('registration_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                  placeholder="Any additional notes about this customer...">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <button type="submit" class="btn-submit">
                <i class="bx bx-save me-1"></i>Save Customer
            </button>
            <a href="{{ route('customers.index') }}" class="btn-cancel">
                <i class="bx bx-x me-1"></i>Cancel
            </a>
        </div>

    </form>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   
$(document).ready(function () {
   
    console.log("SCRIPT LOADED");
    generateCustomerIDFromServer();
    setupPhoneValidation('#phone', '#phoneHelp');
    setupPhoneValidation('#alternate_phone', '#altPhoneHelp');
    populateDistricts('#regionSelect', '#districtSelect', '#townSelect');
    populateTowns('#districtSelect', '#townSelect', '#regionSelect');

    // debug
    $('#regionSelect').on('change', function () {
        console.log('Region changed to:', $(this).val());
    });
    $('#region').trigger('change');
});
 const tanzaniaData = {
            "Arusha": {
                "Arusha": ["Arusha", "Mererani"],
                "Karatu": ["Karatu"],
                "Longido": ["Longido"],
                "Monduli": ["Monduli"],
                "Ngorongoro": ["Ngorongoro"]
            },
            "Dar es Salaam": {
                "Ilala": ["Ilala", "Kariakoo"],
                "Kinondoni": ["Kinondoni", "Mbezi"],
                "Temeke": ["Temeke", "Mbagala"],
                "Ubungo": ["Ubungo"],
                "Kigamboni": ["Kigamboni"]
            },
            "Dodoma": {
                "Dodoma": ["Dodoma"],
                "Bahi": ["Bahi"],
                "Chamwino": ["Chamwino"],
                "Chemba": ["Chemba"],
                "Kondoa": ["Kondoa"],
                "Kongwa": ["Kongwa"],
                "Mpwapwa": ["Mpwapwa"]
            },
            "Geita": {
                "Geita": ["Geita"],
                "Bukombe": ["Bukombe"],
                "Chato": ["Chato"],
                "Mbogwe": ["Mbogwe"],
                "Nyanghwale": ["Nyanghwale"]
            },
            "Iringa": {
                "Iringa": ["Iringa"],
                "Kilolo": ["Kilolo"],
                "Mufindi": ["Mufindi"],
                "Mufindi": ["Makambako"]
            },
            "Kagera": {
                "Bukoba": ["Bukoba"],
                "Biharamulo": ["Biharamulo"],
                "Karagwe": ["Karagwe"],
                "Kyerwa": ["Kyerwa"],
                "Misenyi": ["Misenyi"],
                "Muleba": ["Muleba"],
                "Ngara": ["Ngara"]
            },
            "Katavi": {
                "Mpanda": ["Mpanda"],
                "Mlele": ["Mlele"],
                "Nsimbo": ["Nsimbo"]
            },
            "Kigoma": {
                "Kigoma": ["Kigoma"],
                "Buhigwe": ["Buhigwe"],
                "Kakonko": ["Kakonko"],
                "Kasulu": ["Kasulu"],
                "Kibondo": ["Kibondo"],
                "Uvinza": ["Uvinza"]
            },
            "Kilimanjaro": {
                "Moshi": ["Moshi"],
                "Hai": ["Hai"],
                "Mwanga": ["Mwanga"],
                "Rombo": ["Rombo"],
                "Same": ["Same"],
                "Siha": ["Siha"]
            },
            "Lindi": {
                "Lindi": ["Lindi"],
                "Kilifi": ["Kilifi"],
                "Liwale": ["Liwale"],
                "Nachingwea": ["Nachingwea"],
                "Ruangwa": ["Ruangwa"]
            },
            "Manyara": {
                "Babati": ["Babati"],
                "Hanang": ["Hanang"],
                "Kiteto": ["Kiteto"],
                "Mbulu": ["Mbulu"],
                "Simanjiro": ["Simanjiro"]
            },
            "Mara": {
                "Musoma": ["Musoma"],
                "Bunda": ["Bunda"],
                "Butiama": ["Butiama"],
                "Northmara": ["Northmara"],
                "Rorya": ["Rorya"],
                "Serengeti": ["Serengeti"],
                "Tarime": ["Tarime"]
            },
            "Mbeya": {
                "Mbeya": ["Mbeya", "Tunduma"],
                "Chunya": ["Chunya"],
                "Ileje": ["Ileje"],
                "Kyela": ["Kyela"],
                "Mbarali": ["Mbarali"],
                "Mbozi": ["Mbozi"],
                "Momba": ["Momba"],
                "Rungwe": ["Rungwe"]
            },
            "Morogoro": {
                "Morogoro": ["Morogoro"],
                "Gairo": ["Gairo"],
                "Kilombero": ["Kilombero"],
                "Kilosa": ["Kilosa"],
                "Mvomero": ["Mvomero"],
                "Ulanga": ["Ulanga"]
            },
            "Mtwara": {
                "Mtwara": ["Mtwara"],
                "Masasi": ["Masasi"],
                "Nanyumbu": ["Nanyumbu"],
                "Newala": ["Newala"],
                "Tandahimba": ["Tandahimba"]
            },
            "Mwanza": {
                "Mwanza": ["Mwanza", "Nyamagana"],
                "Ilemela": ["Ilemela"],
                "Kwimba": ["Kwimba"],
                "Magu": ["Magu"],
                "Misungwi": ["Misungwi"],
                "Nyamagana": ["Nyamagana"],
                "Sengerema": ["Sengerema"],
                "Ukerewe": ["Ukerewe"]
            },
            "Njombe": {
                "Njombe": ["Njombe"],
                "Ludewa": ["Ludewa"],
                "Makambako": ["Makambako"],
                "Makete": ["Makete"],
                "Wanging'ombe": ["Wanging'ombe"]
            },
            "Pwani": {
                "Bagamoyo": ["Bagamoyo"],
                "Chalinze": ["Chalinze"],
                "Kibaha": ["Kibaha"],
                "Kisarawe": ["Kisarawe"],
                "Mafia": ["Mafia"],
                "Mkuranga": ["Mkuranga"],
                "Rufiji": ["Rufiji"]
            },
            "Rukwa": {
                "Sumbawanga": ["Sumbawanga"],
                "Kalambo": ["Kalambo"],
                "Nkasi": ["Nkasi"]
            },
            "Ruvuma": {
                "Songea": ["Songea"],
                "Mbinga": ["Mbinga"],
                "Namtumbo": ["Namtumbo"],
                "Nyasa": ["Nyasa"],
                "Tunduru": ["Tunduru"]
            },
            "Shinyanga": {
                "Shinyanga": ["Shinyanga"],
                "Kahama": ["Kahama"],
                "Kishapu": ["Kishapu"],
                "Msalala": ["Msalala"],
                "Shinyanga": ["Shinyanga"]
            },
            "Simiyu": {
                "Bariadi": ["Bariadi"],
                "Busega": ["Busega"],
                "Itilima": ["Itilima"],
                "Maswa": ["Maswa"],
                "Meatu": ["Meatu"]
            },
            "Singida": {
                "Singida": ["Singida"],
                "Ikungi": ["Ikungi"],
                "Iramba": ["Iramba"],
                "Manyoni": ["Manyoni"],
                "Mkalama": ["Mkalama"]
            },
            "Songwe": {
                "Mbozi": ["Mbozi"],
                "Ileje": ["Ileje"],
                "Momba": ["Momba"],
                "Songwe": ["Songwe"]
            },
            "Tabora": {
                "Tabora": ["Tabora"],
                "Igunga": ["Igunga"],
                "Kaliua": ["Kaliua"],
                "Nzega": ["Nzega"],
                "Sikonge": ["Sikonge"],
                "Urambo": ["Urambo"],
                "Uyui": ["Uyui"]
            },
            "Tanga": {
                "Tanga": ["Tanga"],
                "Handeni": ["Handeni"],
                "Kilifi": ["Kilifi"],
                "Korogwe": ["Korogwe"],
                "Lushoto": ["Lushoto"],
                "Mkinga": ["Mkinga"],
                "Muheza": ["Muheza"],
                "Pangani": ["Pangani"]
            },
            "Unguja North": {
                "Unguja North": ["Unguja"]
            },
            "Unguja South": {
                "Unguja South": ["Unguja"]
            },
            "Pemba North": {
                "Pemba North": ["Pemba"]
            },
            "Pemba South": {
                "Pemba South": ["Pemba"]
            }
        };

function populateDistricts(regionSelector, districtSelector, townSelector) {
    $(regionSelector).on('change', function () {

        const region = $(this).val();
        console.log("Selected region:", region);
        const $district = $(districtSelector);
        const $town = $(townSelector);

        $district.html('<option value="">Select District</option>');
        $town.html('<option value="">Select Town</option>');

        if (!region || !tanzaniaData[region]) return;

        Object.keys(tanzaniaData[region]).forEach(district => {
            $district.append(`<option value="${district}">${district}</option>`);
        });
    });
}

function populateTowns(districtSelector, townSelector, regionSelector) {
    $(districtSelector).on('change', function () {

        const region = $(regionSelector).val();
        const district = $(this).val();
        const $town = $(townSelector);

        $town.html('<option value="">Select Town</option>');

        if (!region || !district) return;

        const towns = tanzaniaData?.[region]?.[district] || [];

        towns.forEach(town => {
            $town.append(`<option value="${town}">${town}</option>`);
        });
    });
}

function setupPhoneValidation(selector, helpSelector) {

    $(selector).on('input', function () {
        this.value = this.value.replace(/\D/g, '');

        if (this.value.length > 9) {
            this.value = this.value.substring(0, 9);
        }

        $(helpSelector).text('').removeClass('text-danger text-success');
        $(this).removeClass('is-invalid is-valid');
    });

    $(selector).on('blur', function () {
        let len = this.value.length;

        if (len === 0) {
            $(helpSelector)
                .text('Phone number is optional')
                .addClass('text-muted');
        }
        else if (len !== 9) {
            $(helpSelector)
                .text('Must be exactly 9 digits')
                .addClass('text-danger');

            $(this).addClass('is-invalid');
        }
        else {
            $(helpSelector)
                .text('Valid number')
                .removeClass('text-danger')
                .addClass('text-success');

            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });
}

function toProperCase(str) {
    return str
        .toLowerCase()
        .replace(/\b\w/g, function (char) {
            return char.toUpperCase();
        });
}

// First Name
$('input[name="first_name"]').on('input', function () {
    let cursor = this.selectionStart;
    this.value = toProperCase(this.value);
    this.setSelectionRange(cursor, cursor);
});

// Last Name
$('input[name="last_name"]').on('input', function () {
    let cursor = this.selectionStart;
    this.value = toProperCase(this.value);
    this.setSelectionRange(cursor, cursor);
});


function generateCustomerIDFromServer() {
     console.log("Function called");
    let town = $('#town').val() || 'DAR';

    $.ajax({
        url: '/generate-customer-code',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            city: town
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $('#customerCode').val(response.customer_code);
            } else {
                console.log('Fallback triggered');
            }
        },
        error: function (xhr, status, error) {
    console.log('Status:', xhr.status);
    console.log('Response:', xhr.responseText);
}
    });
}
</script>

<script>
    /* ── Status toggle (three distinct colours) ── */
    function setStatusToggle(btn, value) {
        const statusClasses = ['active-status-active','active-status-inactive','active-status-premium'];
        document.querySelectorAll('#statusGroup .btn-toggle').forEach(b => b.classList.remove(...statusClasses));
        const map = { Active: 'active-status-active', Inactive: 'active-status-inactive', Premium: 'active-status-premium' };
        btn.classList.add(map[value]);
        document.getElementById('status_field').value = value;
    }

    /* ── Region → District → Town cascade ── */
    const geodata = {
        'Dar es Salaam': {
            'Ilala':    ['Buguruni','Gerezani','Ilala','Kariakoo','Kisutu','Kivukoni'],
            'Kinondoni':['Hananasif','Kawe','Kinondoni','Magomeni','Makuburi','Msasani'],
            'Temeke':   ['Chang\'ombe','Keko','Kurasini','Mbagala','Temeke','Vijibweni'],
            'Ubungo':   ['Kibamba','Kimara','Kwembe','Mbezi','Ubungo'],
            'Kigamboni':['Kigamboni','Charambe','Kibada','Mjimwema','Somangila'],
        },
        'Arusha': {
            'Arusha City':['Arusha CBD','Kaloleni','Lemara','Sekei','Themi'],
            'Arumeru':    ['Usa River','Tengeru','Moshono','Nkoaranga'],
            'Meru':       ['Poli','Ngarenanyuki','Olkokola'],
        },
        'Dodoma': {
            'Dodoma Urban':['Dodoma CBD','Chamwino','Njedengwa','Kikuyu'],
            'Chamwino':    ['Buigiri','Idifu','Nala'],
        },
        'Mwanza': {
            'Ilemela':   ['Ilemela','Kirumba','Mkolani','Nyamagana'],
            'Nyamagana': ['Nyamagana','Buzuruga','Igogo','Mahina'],
            'Sengerema': ['Sengerema','Buseresere','Nyampande'],
        },
    };

    const regionSel   = document.getElementById('regionSelect');
    const districtSel = document.getElementById('districtSelect');
    const townSel     = document.getElementById('townSelect');

    regionSel.addEventListener('change', function () {
        const region = this.value;
        districtSel.innerHTML = '<option value="">Select District</option>';
        townSel.innerHTML     = '<option value="">Select Town</option>';

        if (geodata[region]) {
            Object.keys(geodata[region]).forEach(d => {
                districtSel.append(new Option(d, d));
            });
        }
    });

    districtSel.addEventListener('change', function () {
        const region   = regionSel.value;
        const district = this.value;
        townSel.innerHTML = '<option value="">Select Town</option>';

        if (geodata[region] && geodata[region][district]) {
            geodata[region][district].forEach(t => townSel.append(new Option(t, t)));
        }
    });
</script>

</x-app-layout>