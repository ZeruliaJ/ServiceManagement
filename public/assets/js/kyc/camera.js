let stream = null;
let photos = [];
let existingCount = existingPhotos.length;
const MAX_PHOTOS = 5;

$(document).ready(function() {
    const openBtn = document.getElementById("openCamera");
    const cameraContainer = document.getElementById("cameraContainer");
    const thumbnails = document.getElementById("thumbnails");
    const photoInput = document.getElementById("photo_paths");
    const emptyState = document.getElementById("emptyPhotosState");
    const photoFileInput = document.getElementById("photoFileInput");

    // Load existing photos
    existingPhotos.forEach(photo => {
        addThumbnail(photo.id, photo.image, true);
    });

    updateUI();

    // Toggle between upload and camera modes
    $(document).on('click', '.photo-mode-btn', function() {
        $('.photo-mode-btn').removeClass('active');
        $(this).addClass('active');

        const mode = $(this).data('mode');
        if (mode === 'upload') {
            $('#photoUploadSection').show();
            $('#photoCameraSection').hide();
            stopCamera();
        } else {
            $('#photoUploadSection').hide();
            $('#photoCameraSection').show();
        }
    });

    // File upload handler
    $(document).on('change', '#photoFileInput', function(e) {
        const files = Array.from(e.target.files);
        const remainingSlots = MAX_PHOTOS - totalPhotosCount();

        if (files.length > remainingSlots) {
            Swal.fire({
                icon: 'warning',
                title: window.Lang.photo_limit_warning || 'Limit Exceeded',
                text: `${window.Lang.you_can_only_add || 'You can only add'} ${remainingSlots} ${window.Lang.more_photos || 'more photo(s)'}`,
                confirmButtonColor: 'rgb(var(--primary-theme-rgb))'
            });
        }

        const filesToProcess = files.slice(0, remainingSlots);

        filesToProcess.forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const imgData = event.target.result;
                    photos.push(imgData);
                    photoInput.value = JSON.stringify(photos);
                    addThumbnail(imgData, null, false);
                    updateUI();
                };
                reader.readAsDataURL(file);
            }
        });

        // Reset input
        this.value = '';
    });

    // Camera button handler
    $(document).on('click', '#openCamera', async function() {
        if (!navigator.mediaDevices?.getUserMedia) {
            Swal.fire({
                icon: 'error',
                title: window.Lang.camera_not_supported_title || 'Camera Not Supported',
                text: window.Lang.camera_not_supported_message || 'Your browser does not support camera access',
                confirmButtonColor: 'rgb(var(--primary-theme-rgb))'
            });
            return;
        }

        if (stream) return;

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });

            const cameraContainer = document.getElementById("cameraContainer");
            $(cameraContainer).show();
            $('#openCamera').hide();

            const video = document.createElement("video");
            video.srcObject = stream;
            video.autoplay = true;
            video.playsInline = true;

            const controls = document.createElement("div");
            controls.className = "camera-controls";

            const cancelBtn = document.createElement("button");
            cancelBtn.type = "button";
            cancelBtn.className = "camera-cancel-btn";
            cancelBtn.innerHTML = '<i class="bx bx-x"></i> ' + (window.Lang.close || 'Close');
            cancelBtn.onclick = () => stopCamera();

            const captureBtn = document.createElement("button");
            captureBtn.type = "button";
            captureBtn.className = "camera-capture-btn";
            captureBtn.innerHTML = '<i class="bx bx-camera"></i>';
            captureBtn.onclick = () => capturePhoto(video);

            controls.append(cancelBtn, captureBtn);
            cameraContainer.innerHTML = '';
            cameraContainer.append(video, controls);

        } catch (error) {
            console.error('Camera error:', error);
            Swal.fire({
                icon: 'error',
                title: window.Lang.camera_access_denied_title || 'Camera Access Denied',
                text: window.Lang.camera_access_denied_message || 'Please allow camera access to capture photos',
                confirmButtonColor: 'rgb(var(--primary-theme-rgb))'
            });
        }
    });

    function capturePhoto(video) {
        if (totalPhotosCount() >= MAX_PHOTOS) {
            Swal.fire({
                icon: 'warning',
                title: window.Lang.photo_limit_reached || 'Limit Reached',
                text: `${MAX_PHOTOS} photos maximum`,
                confirmButtonColor: 'rgb(var(--primary-theme-rgb))'
            });
            return;
        }

        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0);

        const imgData = canvas.toDataURL("image/png");

        photos.push(imgData);
        photoInput.value = JSON.stringify(photos);

        addThumbnail(imgData, null, false);
        updateUI();

        if (totalPhotosCount() >= MAX_PHOTOS) {
            stopCamera();
        }
    }

    function totalPhotosCount() {
        return existingCount + photos.length;
    }

    function addThumbnail(idOrSrc, src = null, isExisting = false) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const card = document.createElement("div");
        card.className = "photo-thumbnail-card";

        const img = document.createElement("img");
        img.src = isExisting ? src : idOrSrc;
        img.onclick = () => window.open(img.src, '_blank');

        const badge = document.createElement("span");
        badge.className = isExisting ? "photo-badge badge-existing" : "photo-badge badge-new";
        badge.textContent = isExisting ? (window.Lang.saved || 'Saved') : (window.Lang.new || 'New');

        const removeBtn = document.createElement("button");
        removeBtn.className = "photo-remove-btn";
        removeBtn.innerHTML = '<i class="bx bx-x"></i>';
        removeBtn.type = "button";

        removeBtn.onclick = () => {
            if (isExisting) {
                Swal.fire({
                    title: window.Lang.delete_photo_title || 'Delete Photo?',
                    text: window.Lang.delete_photo_message || 'This action cannot be undone',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: window.Lang.yes_delete_it || 'Yes, delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('/kyc/photo-delete', {
                            id: idOrSrc,
                            _token: token
                        }).then(() => {
                            card.remove();
                            existingCount--;
                            updateUI();
                        });
                    }
                });
            } else {
                photos = photos.filter(p => p !== idOrSrc);
                photoInput.value = JSON.stringify(photos);
                card.remove();
                updateUI();
            }
        };

        card.append(img, badge, removeBtn);
        thumbnails.append(card);
    }

    function updateUI() {
        const total = totalPhotosCount();

        const counterText = document.getElementById('photoCountText');
        if (counterText) {
            counterText.textContent = `${total} / ${MAX_PHOTOS} Photos`;
        }

        const counter = document.getElementById('photoCounter');
        if (counter) {
            if (total >= 3) {
                counter.style.background = 'rgba(16, 185, 129, 0.15)';
                counter.style.color = '#10b981';
            } else {
                counter.style.background = 'rgba(var(--primary-theme-rgb), 0.1)';
                counter.style.color = 'rgb(var(--primary-theme-rgb))';
            }
        }

        if (emptyState) {
            $(emptyState).toggle(total === 0);
        }

        // Hide upload options if max reached
        if (total >= MAX_PHOTOS) {
            $('#photoUploadSection').hide();
            $('#photoCameraSection').hide();
            $('.photo-upload-options .btn-group').hide();
            stopCamera();
        } else {
            $('.photo-upload-options .btn-group').show();
            const activeMode = $('.photo-mode-btn.active').data('mode');
            if (activeMode === 'upload') {
                $('#photoUploadSection').show();
                $('#photoCameraSection').hide();
            } else {
                $('#photoUploadSection').hide();
                $('#photoCameraSection').show();
            }
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        const cameraContainer = document.getElementById("cameraContainer");
        if (cameraContainer) {
            cameraContainer.innerHTML = "";
            $(cameraContainer).hide();
        }

        if (totalPhotosCount() < MAX_PHOTOS) {
            $('#openCamera').show();
        }
    }

    // Make functions available globally for onclick handlers
    window.stopCamera = stopCamera;
});
