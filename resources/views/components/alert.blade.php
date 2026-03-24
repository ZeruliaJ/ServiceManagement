@if (session('success'))
    <div class="kyc-alert kyc-alert-success" role="alert">
        <div class="kyc-alert-icon"><i class="bx bx-check-circle"></i></div>
        <div class="kyc-alert-content">
            <span class="kyc-alert-title">{{ trans('lang.alert_success') }}</span>
            <span class="kyc-alert-message">{{ session('success') }}</span>
        </div>
        <button type="button" class="kyc-alert-close" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
    </div>
@endif

@if (session('error'))
    <div class="kyc-alert kyc-alert-error" role="alert">
        <div class="kyc-alert-icon"><i class="bx bx-x-circle"></i></div>
        <div class="kyc-alert-content">
            <span class="kyc-alert-title">{{ trans('lang.alert_error') }}</span>
            <span class="kyc-alert-message">{{ session('error') }}</span>
        </div>
        <button type="button" class="kyc-alert-close" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
    </div>
@endif

@if (session('warning'))
    <div class="kyc-alert kyc-alert-warning" role="alert">
        <div class="kyc-alert-icon"><i class="bx bx-error"></i></div>
        <div class="kyc-alert-content">
            <span class="kyc-alert-title">{{ trans('lang.alert_warning') }}</span>
            <span class="kyc-alert-message">{{ session('warning') }}</span>
        </div>
        <button type="button" class="kyc-alert-close" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
    </div>
@endif

@if (session('info'))
    <div class="kyc-alert kyc-alert-info" role="alert">
        <div class="kyc-alert-icon"><i class="bx bx-info-circle"></i></div>
        <div class="kyc-alert-content">
            <span class="kyc-alert-title">{{ trans('lang.alert_info') }}</span>
            <span class="kyc-alert-message">{{ session('info') }}</span>
        </div>
        <button type="button" class="kyc-alert-close" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
    </div>
@endif

@if(session('success') || session('error') || session('warning') || session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.querySelectorAll('.kyc-alert').forEach(function (el) {
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                setTimeout(function () { el.remove(); }, 500);
            });
        }, 5000);
    });
</script>
@endif
