<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" loader="enable" data-nav-layout="horizontal" data-nav-style="menu-click">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} : {{ isset($title) ? $title : '' }}</title>
        <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
        <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>
        <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/select2/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/responsive.bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/kyc.css') }}">
        @stack('css')
    </head>
    <body>
        <div id="loader">
            <div class="dimmer active">
                <div class="spinner4">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </div>
        </div>

        <div class="page">
            <x-header></x-header>
            <x-sidebar></x-sidebar>
            <div class="main-content app-content mb-3rem">
                {{ $slot }}
            </div>
            <x-footer></x-footer>
        </div>

    <script>
        window.Lang = {
            are_you_sure: {!! json_encode(trans('lang.are_you_sure')) !!},
            wont_be_able_to_revert: {!! json_encode(trans('lang.wont_be_able_to_revert')) !!},
            yes_delete_it: {!! json_encode(trans('lang.yes_delete_it')) !!},
            deleted: {!! json_encode(trans('lang.deleted')) !!},
            record_deleted_successfully: {!! json_encode(trans('lang.record_deleted_successfully')) !!},
            oops: {!! json_encode(trans('lang.oops')) !!},
            something_went_wrong: {!! json_encode(trans('lang.something_went_wrong')) !!},
            want_to_change_status: {!! json_encode(trans('lang.want_to_change_status')) !!},
            yes_change_it: {!! json_encode(trans('lang.yes_change_it')) !!},
            changed: {!! json_encode(trans('lang.changed')) !!},
            status_changed_successfully: {!! json_encode(trans('lang.status_changed_successfully')) !!},

            field_required: {!! json_encode(trans('lang.field_required')) !!},
            email_invalid: {!! json_encode(trans('lang.email_invalid')) !!},

            dt_empty_table: {!! json_encode(trans('lang.dt_empty_table')) !!},
            dt_info: {!! json_encode(trans('lang.dt_info')) !!},
            dt_info_empty: {!! json_encode(trans('lang.dt_info_empty')) !!},
            dt_info_filtered: {!! json_encode(trans('lang.dt_info_filtered')) !!},
            dt_length_menu: {!! json_encode(trans('lang.dt_length_menu')) !!},
            dt_loading_records: {!! json_encode(trans('lang.dt_loading_records')) !!},
            dt_processing: {!! json_encode(trans('lang.dt_processing')) !!},
            dt_search: {!! json_encode(trans('lang.dt_search')) !!},
            dt_search_placeholder: {!! json_encode(trans('lang.dt_search_placeholder')) !!},
            dt_zero_records: {!! json_encode(trans('lang.dt_zero_records')) !!},
            dt_first: {!! json_encode(trans('lang.dt_first')) !!},
            dt_last: {!! json_encode(trans('lang.dt_last')) !!},
            dt_next: {!! json_encode(trans('lang.dt_next')) !!},
            dt_previous: {!! json_encode(trans('lang.dt_previous')) !!},
            dt_sort_ascending: {!! json_encode(trans('lang.dt_sort_ascending')) !!},
            dt_sort_descending: {!! json_encode(trans('lang.dt_sort_descending')) !!},
        };
    </script>
    <script src="{{ asset('assets/core/core.js') }}"></script>
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/simplebar.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>
    <script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.js') }}"></script>
    <script src="{{ asset('assets/js/loader.js') }}"></script>
    <script src="{{ asset('assets/js/general-layout.js') }}"></script>
    @stack('scripts')
    </body>
</html>
