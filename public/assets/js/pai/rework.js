$(function () {
    var activeStreams = {};

    // ─── Auto-save remarks on blur ──────────────────────────────────
    $(document).on('blur', '.rework-remarks', function () {
        var $textarea = $(this);
        var itemId = $textarea.data('item-id');
        var remarks = $textarea.val();

        $.ajax({
            url: ReworkConfig.saveSectionUrl,
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': ReworkConfig.csrfToken },
            data: JSON.stringify({
                checklist_item_id: itemId,
                remarks: remarks
            })
        });
    });

    // ─── Camera Capture for rework photos ──────────────────────────
    $(document).on('click', '.btn-capture-rework-photo', async function () {
        var itemId = $(this).data('item-id');
        var container = $('#rework-camera-' + itemId);

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
            snapBtn.className = 'btn btn-success btn-sm flex-fill';
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

            var $preview = $('#rework-preview-' + itemId);
            $preview.html('<span class="text-muted small"><i class="bx bx-loader-alt bx-spin"></i> ' + Lang.uploading_photo + '</span>');

            var fd = new FormData();
            fd.append('photo', blob, 'rework_' + itemId + '.png');
            fd.append('checklist_item_id', itemId);

            $.ajax({
                url: ReworkConfig.uploadPhotoUrl,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': ReworkConfig.csrfToken },
                data: fd,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.success) {
                        $preview.html(
                            '<img src="' + res.url + '" class="captured-thumb preview-img" data-has-photo="1">' +
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
        $('#rework-camera-' + itemId).empty();
    }

    // ─── Submit Rework ──────────────────────────────────────────────
    $('#btn-submit-rework').on('click', function () {
        // Validate all items have remarks and photo
        var allValid = true;
        var $firstInvalid = null;
        $('.rework-item-card').each(function () {
            var $card = $(this);
            var itemId = $card.data('item-id');
            var remarks = $card.find('.rework-remarks').val().trim();
            var hasPhoto = $('#rework-preview-' + itemId).find('img').length > 0;

            if (!remarks || !hasPhoto) {
                allValid = false;
                $card.css('border-color', '#dc3545');
                if (!$firstInvalid) $firstInvalid = $card;
            } else {
                $card.css('border-color', '#dee2e6');
            }
        });

        if (!allValid) {
            Swal.fire({ icon: 'warning', title: Lang.oops, text: Lang.rework_all_items_required }).then(function () {
                if ($firstInvalid) {
                    $('html, body').animate({ scrollTop: $firstInvalid.offset().top - 120 }, 400);
                }
            });
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).find('span').html('<i class="bx bx-loader-alt bx-spin me-1"></i>' + Lang.submitting);

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = ReworkConfig.submitUrl;

        var csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = ReworkConfig.csrfToken;
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    });

    // ─── Image Preview Modal ────────────────────────────────────────
    $(document).on('click', '.preview-img', function () {
        var src = $(this).attr('src');
        $('#imagePreviewSrc').attr('src', src);
        new bootstrap.Modal($('#imagePreviewModal')[0]).show();
    });

    // ─── Cleanup ────────────────────────────────────────────────────
    $(window).on('beforeunload', function () {
        Object.keys(activeStreams).forEach(stopItemCamera);
    });
});
