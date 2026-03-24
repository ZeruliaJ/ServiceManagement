$(function () {

    // ─── Approve ────────────────────────────────────────────────────
    $('#btn-approve-rework').on('click', function () {
        Swal.fire({
            title: Lang.approve_rework_confirm,
            text: Lang.approve_rework_text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: Lang.approve,
            cancelButtonText: Lang.close
        }).then(function (result) {
            if (result.isConfirmed) {
                $('#approve-form').submit();
            }
        });
    });

    // ─── Reject ─────────────────────────────────────────────────────
    $('#btn-reject-rework').on('click', function () {
        Swal.fire({
            title: Lang.reject_rework_confirm,
            input: 'textarea',
            inputLabel: Lang.rejection_reason,
            inputPlaceholder: Lang.rejection_reason_placeholder,
            inputAttributes: { 'aria-label': Lang.rejection_reason },
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: Lang.reject,
            cancelButtonText: Lang.close,
            inputValidator: function (value) {
                if (!value || !value.trim()) {
                    return Lang.rejection_reason_required;
                }
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                $('#rejection-reason-input').val(result.value);
                $('#reject-form').submit();
            }
        });
    });

    // ─── Image Preview Modal ────────────────────────────────────────
    $(document).on('click', '.preview-img', function () {
        var src = $(this).attr('src');
        $('#imagePreviewSrc').attr('src', src);
        new bootstrap.Modal($('#imagePreviewModal')[0]).show();
    });

});
