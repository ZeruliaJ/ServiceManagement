/**
 * Document Capture Module
 * Handles camera capture and file upload toggle for KYC documents
 */
$(document).ready(function() {
    let activeStreams = {};

    // Toggle between upload and camera modes
    $(document).on('click', '.doc-mode-btn', function() {
        const field = $(this).data('field');
        const mode = $(this).data('mode');

        // Update button states
        $(`.doc-mode-btn[data-field="${field}"]`).removeClass('active');
        $(this).addClass('active');

        // Toggle sections
        const uploadSection = $(`#upload-section-${field}`);
        const cameraSection = $(`#camera-section-${field}`);

        if (mode === 'upload') {
            uploadSection.removeClass('d-none');
            cameraSection.addClass('d-none');
            // Stop camera if running
            stopDocCamera(field);
            // Clear base64 data
            $(`#${field}_base64`).val('');
        } else {
            uploadSection.addClass('d-none');
            cameraSection.removeClass('d-none');
            // Clear file input
            $(`#${field}`).val('');
        }
    });

    // Open camera for document capture
    $(document).on('click', '.open-doc-camera', async function() {
        const field = $(this).data('field');
        const container = $(`#doc-camera-container-${field}`);

        if (!navigator.mediaDevices?.getUserMedia) {
            Swal.fire(window.Lang.camera_error, window.Lang.camera_not_supported_message, 'error');
            return;
        }

        // Check if camera is already open for this field
        if (activeStreams[field]) {
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment', // Use back camera on mobile
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });

            activeStreams[field] = stream;

            // Create video element
            const video = document.createElement('video');
            video.srcObject = stream;
            video.autoplay = true;
            video.playsInline = true;
            video.className = 'w-100 rounded';
            video.id = `video-${field}`;

            // Create capture button
            const captureBtn = document.createElement('button');
            captureBtn.type = 'button';
            captureBtn.className = 'btn btn-primary btn-sm w-100 mt-2';
            captureBtn.innerHTML = '<i class="bx bx-camera"></i> Capture';
            captureBtn.onclick = () => captureDocPhoto(field, video);

            // Create cancel button
            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-outline-primary btn-sm w-100 mt-1';
            cancelBtn.innerHTML = '<i class="bx bx-x"></i> Cancel';
            cancelBtn.onclick = () => stopDocCamera(field);

            container.empty().append(video, captureBtn, cancelBtn);

        } catch (error) {
            console.error('Camera error:', error);
            Swal.fire(window.Lang.camera_error, window.Lang.camera_permission_error, 'error');
        }
    });

    // Capture photo from video stream
    window.captureDocPhoto = function(field, video) {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        const imageData = canvas.toDataURL('image/png');

        // Store base64 data in hidden input
        $(`#${field}_base64`).val(imageData);

        // Stop camera
        stopDocCamera(field);

        // Show preview
        showDocPreview(field, imageData);

        // Clear file input to avoid conflicts
        $(`#${field}`).val('');
    };

    // Stop camera stream
    window.stopDocCamera = function(field) {
        if (activeStreams[field]) {
            activeStreams[field].getTracks().forEach(track => track.stop());
            delete activeStreams[field];
        }
        $(`#doc-camera-container-${field}`).empty();
    };

    // Show preview of captured image
    window.showDocPreview = function(field, imageData) {
        const previewContainer = $(`#preview-${field}`);
        const existingLink = previewContainer.find('a[href^="http"]').first();

        // Create preview HTML
        let previewHtml = `
            <div class="captured-preview position-relative d-inline-block">
                <img src="${imageData}" class="img-thumbnail" style="max-height: 100px; cursor: pointer;"
                     onclick="window.open('${imageData}', '_blank')">
                <button type="button" class="btn btn-danger btn-sm position-absolute"
                        style="top: -5px; right: -5px; padding: 0 5px; font-size: 12px;"
                        onclick="removeDocCapture('${field}')">
                    <i class="bx bx-x"></i>
                </button>
                <div class="text-success small mt-1"><i class="bx bx-check"></i> ${window.Lang.image_captured}</div>
            </div>
        `;

        // Keep existing document link if present
        if (existingLink.length) {
            previewHtml = existingLink.prop('outerHTML') + '<br>' + previewHtml;
        }

        previewContainer.html(previewHtml);
    };

    // Remove captured image
    window.removeDocCapture = function(field) {
        $(`#${field}_base64`).val('');
        const previewContainer = $(`#preview-${field}`);
        previewContainer.find('.captured-preview').remove();
    };

    // Handle file input change - show preview
    $(document).on('change', '.doc-file-input', function() {
        const field = $(this).data('field');
        const file = this.files[0];

        if (file) {
            const previewContainer = $(`#preview-${field}`);
            const existingLink = previewContainer.find('a[href^="http"]').first();

            // Clear any captured image
            $(`#${field}_base64`).val('');
            previewContainer.find('.captured-preview').remove();

            // Show file name preview
            let previewHtml = `
                <div class="file-preview">
                    <span class="text-success small">
                        <i class="bx bx-file"></i> ${file.name}
                        <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2"
                                onclick="clearDocFile('${field}')">
                            <i class="bx bx-x"></i>
                        </button>
                    </span>
                </div>
            `;

            // Keep existing document link
            if (existingLink.length) {
                previewHtml = existingLink.prop('outerHTML') + '<br>' + previewHtml;
            }

            previewContainer.html(previewHtml);
        }
    });

    // Clear file input
    window.clearDocFile = function(field) {
        $(`#${field}`).val('');
        const previewContainer = $(`#preview-${field}`);
        previewContainer.find('.file-preview').remove();
    };

    // Clean up streams when leaving page
    $(window).on('beforeunload', function() {
        Object.keys(activeStreams).forEach(field => {
            stopDocCamera(field);
        });
    });
});
