$(function () {
    var currentStep = InspectConfig.resumeStep;
    var totalSections = InspectConfig.totalSections;
    var activeStreams = {};
    var isReadOnly = InspectConfig.readOnly || false;

    // Read-only mode: disable all inputs and hide action buttons
    if (isReadOnly) {
        $('.result-radio').prop('disabled', true);
        $('.remarks-input').prop('disabled', true);
        $('.btn-capture-photo').hide();
        $('.remove-photo').hide();
        $('.btn-next').hide();
        $('.btn-prev').hide();
        $('.btn-submit-final').hide();
        // Make all sections visible and all tabs clickable
        $('.wizard-section').addClass('active');
        $('.wizard-tab').removeClass('disabled').css({ 'opacity': 1, 'cursor': 'pointer' });
    }

    // Track which steps are completed (from server + client)
    var completedSteps = {};
    (InspectConfig.completedSections || []).forEach(function (i) { completedSteps[i] = true; });

    // Disable tabs that are not reachable
    updateTabStates();

    // On page load, scroll active tab into view and scroll page to active section
    if (currentStep > 0 && !isReadOnly) {
        setTimeout(function () {
            // Scroll tab strip horizontally to show the active tab
            var $activeTab = $('.wizard-tab[data-step="' + currentStep + '"]');
            if ($activeTab.length) {
                var $tabContainer = $activeTab.closest('.wizard-tabs');
                if ($tabContainer.length) {
                    var scrollLeft = $activeTab[0].offsetLeft - $tabContainer[0].offsetLeft - 20;
                    $tabContainer[0].scrollLeft = scrollLeft;
                }
            }
            // Scroll page to the active section
            var $activeSection = $('.wizard-section[data-section="' + currentStep + '"]');
            if ($activeSection.length) {
                $('html, body').animate({ scrollTop: $activeSection.offset().top - 180 }, 300);
            }
        }, 300);
    }

    function updateTabStates() {
        $('.wizard-tab').each(function () {
            var step = parseInt($(this).data('step'));
            // Reachable = completed step, or current step
            var reachable = completedSteps[step] || step === currentStep;
            $(this).toggleClass('disabled', !reachable);
            $(this).css('opacity', reachable ? 1 : 0.45);
            $(this).css('cursor', reachable ? 'pointer' : 'not-allowed');
        });
    }

    // ─── Result Radio Toggle → show/hide Not-OK extras ───────────
    $(document).on('change', '.result-radio', function () {
        var $card = $(this).closest('.checklist-card');
        var itemId = $card.data('item-id');
        var value = $(this).val();
        var $extras = $card.find('.not-ok-extras[data-parent-item="' + itemId + '"]');

        // Clear validation state
        $card.removeClass('is-invalid-card shake-row');

        if (value === 'not_ok') {
            $extras.show();
        } else {
            $extras.hide();
            // Clear photo when switching to OK
            $('#photo_' + itemId).val('');
            $('#preview-' + itemId).empty();
            stopItemCamera(itemId);
        }
    });

    // ─── Wizard Navigation ───────────────────────────────────────────
    function goToStep(step) {
        $('.wizard-section').removeClass('active');
        $('.wizard-section[data-section="' + step + '"]').addClass('active');

        $('.wizard-tab').removeClass('active');
        var $tab = $('.wizard-tab[data-step="' + step + '"]');
        $tab.addClass('active');

        // Scroll tab strip horizontally to show the active tab
        var $tabContainer = $tab.closest('.wizard-tabs');
        if ($tabContainer.length && $tab.length) {
            var scrollLeft = $tab[0].offsetLeft - $tabContainer[0].offsetLeft - 20;
            $tabContainer.animate({ scrollLeft: scrollLeft }, 200);
        }

        currentStep = step;
        updateTabStates();

        $('html, body').animate({ scrollTop: 0 }, 200);
    }

    // Tab clicks — only allow completed steps (to review) or current step
    $(document).on('click', '.wizard-tab', function () {
        var targetStep = parseInt($(this).data('step'));
        if (targetStep === currentStep) return;

        // Only allow navigating to completed steps
        if (!completedSteps[targetStep]) return;

        goToStep(targetStep);
    });

    // Next button: validate → save → go
    $(document).on('click', '.btn-next', function () {
        var step = parseInt($(this).data('step'));
        var $btn = $(this);
        validateAndSave(step, function () { goToStep(step + 1); }, $btn);
    });

    // Previous: only go to completed steps
    $(document).on('click', '.btn-prev', function () {
        var prevStep = parseInt($(this).data('step')) - 1;
        if (prevStep >= 0) goToStep(prevStep);
    });

    // Check if any item across all sections is marked not_ok
    function hasNotOkItems() {
        var found = false;
        $('.checklist-card[data-item-id]').each(function () {
            var $checked = $(this).find('.result-radio:checked');
            if ($checked.length && $checked.val() === 'not_ok') {
                found = true;
                return false; // break
            }
        });
        return found;
    }

    // Submit the form with optional rework_technician_id
    function submitFinalForm($btn, reworkTechnicianId) {
        finalSubmitting = true;

        $btn.prop('disabled', true);
        $btn.find('.btn-next-text').html('<i class="bx bx-loader-alt bx-spin me-1"></i>' + Lang.submitting);
        $('.btn-next, .btn-prev').prop('disabled', true);

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = InspectConfig.saveSignatureUrl;

        var csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = InspectConfig.csrfToken;
        form.appendChild(csrf);

        if (reworkTechnicianId) {
            var techInput = document.createElement('input');
            techInput.type = 'hidden';
            techInput.name = 'rework_technician_id';
            techInput.value = reworkTechnicianId;
            form.appendChild(techInput);
        }

        document.body.appendChild(form);
        form.submit();

        setTimeout(function () {
            if (finalSubmitting) {
                finalSubmitting = false;
                $btn.prop('disabled', false);
                $btn.find('.btn-next-text').html('<i class="bx bx-check me-1"></i>' + Lang.submit_inspection);
                $('.btn-next, .btn-prev').prop('disabled', false);
            }
        }, 30000);
    }

    // Submit final: validate last section → save → submit inspection
    var finalSubmitting = false;
    $(document).on('click', '.btn-submit-final', function () {
        if (finalSubmitting) return;
        var step = parseInt($(this).data('step'));
        var $btn = $(this);
        validateAndSave(step, function () {
            // Mark last step completed
            completedSteps[step] = true;
            var $tab = $('.wizard-tab[data-step="' + step + '"]');
            $tab.addClass('completed');
            $tab.find('.tab-icon').removeClass('bx-circle').addClass('bxs-check-circle').css('color', '#198754');

            // Check if any not_ok items exist
            if (hasNotOkItems()) {
                // Fetch rework technicians and show popup
                $.ajax({
                    url: InspectConfig.reworkTechniciansUrl,
                    method: 'GET',
                    headers: { 'X-CSRF-TOKEN': InspectConfig.csrfToken },
                    success: function (technicians) {
                        var optionsHtml = '<option value="">' + (Lang.select_technician || 'Select technician') + '</option>';
                        technicians.forEach(function (t) {
                            optionsHtml += '<option value="' + t.id + '">' + t.fullname + '</option>';
                        });

                        Swal.fire({
                            html:
                                '<div style="text-align:center; padding-top:8px;">' +
                                    '<div style="width:64px; height:64px; border-radius:50%; background:linear-gradient(135deg,#ff6b6b,#ee5a24); display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px;">' +
                                        '<i class="bx bx-wrench" style="font-size:28px; color:#fff;"></i>' +
                                    '</div>' +
                                    '<h4 style="font-weight:700; color:#1a1a2e; margin-bottom:6px;">' + (Lang.select_rework_technician || 'Select Rework Technician') + '</h4>' +
                                    '<p style="color:#6c757d; font-size:0.9rem; margin-bottom:20px;">' + (Lang.not_ok_items_found || 'Some items are marked as Not OK. Please select a technician for rework.') + '</p>' +
                                    '<div style="text-align:left; margin:0 8px;">' +
                                        '<label style="font-weight:600; font-size:0.85rem; color:#495057; margin-bottom:6px; display:block;">' + (Lang.select_technician || 'Select technician') + ' <span style="color:#e74c3c;">*</span></label>' +
                                        '<select id="swal-rework-tech" class="form-select" style="width:100%;">' + optionsHtml + '</select>' +
                                    '</div>' +
                                '</div>',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: true,
                            confirmButtonText: '<i class="bx bx-check me-1"></i>' + (Lang.submit_inspection || 'Submit'),
                            confirmButtonColor: '#198754',
                            customClass: {
                                popup: 'rework-tech-popup',
                                confirmButton: 'btn btn-success px-4'
                            },
                            didOpen: function () {
                                $('#swal-rework-tech').select2({
                                    dropdownParent: $('.swal2-container'),
                                    placeholder: Lang.select_technician || 'Select technician',
                                    allowClear: false,
                                    width: '100%'
                                });
                            },
                            preConfirm: function () {
                                var val = $('#swal-rework-tech').val();
                                if (!val) {
                                    Swal.showValidationMessage(Lang.please_select_technician || 'Please select a technician');
                                    return false;
                                }
                                return val;
                            }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                submitFinalForm($btn, result.value);
                            }
                        });
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: Lang.oops, text: Lang.something_went_wrong });
                    }
                });
            } else {
                // All OK — submit directly
                submitFinalForm($btn, null);
            }
        }, $btn);
    });

    // ─── Validate + Save ─────────────────────────────────────────────
    function validateAndSave(sectionIndex, onSuccess, $triggerBtn) {
        if (sectionIndex >= totalSections) { onSuccess(); return; }

        var $section = $('.wizard-section[data-section="' + sectionIndex + '"]');
        var allValid = true;
        var $firstInvalid = null;
        var errorMessages = [];

        $section.find('.checklist-card[data-item-id]').each(function () {
            var $card = $(this);
            var itemId = $card.data('item-id');
            var $checked = $card.find('.result-radio:checked');

            // Clear previous errors
            $card.removeClass('is-invalid-card shake-row');

            // 1) Result is required
            if (!$checked.length) {
                allValid = false;
                $card.addClass('is-invalid-card shake-row');
                if (!$firstInvalid) $firstInvalid = $card;
                return true; // continue
            }

            // 2) If Not OK → remarks + photo mandatory
            if ($checked.val() === 'not_ok') {
                var remarks = $card.find('.remarks-input').val().trim();
                var hasPhoto = $('#preview-' + itemId).find('img').length > 0 || $('#photo_' + itemId).val();

                if (!remarks) {
                    allValid = false;
                    $card.addClass('is-invalid-card shake-row');
                    $card.find('.remarks-input').addClass('is-invalid');
                    if (!$firstInvalid) $firstInvalid = $card;
                }
                if (!hasPhoto) {
                    allValid = false;
                    $card.addClass('is-invalid-card shake-row');
                    if (!$firstInvalid) $firstInvalid = $card;
                    if (errorMessages.indexOf(Lang.photo_required_not_ok) === -1) {
                        errorMessages.push(Lang.photo_required_not_ok);
                    }
                }
            }
        });

        if (!allValid) {
            var msg = Lang.all_items_required;
            if (errorMessages.length) {
                msg = errorMessages.join('<br>');
            }
            Swal.fire({ icon: 'warning', title: Lang.oops, html: msg }).then(function () {
                if ($firstInvalid) {
                    $('html, body').animate({ scrollTop: $firstInvalid.offset().top - 120 }, 400, function () {
                        $firstInvalid.addClass('shake-row');
                        setTimeout(function () { $firstInvalid.removeClass('shake-row'); }, 600);
                    });
                }
            });
            setTimeout(function () { $section.find('.shake-row').removeClass('shake-row'); }, 600);
            return;
        }

        // Clear all invalid states
        $section.find('.is-invalid, .is-invalid-card').removeClass('is-invalid is-invalid-card');

        // Collect items
        var items = [];
        $section.find('.checklist-card[data-item-id]').each(function () {
            var $card = $(this);
            items.push({
                checklist_item_id: $card.data('item-id'),
                result: $card.find('.result-radio:checked').val(),
                remarks: $card.find('.remarks-input').val() || null
            });
        });

        // Save
        var $btnText = $triggerBtn ? $triggerBtn.find('.btn-next-text') : null;
        var originalText = $btnText ? $btnText.html() : '';
        if ($triggerBtn) {
            $triggerBtn.prop('disabled', true);
            $btnText.html('<i class="bx bx-loader-alt bx-spin me-1"></i>' + Lang.saving_section);
        }

        $.ajax({
            url: InspectConfig.saveSectionUrl,
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': InspectConfig.csrfToken },
            data: JSON.stringify({ items: items }),
            success: function () {
                // Mark step as completed
                completedSteps[sectionIndex] = true;
                var $tab = $('.wizard-tab[data-step="' + sectionIndex + '"]');
                $tab.addClass('completed');
                $tab.find('.tab-icon').removeClass('bx-circle').addClass('bxs-check-circle').css('color', '#198754');
                updateTabStates();
                onSuccess();
            },
            error: function () {
                Swal.fire({ icon: 'error', title: Lang.oops, text: Lang.save_step_failed });
            },
            complete: function () {
                if ($triggerBtn && !finalSubmitting) {
                    $triggerBtn.prop('disabled', false);
                    $btnText.html(originalText);
                }
            }
        });
    }

    // ─── Camera Capture → separate upload ────────────────────────────
    $(document).on('click', '.btn-capture-photo', async function () {
        var itemId = $(this).data('item-id');
        var container = $('#camera-' + itemId);

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Swal.fire(Lang.camera_error, Lang.camera_not_supported_message, 'error');
            return;
        }
        if (activeStreams[itemId]) return;

        try {
            var stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
            });
            activeStreams[itemId] = stream;

            var video = document.createElement('video');
            video.srcObject = stream;
            video.autoplay = true;
            video.playsInline = true;
            video.className = 'w-100 rounded';

            var btnRow = document.createElement('div');
            btnRow.className = 'd-flex gap-2 mt-1';

            var snapBtn = document.createElement('button');
            snapBtn.type = 'button';
            snapBtn.className = 'btn btn-danger btn-sm flex-fill';
            snapBtn.innerHTML = '<i class="bx bx-camera"></i> ' + Lang.capture_photo;
            snapBtn.onclick = function () { captureAndUpload(itemId, video); };

            var cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-outline-secondary btn-sm flex-fill';
            cancelBtn.innerHTML = '<i class="bx bx-x"></i> ' + Lang.close;
            cancelBtn.onclick = function () { stopItemCamera(itemId); };

            btnRow.appendChild(snapBtn);
            btnRow.appendChild(cancelBtn);
            container.empty().append(video, btnRow);
        } catch (err) {
            Swal.fire(Lang.camera_error, Lang.camera_permission_error, 'error');
        }
    });

    function captureAndUpload(itemId, video) {
        var canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(function (blob) {
            stopItemCamera(itemId);

            var $preview = $('#preview-' + itemId);
            $preview.html('<span class="text-muted small"><i class="bx bx-loader-alt bx-spin"></i> ' + Lang.uploading_photo + '</span>');

            var fd = new FormData();
            fd.append('photo', blob, 'inspection_' + itemId + '.png');
            fd.append('checklist_item_id', itemId);

            $.ajax({
                url: InspectConfig.uploadPhotoUrl,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': InspectConfig.csrfToken },
                data: fd,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.success) {
                        $preview.html(
                            '<img src="' + res.url + '" class="captured-thumb preview-img" data-has-photo="1">' +
                            ' <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-photo" data-item-id="' + itemId + '">' +
                            '<i class="bx bx-trash"></i></button>' +
                            ' <span class="text-success small"><i class="bx bx-check"></i></span>'
                        );
                    }
                },
                error: function () {
                    $preview.html('<span class="text-danger small">' + Lang.photo_upload_failed + '</span>');
                }
            });
        }, 'image/png');
    }

    function stopItemCamera(itemId) {
        if (activeStreams[itemId]) {
            activeStreams[itemId].getTracks().forEach(function (t) { t.stop(); });
            delete activeStreams[itemId];
        }
        $('#camera-' + itemId).empty();
    }

    $(document).on('click', '.remove-photo', function () {
        var itemId = $(this).data('item-id');
        $('#photo_' + itemId).val('');
        $('#preview-' + itemId).empty();
    });

    // Clear invalid state on input
    $(document).on('input', '.remarks-input', function () {
        $(this).removeClass('is-invalid');
        $(this).closest('.checklist-card').removeClass('is-invalid-card');
    });


    // ─── Reference Image Slider (card-level) ─────────────────────────
    $(document).on('click', '.ref-slider-prev', function (e) {
        e.stopPropagation();
        var $card = $(this).closest('.checklist-card-img');
        var $slides = $card.find('.ref-slide');
        var $active = $card.find('.ref-slide.active');
        var idx = $slides.index($active);
        var newIdx = (idx - 1 + $slides.length) % $slides.length;
        $active.removeClass('active');
        $slides.eq(newIdx).addClass('active');
        $card.find('.ref-slider-counter').text((newIdx + 1) + ' / ' + $slides.length);
    });

    $(document).on('click', '.ref-slider-next', function (e) {
        e.stopPropagation();
        var $card = $(this).closest('.checklist-card-img');
        var $slides = $card.find('.ref-slide');
        var $active = $card.find('.ref-slide.active');
        var idx = $slides.index($active);
        var newIdx = (idx + 1) % $slides.length;
        $active.removeClass('active');
        $slides.eq(newIdx).addClass('active');
        $card.find('.ref-slider-counter').text((newIdx + 1) + ' / ' + $slides.length);
    });

    // ─── Image Preview Modal ────────────────────────────────────────
    var modalImages = [];
    var modalCurrentIdx = 0;

    // Single image preview (captured photos etc.)
    $(document).on('click', '.preview-img', function () {
        var src = $(this).attr('src');
        var alt = $(this).attr('alt') || '';
        modalImages = [];
        $('#imagePreviewSrc').attr('src', src).show();
        $('#refCarousel').addClass('d-none');
        $('#modalImgCounter').text('');
        $('#imagePreviewTitle').html('<i class="bx bx-image"></i> ' + alt);
        new bootstrap.Modal($('#imagePreviewModal')[0]).show();
    });

    // Multi-image preview (reference images — click expand or slide)
    $(document).on('click', '.ref-expand-btn, .ref-slide', function (e) {
        e.stopPropagation();
        var $card = $(this).closest('.checklist-card-img');
        var images = $card.data('images') || [];
        var name = $card.data('item-name') || '';
        var $active = $card.find('.ref-slide.active');
        var startIdx = $card.find('.ref-slide').index($active);
        if (startIdx < 0) startIdx = 0;
        var base = (window.InspectConfig && window.InspectConfig.assetBase) || '/';

        if (images.length <= 1) {
            // Single image — use simple preview
            var src = images.length ? base + images[0] : $card.find('img').first().attr('src');
            modalImages = [];
            $('#imagePreviewSrc').attr('src', src).show();
            $('#refCarousel').addClass('d-none');
            $('#modalImgCounter').text('');
        } else {
            // Multiple images — carousel
            modalImages = images;
            modalCurrentIdx = startIdx;
            $('#imagePreviewSrc').hide();
            var html = '';
            images.forEach(function (path) {
                html += '<img src="' + base + path + '" alt="' + name + '" style="max-width:100%; max-height:82vh; object-fit:contain; border-radius:0.5rem; display:none; transition:transform 0.3s ease;">';
            });
            $('#refCarouselInner').html(html);
            $('#refCarouselInner img').eq(modalCurrentIdx).show();
            $('#refCarousel').removeClass('d-none');
            $('#modalImgCounter').text((modalCurrentIdx + 1) + ' / ' + images.length);
        }
        $('#imagePreviewTitle').html('<i class="bx bx-image"></i> ' + name);
        new bootstrap.Modal($('#imagePreviewModal')[0]).show();
    });

    // Modal carousel navigation
    $(document).on('click', '#modalPrev', function () {
        if (!modalImages.length) return;
        $('#refCarouselInner img').eq(modalCurrentIdx).hide();
        modalCurrentIdx = (modalCurrentIdx - 1 + modalImages.length) % modalImages.length;
        $('#refCarouselInner img').eq(modalCurrentIdx).show().css('transform', '');
        $('#modalImgCounter').text((modalCurrentIdx + 1) + ' / ' + modalImages.length);
    });

    $(document).on('click', '#modalNext', function () {
        if (!modalImages.length) return;
        $('#refCarouselInner img').eq(modalCurrentIdx).hide();
        modalCurrentIdx = (modalCurrentIdx + 1) % modalImages.length;
        $('#refCarouselInner img').eq(modalCurrentIdx).show().css('transform', '');
        $('#modalImgCounter').text((modalCurrentIdx + 1) + ' / ' + modalImages.length);
    });

    // ─── Cleanup ─────────────────────────────────────────────────────
    $(window).on('beforeunload', function () {
        Object.keys(activeStreams).forEach(stopItemCamera);
    });
});
