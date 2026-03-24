(function () {
    const TOTAL = 9;
    const $form = $("#kycForm");
    const $steps = $(".wizard-step");

    let current = 1;
    let isRequestInFlight = false;
    let signaturePad = null;
    function getStepEl(step) {
        return $(`.wizard-step[data-step="${step}"]`);
    }
    function isCreateMode() {
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

        current = step;

        $steps.addClass("d-none");
        const $active = $steps.filter(`[data-step="${step}"]`).removeClass("d-none");

        const percent = Math.round((step / TOTAL) * 100);
        $("#wizardProgress").css("width", percent + "%");
        $("#wizardSubTitle").text(`Step ${step} of ${TOTAL}`);

        $("#prevStep").prop("disabled", step === 1);
        $("#nextStep").toggleClass("d-none", step === TOTAL);
        $("#finalSubmit").toggleClass("d-none", step !== TOTAL);

        if (step === 8)
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
    function validateStep(step)
    {
        const $step = getStepEl(step);
        clearStepErrors(step);

        let ok = true;

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

            // Important: if element is hidden, don't validate it
            if (!$el.is(':visible')) return;

            if ($el.attr('type') === 'file' && !isCreateMode()) return;
            if ($el.is(':disabled')) return;
            if ($el.is(':checkbox')) return;

            const val = $el.val();

            const isEmpty =
                val === null ||
                val === undefined ||
                (typeof val === 'string' && val.trim() === '') ||
                (Array.isArray(val) && val.length === 0);

            if (isEmpty) {
                ok = false; // ✅ THIS WAS MISSING
                markInvalid($el, 'This field is required.');
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

        if (!ok) scrollToFirstError($step);
        return ok;
    }

    function validateStep1($step) {
        let ok = true;
        const tin = $step.find('#tin').val();
        if (tin && !/^\d{3}-\d{3}-\d{3}$/.test(tin)) {
            ok = false;
            markInvalid($step.find('#tin'), 'TIN must be in format 111-222-333.');
        }
        const vat = $step.find('#vat_registration_number').val();
        if (vat && !/^\d{2}-\d{6}-[A-Za-z]$/.test(vat)) {
            ok = false;
            markInvalid($step.find('#vat_registration_number'), 'VAT must be in format 99-999999-A.');
        }
        return ok;
    }
    function validateStep2($step) {
        let ok = true;

        const $primary = $step.find('#business_contact_number');
        const primaryVal = ($primary.val() || '').trim();
        if (primaryVal && !/^\+255\d{9}$/.test(primaryVal)) {
            ok = false;
            markInvalid($primary, 'Primary phone must be in format +255XXXXXXXXX (9 digits after +255).');
        }

        const $alt = $step.find('#business_alternate_number');
        const altVal = ($alt.val() || '').trim();
        if (altVal !== '' && altVal !== '+255') {
            if (!/^\+255\d{9}$/.test(altVal)) {
                ok = false;
                markInvalid($alt, 'Alternate phone must be in format +255XXXXXXXXX (9 digits after +255).');
            }
        }

        const $email = $step.find('#business_email');
        const emailVal = ($email.val() || '').trim();
        if (emailVal && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
            ok = false;
            markInvalid($email, 'Enter a valid email address.');
        }

        return ok;
    }
    function validateStep3($step) {
        let ok = true;

        if ($step.find('input[name="nature_of_business[]"]:checked').length === 0) {
            ok = false;
            $('#nature_of_business_error').html(
                `<div class="invalid-feedback d-block step-error">Select at least one nature of business.</div>`
            );
        }

        const isOtherChecked = $step.find('.nature-business-checkbox[value="Other"]').is(':checked');
        if (isOtherChecked) {
            const $other = $step.find('#nature_of_business_other');
            if (!$other.val()) {
                ok = false;
                markInvalid($other, 'Please specify the nature of business.');
            }
        }

        const isPep = ($step.find('#is_pep').val() || $step.find('[name="is_pep"]').val());
        if (isPep === 'yes') {
            const $pepSection = $step.find('.section-pep-details');
            if ($pepSection.is(':visible')) {
                const $name = $step.find('#emergency_fullname');
                const $phone = $step.find('#emergency_contact_number');
                if (!$name.val()) { ok = false; markInvalid($name, 'Full name is required for PEP.'); }
                if (!$phone.val()) { ok = false; markInvalid($phone, 'Contact number is required for PEP.'); }
            }
        }

        return ok;
    }
    function validateStep4($step) {
        let ok = true;

        if ($step.find('input[name="source"]:checked').length === 0) {
            ok = false;
            $('#source_error').html(
                `<div class="invalid-feedback d-block step-error">Select a source.</div>`
            );
        }

        const selectedSource = $step.find('input[name="source"]:checked').val();
        if (selectedSource === 'Import') {
            const $country = $step.find('select[name="country_of_origin"]');
            if (!$country.val()) {
                ok = false;
                markInvalid($country, 'Country of origin is required for Import.');
            }
        }

        $step.find('table.table-bordered tbody tr').each(function () {
            const $tr = $(this);
            const $brand = $tr.find('input[name="brand_name[]"]');
            const $supplier = $tr.find('input[name="supplier_name[]"]');
            const $year = $tr.find('select[name="since_when_year[]"]');

            if ($brand.length && !$brand.val()) { ok = false; markInvalid($brand, 'Brand name is required.'); }
            if ($supplier.length && !$supplier.val()) { ok = false; markInvalid($supplier, 'Supplier name is required.'); }
            if ($year.length && !$year.val()) { ok = false; markInvalid($year, 'Year is required.'); }
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
                markInvalid($branch, 'Branch name is required.');
            }

            if ($brand.length && !$brand.val()?.trim()) {
                ok = false;
                markInvalid($brand, 'Brand name is required.');
            }

            if ($key.length && !$key.val()?.trim()) {
                ok = false;
                markInvalid($key, 'Key personnel is required.');
            }

            if ($setup.length && !$setup.val()) {
                ok = false;
                markInvalid($setup, 'Setup is required.');
            }
        });

        if (!ok) scrollToFirstError($step);
        return ok;
    }
    function validateStep6($step) {
        const isEdit = $('#is_edit').val() === '1';
        if (isEdit) return true;
        applyDocRulesByComposition();
        return true;
    }
    function validateStep7($step)
    {
        let ok = true;

        const email = $step.find('#residential_email').val();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            ok = false;
            markInvalid($step.find('#residential_email'), 'Enter a valid email address.');
        }

        const pct = ($step.find('#owner_percentage').val() || '').trim();
        if (pct) {
            if (!/^\d{1,2}(\.\d{1,2})?$/.test(pct) || parseFloat(pct) >= 100) {
                ok = false;
                markInvalid($step.find('#owner_percentage'), 'Enter a valid percentage less than 100 (max 2 decimals).');
            }
        }

        if (isCreateMode()) {
            $step.find('input[type="file"][required]').each(function () {
                const $el = $(this);
                if (!this.files || this.files.length === 0) {
                    ok = false;
                    markInvalid($el, 'This document is required.');
                }
            });
        }

        return ok;
    }
    function validateStep8($step)
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
    function validateStep9($step) {
        let ok = true;

        const $consent = $step.find('#consent_declaration');
        console.log('$consent', $consent.is(':checked'));


        if (!$consent.is(':checked')) {
            ok = false;

            // If unchecked, add error message and mark it as invalid
            $consent.closest('p').after(
                `<div class="invalid-feedback d-block step-error">You must consent to continue.</div>`
            );
            $consent.addClass('is-invalid');
        } else {
            $consent.removeClass('is-invalid');
            $consent.closest('p').find('.step-error').remove();
        }
        if (ok && current === TOTAL) {
            $('#kycForm').submit();
        }
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
    async function serverValidateStep(step) {
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
            $next.html(`<span class="spinner-border spinner-border-sm me-2"></span>Validating...`);
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
        if (signaturePad && !signaturePad.isEmpty())
        {
            $('input[name="declarer_signature"]').val(signaturePad.toDataURL('image/png'));
        }
    }
    const canvas = document.getElementById('signature-pad');
    if (!canvas) return;
    if (signaturePad) return;

    function resizeCanvas()
    {
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
        if (existingSignature) {
            signaturePad.fromDataURL(existingSignature);
        }

        setTimeout(resizeCanvas, 100);
        window.addEventListener('resize', resizeCanvas);
    }

    $(document).on('change', 'select[name="business_composition_id"]', applyDocRulesByComposition);
    $(function () { applyDocRulesByComposition(); });

    $(document).on("click","#nextStep, #finalSubmit", function () {
        runWithLock(async () => {
            if (!validateStep(current)) return;
            resizeCanvas();
            const ok = await serverValidateStep(current);
            if (!ok) return;

            showStep(current + 1, { validate: false });
        }, true);
    });

    $(document).on('input', '#kycForm .force-255, #kycForm .email-format', function () {
        $(this).removeClass('is-invalid');
        $(this).nextAll('.step-error:first').remove();
    });

    $('#clear-signature').off('click').on('click', function () {
        signaturePad.clear();
        $('input[name="declarer_signature"]').val('');
        $('#declarer_signature_error').remove();
    });

    showStep(1);
})();
