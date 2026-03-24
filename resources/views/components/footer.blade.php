<footer class="footer border-top">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-4 text-center text-md-start">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </div>
            <div class="col-md-4 text-center">
                {{ trans('lang.version') }} {{ config('app.version', '1.0.0') }}
            </div>
            <div class="col-md-4 text-center text-md-end">
                {{ trans('lang.developed_by') }} <strong class="text-danger">Car &amp; General</strong>
            </div>
        </div>
    </div>
</footer>
