$(function () {
    var selectedItems = {};
    var photoFiles = {};
    var activeStreams = {};
    var Lang = window.Lang;

    // Toggle card selection
    $(document).on('click', '.reopen-item-card', function (e) {
        if ($(e.target).is('textarea, input, button, img, video') || $(e.target).closest('textarea, button, .btn-capture-photo, .reopen-camera-container').length) return;

        var $card = $(this);
        var $checkbox = $card.find('.item-checkbox');
        var itemId = $card.data('item-id');

        $checkbox.prop('checked', !$checkbox.prop('checked'));
        $card.toggleClass('selected', $checkbox.prop('checked'));

        if ($checkbox.prop('checked')) {
            selectedItems[itemId] = true;
        } else {
            delete selectedItems[itemId];
            delete photoFiles[itemId];
            stopItemCamera(itemId);
            $card.find('.reopen-remarks-input').val('');
            $card.find('.reopen-photo-preview').addClass('d-none').attr('src', '');
            $card.find('.reopen-photo-name').text('');
        }

        updateCounter();
    });

    // Prevent checkbox click from double-toggling
    $(document).on('click', '.item-checkbox', function (e) {
        e.stopPropagation();

        var $card = $(this).closest('.reopen-item-card');
        var itemId = $card.data('item-id');

        $card.toggleClass('selected', $(this).prop('checked'));

        if ($(this).prop('checked')) {
            selectedItems[itemId] = true;
        } else {
            delete selectedItems[itemId];
            delete photoFiles[itemId];
            stopItemCamera(itemId);
            $card.find('.reopen-remarks-input').val('');
            $card.find('.reopen-photo-preview').addClass('d-none').attr('src', '');
            $card.find('.reopen-photo-name').text('');
        }

        updateCounter();
    });

    // ─── Live Camera Capture ──────────────────────────
    $(document).on('click', '.btn-capture-photo', async function (e) {
        e.stopPropagation();
        var itemId = $(this).data('item-id');
        var container = $('.reopen-camera-container[data-item-id="' + itemId + '"]');

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
            snapBtn.onclick = function () { capturePhoto(itemId, video); };

            var cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-outline-secondary btn-sm flex-fill';
            cancelBtn.innerHTML = '<i class="bx bx-x"></i> ' + Lang.close;
            cancelBtn.onclick = function () { stopItemCamera(itemId); };

            btnRow.appendChild(snapBtn);
            btnRow.appendChild(cancelBtn);
            container.empty().append(video, btnRow).removeClass('d-none');
        } catch (err) {
            Swal.fire(Lang.camera_error, Lang.camera_permission_error, 'error');
        }
    });

    function capturePhoto(itemId, video) {
        var canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(function (blob) {
            stopItemCamera(itemId);

            // Store as File object for form submission
            var file = new File([blob], 'reopen_' + itemId + '.png', { type: 'image/png' });
            photoFiles[itemId] = file;

            // Show preview
            var url = URL.createObjectURL(blob);
            var $preview = $('.reopen-photo-preview[data-item-id="' + itemId + '"]');
            $preview.attr('src', url).removeClass('d-none');
            $('.reopen-photo-name[data-item-id="' + itemId + '"]').text(Lang.image_captured);
        }, 'image/png');
    }

    function stopItemCamera(itemId) {
        if (activeStreams[itemId]) {
            activeStreams[itemId].getTracks().forEach(function (t) { t.stop(); });
            delete activeStreams[itemId];
        }
        $('.reopen-camera-container[data-item-id="' + itemId + '"]').empty().addClass('d-none');
    }

    function updateCounter() {
        var count = Object.keys(selectedItems).length;
        $('#selected-count').text(count + ' ' + Lang.items + ' ' + Lang.selected);
    }

    // Initialize Select2 on technician dropdown
    $('#technician-select').select2({
        placeholder: Lang.select_technician,
        allowClear: true,
        width: '100%'
    });

    // Submit
    $('#btn-submit-reopen').on('click', function () {
        var techId = $('#technician-select').val();
        var itemIds = Object.keys(selectedItems);

        // Validate technician
        if (!techId) {
            Swal.fire({
                icon: 'warning',
                title: Lang.oops,
                text: Lang.select_technician_required
            });
            return;
        }

        // Validate at least one item selected
        if (itemIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: Lang.oops,
                text: Lang.reopen_select_items_required
            });
            return;
        }

        // Validate all selected items have remarks
        var missingRemarks = false;
        itemIds.forEach(function (id) {
            var remarks = $('[data-item-id="' + id + '"].reopen-remarks-input').val();
            if (!remarks || !remarks.trim()) {
                missingRemarks = true;
            }
        });

        if (missingRemarks) {
            Swal.fire({
                icon: 'warning',
                title: Lang.oops,
                text: Lang.reopen_remarks_required
            });
            return;
        }

        // Confirm
        Swal.fire({
            title: Lang.reopen_confirm,
            text: Lang.reopen_confirm_text.replace(':count', itemIds.length),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: Lang.reopen,
            cancelButtonText: Lang.cancel,
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return submitForm(techId, itemIds);
            },
            allowOutsideClick: function () { return !Swal.isLoading(); }
        });
    });

    function submitForm(techId, itemIds) {
        var $form = $('#reopen-form');
        var $container = $('#form-items-container');

        $container.empty();
        $('#form-technician-id').val(techId);

        itemIds.forEach(function (id, index) {
            var remarks = $('[data-item-id="' + id + '"].reopen-remarks-input').val();
            $container.append('<input type="hidden" name="items[' + index + '][checklist_item_id]" value="' + id + '">');
            $container.append('<input type="hidden" name="items[' + index + '][remarks]" value="' + $('<div>').text(remarks).html() + '">');

            // Append photo file if exists
            if (photoFiles[id]) {
                var fileInput = $('<input type="file" name="items[' + index + '][image]">').hide();
                $container.append(fileInput);

                // Transfer file using DataTransfer
                var dt = new DataTransfer();
                dt.items.add(photoFiles[id]);
                fileInput[0].files = dt.files;
            }
        });

        $form.submit();
    }
});
