(function () {
    const TOTAL = 10;
    const $form = $("#kycForm");
    const $steps = $(".wizard-step");

    let current = 1;
    let isRequestInFlight = false;
    let signaturePad = null;
    function getStepEl(step)
    {
        return $(`.wizard-step[data-step="${step}"]`);
    }
    function isCreateMode()
    {
        return !($('#is_edit').length && $('#is_edit').val() === '1');
    }
    function clearStepErrors(step) {
        const $step = getStepEl(step);
        $step.find('.is-invalid').removeClass('is-invalid');
        $step.find('.step-error').remove();

        $step.find('#nature_of_business_error').empty();
        $step.find('#source_error').empty();
        $step.find('#declarer_signature_error').empty();
    }
    function markInvalid($el, msg) {
        if (!$el || !$el.length) return;

        if ($el.hasClass('select2-hidden-accessible')) {
            $el.next('.select2').nextAll('.step-error:first').remove();
        } else {
            $el.nextAll('.step-error:first').remove();
        }

        $el.addClass('is-invalid');

        if ($el.hasClass('select2-hidden-accessible')) {
            $el.next('.select2').after(`<div class="invalid-feedback d-block step-error">${msg}</div>`);
        } else {
            $el.after(`<div class="invalid-feedback d-block step-error">${msg}</div>`);
        }
    }
    function scrollToFirstError($step) {
        const $first = $step.find('.is-invalid:first');
        if ($first.length) {
            $('html, body').animate({ scrollTop: $first.offset().top - 120 }, 200);
            return;
        }
        const $custom = $step.find('.step-error:first');
        if ($custom.length) {
            $('html, body').animate({ scrollTop: $custom.offset().top - 120 }, 200);
        }
    }
    function showStep(step, opts = { validate: false })
    {
        if (step < 1) step = 1;
        if (step > TOTAL) step = TOTAL;

        if (opts.validate && step > current) {
            if (!validateStep(current)) return;
        }

        // Save signature when leaving step 9 (before hiding the canvas)
        if (current === 9 && step !== 9 && signaturePad && !signaturePad.isEmpty()) {
            $('#declarer_signature').val(signaturePad.toDataURL('image/png'));
        }

        current = step;

        $steps.addClass("d-none");
        const $active = $steps.filter(`[data-step="${step}"]`).removeClass("d-none");

        const percent = Math.round((step / TOTAL) * 100);
        $("#wizardProgress").css("width", percent + "%");
        $("#wizardSubTitle").text(`Step ${step} of ${TOTAL}`);

        $("#prevStep").prop("disabled", step === 1);
        $("#nextStep").toggleClass("d-none", step === TOTAL);
        $("#finalSubmit").toggleClass("d-none", step !== TOTAL);

        if(step === 1)
        {
            $('#fetchLocationBtn').show('slow');
        }
        if (step === 9)
        {
            setTimeout(() => {
                initSignaturePadWhenVisible();
                saveSignature();
            }, 50);
        }

        const $firstInput = $active.find('input, select, textarea').filter(':visible:first');
        if ($firstInput.length) $firstInput.trigger('focus');

        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    function freezeWizardButtons(freeze = true)
    {
        $('#prevStep, #nextStep, #finalSubmit').prop('disabled', freeze);
        if (freeze)
        {
            $('#finalSubmit').data('old-html', $('#finalSubmit').html());
            $('#finalSubmit').html('<span class="spinner-border spinner-border-sm me-2"></span>' + window.Lang.submitting);
        }
        else
        {
            const old = $('#finalSubmit').data('old-html');
            if (old) $('#finalSubmit').html(old);
        }
    }

    function validateStep(step)
    {
        const $step = getStepEl(step);
        clearStepErrors(step);


        let ok = true;
        if (step !== 8)
        {
            stopCamera();
        }

        $step.find('[required]').each(function () {
            const $el = $(this);

            if (step === 5 && $el.attr('name') === 'setup[]') return;

            if (step === 4) {
                const n = $el.attr('name');
                if (n === 'brand_name[]' || n === 'supplier_name[]' || n === 'since_when_year[]') return;
            }

            if ($el.closest('#nature-other-wrapper').length && $('#nature-other-wrapper').is(':hidden')) return;
            if ($el.closest('.section-pep-details').length && $('.section-pep-details').is(':hidden')) return;
            if ($el.closest('#country_origin_wrapper').length && $('#country_origin_wrapper').is(':hidden')) return;
            if ($el.attr('type') === 'file' && !isCreateMode()) return;
            if ($el.is(':disabled')) return;
            if ($el.is(':checkbox')) return;

            const val = $el.val();
            if (!val || (Array.isArray(val) && val.length === 0)) {
                markInvalid($el, window.Lang.field_required);
            }
        });

        if (step === 1) ok = validateStep1($step) && ok;
        if (step === 2) ok = validateStep2($step) && ok;
        if (step === 3) ok = validateStep3($step) && ok;
        if (step === 4) ok = validateStep4($step) && ok;
        if (step === 5) ok = validateStep5($step) && ok;
        if (step === 6) ok = validateStep6($step) && ok;
        if (step === 7) ok = validateStep7($step) && ok;
        if (step === 8) ok = validateStep8($step) && ok;
        if (step === 9) ok = validateStep9($step) && ok;
        if (step === 10) ok = validateStep10($step) && ok;

        if (!ok) scrollToFirstError($step);
        return ok;
    }
    function validateStep1($step)
    {
        let ok = true;
        const tin = $step.find('#tin').val();
        if (tin && !/^\d{3}-\d{3}-\d{3}$/.test(tin)) {
            ok = false;
            markInvalid($step.find('#tin'), window.Lang.tin_format_error);
        }
        const vat = $step.find('#vat_registration_number').val();
        if (vat && !/^\d{2}-\d{6}-[A-Za-z]$/.test(vat)) {
            ok = false;
            markInvalid($step.find('#vat_registration_number'), window.Lang.vat_format_error);
        }
        if(ok)
        {
            $('#fetchLocationBtn').hide('slow');
        }
        return ok;
    }
    function validateStep2($step) {
        let ok = true;

        const $primary = $step.find('#business_contact_number');
        const primaryVal = ($primary.val() || '').trim();
        if (primaryVal && !/^\+255\d{9}$/.test(primaryVal)) {
            ok = false;
            markInvalid($primary, window.Lang.primary_phone_format);
        }

        const $alt = $step.find('#business_alternate_number');
        const altVal = ($alt.val() || '').trim();
        if (altVal !== '' && altVal !== '+255') {
            if (!/^\+255\d{9}$/.test(altVal)) {
                ok = false;
                markInvalid($alt, window.Lang.alternate_phone_format);
            }
        }

        const $email = $step.find('#business_email');
        const emailVal = ($email.val() || '').trim();
        if (emailVal && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
            ok = false;
            markInvalid($email, window.Lang.email_invalid);
        }

        return ok;
    }
    function validateStep3($step) {
        let ok = true;

        if ($step.find('input[name="nature_of_business[]"]:checked').length === 0) {
            ok = false;
            $('#nature_of_business_error').html(
                `<div class="invalid-feedback d-block step-error">${window.Lang.select_nature_of_business}</div>`
            );
        }

        const isOtherChecked = $step.find('.nature-business-checkbox[value="Other"]').is(':checked');
        if (isOtherChecked) {
            const $other = $step.find('#nature_of_business_other');
            if (!$other.val()) {
                ok = false;
                markInvalid($other, window.Lang.specify_nature_of_business);
            }
        }

        const isPep = ($step.find('#is_pep').val() || $step.find('[name="is_pep"]').val());
        if (isPep === 'yes') {
            const $pepSection = $step.find('.section-pep-details');
            if ($pepSection.is(':visible')) {
                const $name = $step.find('#emergency_fullname');
                const $phone = $step.find('#emergency_contact_number');
                if (!$name.val()) { ok = false; markInvalid($name, window.Lang.pep_fullname_required); }
                if (!$phone.val()) { ok = false; markInvalid($phone, window.Lang.pep_contact_required); }
            }
        }

        return ok;
    }
    function validateStep4($step) {
        let ok = true;

        const $sourceSelect = $step.find('select[name="source"]');
        const selectedSource = $sourceSelect.val();
        if (!selectedSource) {
            ok = false;
            $('#source_error').html(
                `<div class="invalid-feedback d-block step-error">${window.Lang.select_source}</div>`
            );
        }

        if (selectedSource === 'Import') {
            const $country = $step.find('select[name="country_of_origin"]');
            if (!$country.val()) {
                ok = false;
                markInvalid($country, window.Lang.country_origin_required);
            }
        }

        $step.find('table.table-bordered tbody tr').each(function () {
            const $tr = $(this);
            const $brand = $tr.find('input[name="brand_name[]"]');
            const $supplier = $tr.find('input[name="supplier_name[]"]');
            const $year = $tr.find('select[name="since_when_year[]"]');

            if ($brand.length && !$brand.val()) { ok = false; markInvalid($brand, window.Lang.brand_name_required); }
            if ($supplier.length && !$supplier.val()) { ok = false; markInvalid($supplier, window.Lang.supplier_name_required); }
            if ($year.length && !$year.val()) { ok = false; markInvalid($year, window.Lang.year_required); }
        });

        return ok;
    }
    function validateStep5($step) {
        let ok = true;

        $step.find('table.table-bordered').eq(0).find('tbody tr').each(function () {
            const $tr = $(this);

            const $branch = $tr.find('input[name="branch_name[]"]');
            const $brand  = $tr.find('input[name="branch_brand_name[]"]');
            const $key    = $tr.find('input[name="key_personnel[]"]');
            const $setup  = $tr.find('select[name="setup[]"]');

            if ($branch.length && !$branch.val()?.trim()) {
                ok = false;
                markInvalid($branch, window.Lang.branch_name_required);
            }

            if ($brand.length && !$brand.val()?.trim()) {
                ok = false;
                markInvalid($brand, window.Lang.brand_name_required);
            }

            if ($key.length && !$key.val()?.trim()) {
                ok = false;
                markInvalid($key, window.Lang.key_personnel_required);
            }

            if ($setup.length && !$setup.val()) {
                ok = false;
                markInvalid($setup, window.Lang.setup_required);
            }
        });

        if (!ok) scrollToFirstError($step);
        return ok;
    }
    function validateFileTypeAndSize(file) {
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        const ext = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(ext) && !allowedTypes.includes(file.type)) {
            return { valid: false, error: window.Lang.invalid_file_type || 'Invalid file type. Allowed: PDF, JPG, PNG, WEBP' };
        }

        if (file.size > maxSize) {
            return { valid: false, error: window.Lang.file_too_large || 'File too large. Maximum size: 5MB' };
        }

        return { valid: true };
    }

    function validateStep6($step) {
        const isEdit = $('#is_edit').val() === '1';
        if (isEdit) return true;
        applyDocRulesByComposition();

        // Check that required documents have either file or base64 or existing upload
        let ok = true;
        const requiredDocs = ['business_license', 'vat_certificate', 'nida_card', 'tin_certificate', 'tax_clearance_certificate', 'certificate_of_incorporation'];

        const comp = ($('select[name="business_composition_id"]').val() || '').toString();
        if (comp === '2') {
            requiredDocs.push('partnership_deed');
        } else if (comp === '3' || comp === '4') {
            requiredDocs.push('audited_financials');
            requiredDocs.push('copy_of_memarts');
        }

        requiredDocs.forEach(function(doc) {
            const $fileInput = $step.find(`input[type="file"][name="${doc}"]`);
            const $base64Input = $step.find(`input[name="${doc}_base64"]`);
            const $preview = $step.find(`#preview-${doc}`);

            const hasFile = $fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0;
            const hasBase64 = $base64Input.length && $base64Input.val();
            const hasExisting = $preview.find('a').length > 0;

            const $container = $fileInput.closest('.col-lg-4, .col-sm-4, .col-md-6, .mb-3');
            $container.find('.step-error').remove();

            if (!hasFile && !hasBase64 && !hasExisting) {
                ok = false;
                $container.find('.doc-preview').after(`<div class="invalid-feedback d-block step-error">${window.Lang.upload_file_or_capture}</div>`);
            } else if (hasFile) {
                // Validate file type and size
                const file = $fileInput[0].files[0];
                const validation = validateFileTypeAndSize(file);
                if (!validation.valid) {
                    ok = false;
                    $container.find('.doc-preview').after(`<div class="invalid-feedback d-block step-error">${validation.error}</div>`);
                }
            }
        });

        return ok;
    }
    function validateStep7($step) {
        let ok = true;

        const email = $step.find('#residential_email').val();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            ok = false;
            markInvalid($step.find('#residential_email'), window.Lang.email_invalid);
        }

        const pct = ($step.find('#owner_percentage').val() || '').trim();
        if (pct)
        {
            const n = parseFloat(pct);

            if (
                isNaN(n) ||
                n < 0 ||
                n > 100 ||
                !/^\d{1,3}(\.\d{1,2})?$/.test(pct)
            ) {
                ok = false;
                markInvalid(
                    $step.find('#owner_percentage'),
                    window.Lang.percentage_invalid
                );
            }

            // extra safety: block decimals with 100
            if (n === 100 && pct.includes('.')) {
                ok = false;
                markInvalid(
                    $step.find('#owner_percentage'),
                    window.Lang.percentage_100_no_decimal
                );
            }
        }

        // Check required owner documents have either file or base64 or existing upload
        if (isCreateMode()) {
            const requiredOwnerDocs = ['next_of_kin_nida_card', 'next_of_kin_tin_certificate'];

            requiredOwnerDocs.forEach(function(doc) {
                const $fileInput = $step.find(`input[type="file"][name="${doc}"]`);
                const $base64Input = $step.find(`input[name="${doc}_base64"]`);
                const $preview = $step.find(`#preview-${doc}`);

                const hasFile = $fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0;
                const hasBase64 = $base64Input.length && $base64Input.val();
                const hasExisting = $preview.find('a').length > 0;

                const $container = $fileInput.closest('.col-lg-4, .col-sm-4, .col-md-6, .mb-3');
                $container.find('.step-error').remove();

                if (!hasFile && !hasBase64 && !hasExisting) {
                    ok = false;
                    $container.find('.doc-preview').after(`<div class="invalid-feedback d-block step-error">${window.Lang.upload_file_or_capture}</div>`);
                } else if (hasFile) {
                    // Validate file type and size
                    const file = $fileInput[0].files[0];
                    const validation = validateFileTypeAndSize(file);
                    if (!validation.valid) {
                        ok = false;
                        $container.find('.doc-preview').after(`<div class="invalid-feedback d-block step-error">${validation.error}</div>`);
                    }
                }
            });
        }

        return ok;
    }
    function validateStep9($step)
    {
        let ok = true;
        if (isCreateMode())
        {
            const signatureVal = $('input[name="declarer_signature"]').val();
            // if (!signatureVal || signatureVal === '')
            // {
            //     ok = false;
            //     if (!$step.find('#declarer_signature_error').length) {
            //         $step.find('input[name="declarer_signature"]').after('<div id="declarer_signature_error"></div>');
            //     }
            //     $('#declarer_signature_error').html(
            //         `<div class="invalid-feedback d-block step-error">Signature is required.</div>`
            //     );
            // }
        }
        return ok;
    }
    function validateStep8() {

        let newPhotos = [];

        try {
            newPhotos = JSON.parse($('#photo_paths').val() || '[]');
        } catch {
            newPhotos = [];
        }

        const total = existingCount + newPhotos.length;

        if (total < 3) {
            Swal.fire(
                window.Lang.camera_error,
                window.Lang.min_photos_required,
                'error'
            );
            return false;
        }

        return true;
    }
    function validateStep10($step) {
        let ok = true;

        const $consent = $step.find('#consent_declaration');

        if (!$consent.is(':checked')) {
            ok = false;

            // If unchecked, add error message and mark it as invalid
            $consent.closest('p').after(
                `<div class="invalid-feedback d-block step-error">${window.Lang.consent_required}</div>`
            );
            $consent.addClass('is-invalid');
        } else {
            $consent.removeClass('is-invalid');
            $consent.closest('p').find('.step-error').remove();
        }
        // Form submission is now handled by the click handler (saveStep for create, form submit for edit)
        return ok;
    }
    function setFileRequired(name, required)
    {
        const $input = $(`input[type="file"][name="${name}"]`);
        if (!$input.length) return;

        $input.prop('required', !!required);

        const $label = $input.closest('.col-lg-4, .col-md-4, .col-sm-4, .mb-3').find('label').first();
        if ($label.length) {
            $label.find('.req-star').remove();
            if (required) $label.append(' <span class="text-danger req-star">*</span>');
        }

        if (!required) {
            $input.removeClass('is-invalid');
            $input.next('.invalid-feedback.step-error').remove();
        }
    }
    function applyDocRulesByComposition()
    {
        const comp = ($('select[name="business_composition_id"]').val() || '').toString();
        const isPartnership = (comp === '2');
        const isLtd = (comp === '3' || comp === '4');
        const isEdit = $('#is_edit').val() === '1';

        let reqPartnershipDeed = false;
        let reqAudited = false;
        let reqMemarts = false;

        if (isLtd) { reqAudited = true; reqMemarts = true; }
        else if (isPartnership) { reqPartnershipDeed = true; }

        if (isEdit) { reqPartnershipDeed = false; reqAudited = false; reqMemarts = false; }

        setFileRequired('partnership_deed', reqPartnershipDeed);
        setFileRequired('audited_financials', reqAudited);
        setFileRequired('copy_of_memarts', reqMemarts);
    }
    async function serverValidateStep(step)
    {
        const url = $form.data('validate-url');
        const fd = new FormData($form[0]);
        fd.append('step', step);
        try {
            await $.ajax({
                url,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            return true;
        } catch (xhr) {
            const errors = xhr.responseJSON?.errors || {};
            const $stepEl = getStepEl(step);

            clearStepErrors(step);

            Object.keys(errors).forEach((name) => {
                const msg = errors[name]?.[0] || 'Invalid';

                if (name === 'source') {
                    $('#source_error').html(`<div class="invalid-feedback d-block step-error">${msg}</div>`);
                    return;
                }
                if (name.startsWith('nature_of_business')) {
                    $('#nature_of_business_error').html(`<div class="invalid-feedback d-block step-error">${msg}</div>`);
                    return;
                }

                let $el = $stepEl.find(`[name="${name}"]`).first();

                if (!$el.length && name.includes('.')) {
                    const base = name.split('.')[0] + '[]';
                    $el = $stepEl.find(`[name="${base}"]`).first();
                }

                if (!$el.length) {
                    $el = $stepEl.find(`[name="${name}[]"]`).first();
                }

                if ($el.length) markInvalid($el, msg);
            });

            scrollToFirstError($stepEl);
            return false;
        }
    }
    async function saveStep(step)
    {
        const url = $form.data('save-url');
        if (!url) return true; // If no save URL, skip (edit mode uses regular form submit)

        const fd = new FormData($form[0]);
        fd.append('step', step);

        const kycId = $('#kyc_id').val();
        if (kycId) {
            fd.append('kyc_id', kycId);
        }

        try {
            const response = await $.ajax({
                url,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            if (response.kyc_id) {
                $('#kyc_id').val(response.kyc_id);
            }

            if (response.redirect) {
                // Final step - redirect to index
                window.location.href = response.redirect;
                return 'redirect';
            }

            return true;
        } catch (xhr) {
            console.error('Save step failed:', xhr);
            const errorMsg = xhr.responseJSON?.error || window.Lang.save_step_failed;
            Swal.fire(window.Lang.camera_error, errorMsg, 'error');
            return false;
        }
    }
    function setWizardLoading(isLoading)
    {
        isRequestInFlight = !!isLoading;

        $("#nextStep, #prevStep, #finalSubmit").prop("disabled", isLoading);

        if (isLoading) {
            document.documentElement.setAttribute('loader', 'enable');
        } else {
            document.documentElement.setAttribute('loader', 'disable');
        }

        const $next = $("#nextStep");
        if (!$next.data("original-html")) $next.data("original-html", $next.html());

        if (isLoading) {
            $next.html(`<span class="spinner-border spinner-border-sm me-2"></span>${window.Lang.validating}`);
        } else {
            $next.html($next.data("original-html"));
        }
    }
    $(document).on("click", "#prevStep", function (e) {
        e.preventDefault();
        if (isRequestInFlight) return;
        showStep(current - 1, { validate: false });
    });

    async function runWithLock(fn, showLoader = true) {
        if (isRequestInFlight) return;
        isRequestInFlight = true;

        if (showLoader) setWizardLoading(true);

        try {
            await fn();
        } finally {
            if (showLoader) setWizardLoading(false);
            isRequestInFlight = false;
        }
    }

    function saveSignature()
    {
        if (!signaturePad)
        {
            // No signature pad initialized, keep existing value
            return;
        }

        if (signaturePad.isEmpty())
        {
            // Don't clear if already has a value (edit mode with existing signature)
            // Only clear if we're explicitly clearing (handled by clear button)
            const currentVal = $('#declarer_signature').val();
            if (!currentVal || !currentVal.startsWith('data:image')) {
                $('#declarer_signature').val('');
            }
            return;
        }

        $('#declarer_signature').val(
            signaturePad.toDataURL('image/png')
        );
    }

    const canvas = document.getElementById('signature-pad');
    if (!canvas) return;
    if (signaturePad) return;
    if (canvas)
    {
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 1)', // White background
            penColor: 'black',
            onEnd: function () {
                saveSignature();
            }
        });

        setTimeout(resizeCanvas, 100);
        window.addEventListener('resize', resizeCanvas);
    }
    else
    {
        console.error("Canvas element not found!");
    }

    function resizeCanvas()
    {
        if (!signaturePad || !canvas) return;

        if (canvas.offsetWidth === 0) return;

        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const data = signaturePad.isEmpty() ? null : signaturePad.toData();

        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = 150 * ratio;

        const ctx = canvas.getContext('2d');
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

        signaturePad.clear();
        if (data) signaturePad.fromData(data);

        saveSignature();
    }

    function initSignaturePadWhenVisible()
    {

        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255,255,255,1)',
            penColor: 'black',
            onEnd: function () {
                saveSignature();
            }
        });

        const existingSignature = $('input[name="declarer_signature"]').val();
        if (existingSignature)
        {
            signaturePad.fromDataURL(existingSignature);
        }

        setTimeout(resizeCanvas, 100);
        window.addEventListener('resize', resizeCanvas);
    }

    $(document).on('change', 'select[name="business_composition_id"]', applyDocRulesByComposition);
    $(function () { applyDocRulesByComposition(); });

    $(document).on("click","#nextStep, #finalSubmit", function () {
        const isFinal = this.id === 'finalSubmit';
        const isEdit = $('#is_edit').val() === '1';

        runWithLock(async () => {
            if (!validateStep(current)) return;
            resizeCanvas();
            const validOk = await serverValidateStep(current);
            if (!validOk) return;

            // For create mode, save step data incrementally
            if (!isEdit) {
                // Show loader on finish button for final step
                if (isFinal) {
                    freezeWizardButtons(true);
                }
                const saveResult = await saveStep(current);
                if (saveResult === 'redirect') return; // Final step redirected
                if (!saveResult) {
                    // Reset button if save failed
                    if (isFinal) freezeWizardButtons(false);
                    return;
                }
            }

            if (isFinal)
            {
                if (isEdit) {
                    // Edit mode - use regular form submit
                    saveSignature();
                    freezeWizardButtons(true);
                    $('#kycForm').trigger('submit');
                }
                // Create mode - saveStep already handled the redirect
                return;
            }
            showStep(current + 1, { validate: false });
        }, true);
    });

    $(document).on('input', '#kycForm .force-255, #kycForm .email-format', function () {
        $(this).removeClass('is-invalid');
        $(this).nextAll('.step-error:first').remove();
    });

    $('#clear-signature').off('click').on('click', function () {
        if (signaturePad)
        {
            signaturePad.clear();
            $('#declarer_signature').val('');
            $('#declarer_signature_error').remove();

            $('.signature-image-show').hide('slow');
        }
    });


    showStep(1);
})();
