$(function () {
    var apiEngineNumber = null;
    var apiData = null;
    var chassisQrCode = null;
    var engineQrCode = null;
    var chassisScannerRunning = false;
    var engineScannerRunning = false;
    var currentStep = 1;

    // Category mapping: prod_line value → expected route category
    var prodLineMap = {
        'TVS 2W': '2w',
        'TVS 3W': '3w'
    };

    // ─── Step Management ─────────────────────────────────────────────
    function goToStep(step) {
        currentStep = step;

        // Update step indicator
        $('#wizard-steps .wizard-step').each(function () {
            var s = parseInt($(this).attr('data-step'));
            $(this).removeClass('active completed');
            var $number = $(this).find('.step-number');

            if (s < step) {
                $(this).addClass('completed');
                $number.html('<i class="bx bx-check" style="font-size: 1rem;"></i>');
            } else if (s === step) {
                $(this).addClass('active');
                $number.text(s);
            } else {
                $number.text(s);
            }
        });

        // Show/hide panels
        if (step >= 2) {
            var $verify = $('#step-verify');
            if (!$verify.is(':visible')) {
                $verify.show().addClass('reveal step-active');
                setTimeout(function () { $verify.removeClass('reveal'); }, 400);
            }
            $verify.removeClass('step-completed step-active');
            if (step > 2) {
                $verify.addClass('step-completed');
            } else {
                $verify.addClass('step-active');
            }
        }

        if (step >= 3) {
            var $proceed = $('#step-proceed');
            if (!$proceed.is(':visible')) {
                $proceed.show().addClass('reveal step-active');
                setTimeout(function () { $proceed.removeClass('reveal'); }, 400);
                // Scroll to step 3
                $('html, body').animate({
                    scrollTop: $proceed.offset().top - 100
                }, 400);
            }
            $proceed.removeClass('step-completed step-active').addClass('step-active');
        }
    }

    function resetToStep1() {
        currentStep = 1;

        // Reset step indicators
        $('#wizard-steps .wizard-step').each(function () {
            var s = parseInt($(this).attr('data-step'));
            $(this).removeClass('active completed');
            $(this).find('.step-number').text(s);
            if (s === 1) {
                $(this).addClass('active');
            }
        });

        // Hide steps 2 and 3
        $('#step-verify').hide().removeClass('reveal step-active step-completed');
        $('#step-proceed').hide().removeClass('reveal step-active step-completed');

        // Reset detail values
        $('#detail-chassis, #detail-engine, #detail-model, #detail-color, #detail-prod-line').text('-');
        $('#category-warning').hide();
        $('#model-selection').hide();

        // Reset API data
        apiEngineNumber = null;
        apiData = null;

        // Stop scanners if running
        stopChassisScanner();
        stopEngineScanner();
    }

    // ─── Chassis QR Scanner ────────────────────────────────────────────
    $('#btn-scan-chassis').on('click', function () {
        startChassisScanner();
    });

    $('#btn-stop-chassis-scanner').on('click', function () {
        stopChassisScanner();
    });

    function startChassisScanner() {
        // Reset if re-scanning
        if (currentStep > 1) {
            resetToStep1();
        }

        $('#chassis-qr-container').show();
        $('#btn-scan-chassis').prop('disabled', true);

        chassisQrCode = new Html5Qrcode("chassis-qr-reader");

        chassisQrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onChassisScanSuccess,
            function () { /* ignore scan failure */ }
        ).then(function () {
            chassisScannerRunning = true;
            $('html, body').animate({
                scrollTop: $('#chassis-qr-container').offset().top - 80
            }, 300);
        }).catch(function () {
            $('#chassis-qr-container').hide();
            $('#btn-scan-chassis').prop('disabled', false);
            Swal.fire({
                icon: 'error',
                title: Lang.camera_access_denied_title,
                text: Lang.camera_access_denied_message
            });
        });
    }

    function stopChassisScanner() {
        if (chassisQrCode && chassisScannerRunning) {
            chassisQrCode.stop().then(function () {
                chassisScannerRunning = false;
                chassisQrCode.clear();
                $('#chassis-qr-container').hide();
                $('#btn-scan-chassis').prop('disabled', false);
            }).catch(function () {
                $('#chassis-qr-container').hide();
                $('#btn-scan-chassis').prop('disabled', false);
            });
        } else {
            $('#chassis-qr-container').hide();
            $('#btn-scan-chassis').prop('disabled', false);
        }
    }

    function onChassisScanSuccess(decodedText) {
        stopChassisScanner();

        var chassis = decodedText.trim();
        if (!chassis) return;

        // Show loading
        $('#chassis-loading').show();
        $('#btn-scan-chassis').prop('disabled', true);

        // Call lookup API
        $.ajax({
            url: PaiConfig.lookupUrl,
            method: 'GET',
            data: { chassis_number: chassis },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.duplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: Lang.oops,
                        html: Lang.pai_already_exists + '<br><strong>' + response.job_card_id + '</strong> <span class="badge bg-primary-transparent">' + response.status + '</span>'
                    });
                    return;
                }

                if (response.discarded_pending) {
                    Swal.fire({
                        icon: 'warning',
                        title: Lang.oops,
                        text: response.message || Lang.chassis_discarded_pending
                    });
                    return;
                }

                if (response.success && response.data) {
                    var data = response.data;
                    apiEngineNumber = data.engine_number;
                    apiData = data;

                    $('#detail-chassis').text(data.chassis_number || '-');
                    $('#detail-engine').text(data.engine_number || '-');
                    $('#detail-model').text(data.model || '-');
                    $('#detail-color').text(data.color || '-');
                    $('#detail-prod-line').text(data.prod_line || '-');

                    // Category cross-validation: block if prod_line doesn't match
                    var expectedCategory = prodLineMap[data.prod_line];
                    if (expectedCategory && expectedCategory !== PaiConfig.category) {
                        var prodLineLabel = data.prod_line;
                        var expectedLabel = expectedCategory.toUpperCase();
                        Swal.fire({
                            icon: 'error',
                            title: Lang.category_mismatch_title || Lang.oops,
                            text: Lang.category_mismatch
                                .replace(':prod_line', prodLineLabel)
                                .replace(':expected_category', expectedLabel),
                            confirmButtonColor: '#dc3545',
                            allowOutsideClick: false
                        });
                        resetToStep1();
                        return;
                    }

                    // Advance to step 2
                    goToStep(2);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: Lang.oops,
                        text: Lang.vehicle_not_found
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: Lang.oops,
                    text: Lang.something_went_wrong
                });
            },
            complete: function () {
                $('#chassis-loading').hide();
                $('#btn-scan-chassis').prop('disabled', false);
            }
        });
    }

    // ─── Engine QR Scanner ────────────────────────────────────────────
    $('#btn-scan-engine').on('click', function () {
        if (!apiEngineNumber) {
            Swal.fire({
                icon: 'warning',
                title: Lang.oops,
                text: Lang.enter_chassis_first
            });
            return;
        }
        startEngineScanner();
    });

    $('#btn-stop-engine-scanner').on('click', function () {
        stopEngineScanner();
    });

    function startEngineScanner() {
        $('#engine-qr-container').show();
        $('#btn-scan-engine').prop('disabled', true);

        engineQrCode = new Html5Qrcode("engine-qr-reader");

        engineQrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onEngineScanSuccess,
            function () { /* ignore scan failure */ }
        ).then(function () {
            engineScannerRunning = true;
            $('html, body').animate({
                scrollTop: $('#engine-qr-container').offset().top - 80
            }, 300);
        }).catch(function () {
            $('#engine-qr-container').hide();
            $('#btn-scan-engine').prop('disabled', false);
            Swal.fire({
                icon: 'error',
                title: Lang.camera_access_denied_title,
                text: Lang.camera_access_denied_message
            });
        });
    }

    function stopEngineScanner() {
        if (engineQrCode && engineScannerRunning) {
            engineQrCode.stop().then(function () {
                engineScannerRunning = false;
                engineQrCode.clear();
                $('#engine-qr-container').hide();
                $('#btn-scan-engine').prop('disabled', false);
            }).catch(function () {
                $('#engine-qr-container').hide();
                $('#btn-scan-engine').prop('disabled', false);
            });
        } else {
            $('#engine-qr-container').hide();
            $('#btn-scan-engine').prop('disabled', false);
        }
    }

    function onEngineScanSuccess(decodedText) {
        stopEngineScanner();

        var scannedEngine = decodedText.trim();

        if (scannedEngine.toUpperCase() === apiEngineNumber.toUpperCase()) {
            // ── Match: try auto-match model ──
            var matchedType = autoMatchVehicleType();
            if (matchedType) {
                Swal.fire({
                    icon: 'success',
                    title: Lang.engine_match_title,
                    html: '<strong>' + matchedType.name + '</strong>',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then(function () {
                    $('#store-chassis').val(apiData.chassis_number);
                    $('#store-engine').val(apiData.engine_number);
                    $('#store-color').val(apiData.color || '');
                    $('#store-vehicle-type').val(matchedType.id);
                    $('#form-store-initial').submit();
                });
            } else {
                goToStep(3);
                initSelect2();
            }
        } else {
            // ── Mismatch: create discarded record ──
            Swal.fire({
                icon: 'error',
                title: Lang.engine_mismatch_title,
                html: Lang.engine_mismatch_message
                    .replace(':scanned', scannedEngine)
                    .replace(':expected', apiEngineNumber)
                    + '<br><br><small class="text-muted">' + Lang.discarded_will_be_created + '</small>',
                confirmButtonColor: '#dc3545',
                confirmButtonText: Lang.ok,
                allowOutsideClick: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    createDiscardedRecord(scannedEngine);
                }
            });
        }
    }

    function createDiscardedRecord(scannedEngine) {
        Swal.fire({
            title: Lang.processing,
            allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); }
        });

        $.ajax({
            url: PaiConfig.storeDiscardedUrl,
            method: 'POST',
            data: {
                chassis_number: apiData.chassis_number,
                engine_number_expected: apiData.engine_number,
                engine_number_scanned: scannedEngine,
                model: apiData.model || '',
                color: apiData.color || '',
                prod_line: apiData.prod_line || '',
                category: PaiConfig.category
            },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                Swal.fire({
                    icon: 'info',
                    title: Lang.engine_mismatch_title,
                    text: Lang.discarded_created_redirect,
                    confirmButtonColor: '#2E3C7E',
                    allowOutsideClick: false
                }).then(function () {
                    window.location.href = response.redirect || '/dashboard';
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: Lang.oops,
                    text: Lang.something_went_wrong
                });
            }
        });
    }

    // ─── Auto Match Vehicle Type ─────────────────────────────────────
    function autoMatchVehicleType() {
        if (!apiData || !apiData.model) return null;

        var model = normalize(apiData.model);
        var types = PaiConfig.vehicleTypes;

        for (var i = 0; i < types.length; i++) {
            var name = normalize(types[i].name || '');
            var slug = normalize(types[i].slug || '');
            if (name === model || slug === model) {
                return types[i];
            }
        }
        return null;
    }

    function normalize(str) {
        return str.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    }

    // ─── Model Selection ─────────────────────────────────────────────
    function initSelect2() {
        if (!$('#vehicle-type-select').data('select2')) {
            $('#vehicle-type-select').select2({
                width: '100%',
                placeholder: Lang.select_model
            });
        }
    }

    $('#btn-proceed').on('click', function () {
        var selectedType = $('#vehicle-type-select').val();
        if (!selectedType) {
            Swal.fire({
                icon: 'warning',
                title: Lang.oops,
                text: Lang.field_required
            });
            return;
        }

        $('#store-chassis').val(apiData.chassis_number);
        $('#store-engine').val(apiData.engine_number);
        $('#store-color').val(apiData.color || '');
        $('#store-vehicle-type').val(selectedType);
        $('#form-store-initial').submit();
    });
});
