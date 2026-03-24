<header class="app-header">
    <div class="main-header-container container-fluid">

        <div class="header-content-left">
            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="{{ route('dashboard') }}" class="header-logo d-flex align-items-center gap-2" style="text-decoration: none;">
                        <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" class="desktop-logo" style="height: 40px;">
                        <div style="display: flex; flex-direction: column; line-height: 1.2;">
                            <span style="font-weight: 800; font-size: 0.75rem; color: #c0172b;">CAR and GENERAL</span>
                            <span style="font-size: 0.65rem; color: #666;">Power for better living</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="header-content-center d-flex align-items-center">
        </div>

        <style>
            .header-user-dropdown:hover { color: #333 !important; }
            .header-user-dropdown + .dropdown-menu { border: 1px solid rgba(0,0,0,0.08); border-radius: 10px; box-shadow: 0 6px 24px rgba(0,0,0,0.12); padding: 0; overflow: hidden; }
            .header-user-dropdown + .dropdown-menu .dropdown-item:hover { background: #f8f9fa; }
            .header-user-dropdown + .dropdown-menu .dropdown-item.text-danger:hover { background: #fff5f5; }
            @media (max-width: 991.98px) {
                .header-title { font-size: 0.6rem !important; letter-spacing: 0.5px !important; line-height: 1.3; }
                .header-content-right .language-switcher { display: none !important; }
                .horizontal-logo { display: none !important; }
            }
        </style>

        <div class="header-content-right">
            {{-- Language Switcher --}}
            <div class="language-switcher d-none d-lg-flex" data-current-locale="{{ app()->getLocale() }}">
                <a href="{{ route('set-locale','sw') }}" class="lang-toggle {{ app()->getLocale() == 'sw' ? 'active' : '' }}">
                    <span class="lang-text">{{ trans('lang.language_swahili') }}</span>
                </a>
                <a href="{{ route('set-locale','en') }}" class="lang-toggle {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                    <span class="lang-text">{{ trans('lang.language_english') }}</span>
                </a>
                <div class="lang-slider" style="transform: translateX({{ app()->getLocale() == 'en' ? '100%' : '0%' }});"></div>
            </div>

            {{-- Notification Bell --}}
            <div class="header-element">
                <a href="javascript:void(0);" class="header-link" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false" title="{{ trans('lang.notifications') }}" style="position:relative;">
                    <i class="bx bx-bell header-link-icon"></i>
                    <span class="badge bg-danger rounded-pill d-none" id="notificationCount" style="position:absolute;top:2px;right:2px;font-size:0.55rem;padding:2px 5px;min-width:18px;line-height:1.2;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" style="width:320px;max-height:400px;overflow-y:auto;" id="notificationDropdown">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <h6 class="mb-0 fw-semibold">{{ trans('lang.notifications') }}</h6>
                        <a href="javascript:void(0);" id="markAllRead" class="text-primary small">{{ trans('lang.mark_all_read') }}</a>
                    </div>
                    <div id="notificationList">
                        <div class="text-center text-muted py-3 small">{{ trans('lang.no_notifications') }}</div>
                    </div>
                </div>
            </div>

            {{-- User Dropdown --}}
            <div class="header-element d-none d-lg-flex align-items-center">
                <div class="dropdown">
                    <a href="javascript:void(0);" class="d-inline-flex align-items-center text-muted header-user-dropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:0.8rem; cursor:pointer; text-decoration:none;">
                        <i class="bx bx-user-circle" style="font-size:1.3rem; margin-inline-end:5px;"></i>
                        {{ auth()->user()->name }}
                        <i class="bx bx-chevron-down" style="font-size:1rem; margin-inline-start:4px; opacity:0.6;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:180px;">
                        <li>
                            <div class="dropdown-item-text px-3 py-2 border-bottom">
                                <div class="fw-semibold" style="font-size:0.85rem;">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size:0.72rem;">{{ auth()->user()->email }}</div>
                            </div>
                        </li>
                        <li>
                            <a href="javascript:void(0);" id="logoutBtn" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 text-danger">
                                <i class="bx bx-log-out" style="font-size:1.1rem;"></i>
                                <span class="logout-text">{{ trans('lang.logout') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>

    </div>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</header>

{{-- Notification Toast Container --}}
<style>
    #toast-container{position:fixed;top:70px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;}
    .app-toast{pointer-events:auto;display:flex;align-items:flex-start;gap:12px;min-width:320px;max-width:400px;padding:14px 16px;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.15),0 2px 8px rgba(0,0,0,.08);border-left:4px solid #4361ee;cursor:pointer;transform:translateX(120%);opacity:0;transition:transform .4s cubic-bezier(.34,1.56,.64,1),opacity .3s ease;}
    .app-toast.show{transform:translateX(0);opacity:1;}
    .app-toast.hide{transform:translateX(120%);opacity:0;}
    .app-toast:hover{box-shadow:0 10px 40px rgba(0,0,0,.2),0 4px 12px rgba(0,0,0,.1);border-left-color:#3a0ca3;}
    .app-toast-icon{flex-shrink:0;width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#4361ee,#3a0ca3);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;}
    .app-toast-body{flex:1;min-width:0;}
    .app-toast-title{font-size:.75rem;font-weight:700;color:#4361ee;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;}
    .app-toast-msg{font-size:.85rem;color:#333;font-weight:500;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
    .app-toast-time{font-size:.68rem;color:#999;margin-top:3px;}
    .app-toast-close{flex-shrink:0;background:none;border:none;color:#aaa;font-size:1.1rem;cursor:pointer;padding:0;line-height:1;transition:color .2s;}
    .app-toast-close:hover{color:#333;}
    .app-toast-progress{position:absolute;bottom:0;left:4px;right:0;height:3px;background:rgba(67,97,238,.15);border-radius:0 0 12px 0;overflow:hidden;}
    .app-toast-progress-bar{height:100%;background:linear-gradient(90deg,#4361ee,#3a0ca3);border-radius:3px;animation:toast-countdown 10s linear forwards;}
    @keyframes toast-countdown{from{width:100%}to{width:0%}}
</style>
<div id="toast-container"></div>

@push('scripts')
<script>
$(function () {
    var $logoutBtn  = $('#logoutBtn');
    var $logoutForm = $('#logoutForm');

    if ($logoutBtn.length && $logoutForm.length) {
        $logoutBtn.on('click', function (e) {
            e.preventDefault();
            $logoutBtn.css({ 'pointer-events': 'none', 'opacity': '0.6' });
            $logoutBtn.find('.logout-text').text({!! json_encode(trans('lang.logging_out')) !!});
            $logoutForm.trigger('submit');
        });
    }

    // ─── Notification helpers ─────────────────────────────────────
    var NOTIFIED_KEY = 'app_notified_ids';

    function getNotifiedIds() {
        try { return JSON.parse(localStorage.getItem(NOTIFIED_KEY) || '[]'); } catch (e) { return []; }
    }
    function saveNotifiedIds(ids) {
        localStorage.setItem(NOTIFIED_KEY, JSON.stringify(ids.slice(-100)));
    }

    function playBellSound() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            var osc1 = ctx.createOscillator(), gain1 = ctx.createGain();
            osc1.type = 'sine'; osc1.frequency.setValueAtTime(830, ctx.currentTime);
            gain1.gain.setValueAtTime(0.3, ctx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
            osc1.connect(gain1); gain1.connect(ctx.destination);
            osc1.start(ctx.currentTime); osc1.stop(ctx.currentTime + 0.6);
            var osc2 = ctx.createOscillator(), gain2 = ctx.createGain();
            osc2.type = 'sine'; osc2.frequency.setValueAtTime(620, ctx.currentTime + 0.15);
            gain2.gain.setValueAtTime(0.001, ctx.currentTime); gain2.gain.setValueAtTime(0.2, ctx.currentTime + 0.15);
            gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.8);
            osc2.connect(gain2); gain2.connect(ctx.destination);
            osc2.start(ctx.currentTime + 0.15); osc2.stop(ctx.currentTime + 0.8);
        } catch (e) {}
    }

    function showToast(nId, message, url) {
        var $container = $('#toast-container');
        if ($container.find('.app-toast').length >= 3) $container.find('.app-toast').first().remove();
        var now = new Date();
        var timeStr = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
        var $toast = $('<div class="app-toast" style="position:relative;">' +
            '<div class="app-toast-icon"><i class="bx bx-bell bx-tada"></i></div>' +
            '<div class="app-toast-body">' +
                '<div class="app-toast-title">' + {!! json_encode(trans('lang.new_notification')) !!} + '</div>' +
                '<div class="app-toast-msg">' + $('<span>').text(message).html() + '</div>' +
                '<div class="app-toast-time"><i class="bx bx-time-five"></i> ' + timeStr + '</div>' +
            '</div>' +
            '<button class="app-toast-close">&times;</button>' +
            '<div class="app-toast-progress"><div class="app-toast-progress-bar"></div></div>' +
        '</div>');
        $container.append($toast);
        setTimeout(function () { $toast.addClass('show'); }, 50);
        $toast.on('click', function (e) {
            if ($(e.target).hasClass('app-toast-close')) return;
            $.ajax({ url: "{{ url('notifications') }}/" + nId + "/read", method: 'PATCH', headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            if (url) window.location.href = url;
        });
        $toast.find('.app-toast-close').on('click', function (e) { e.stopPropagation(); dismiss($toast); });
        var autoTimer = setTimeout(function () { dismiss($toast); }, 10000);
        function dismiss($el) { clearTimeout(autoTimer); $el.removeClass('show').addClass('hide'); setTimeout(function () { $el.remove(); }, 400); }
    }

    var appBase = "{{ url('/') }}";
    function resolveUrl(raw) {
        if (!raw || raw === 'javascript:void(0);') return raw;
        if (raw.indexOf('http') === 0) return raw;
        return appBase + (raw.charAt(0) === '/' ? '' : '/') + raw;
    }

    function fetchNotifications() {
        $.get("{{ route('notifications.index') }}", function (data) {
            var $count = $('#notificationCount');
            var $list  = $('#notificationList');

            data.unread_count > 0 ? $count.text(data.unread_count).removeClass('d-none') : $count.addClass('d-none');

            var notifiedIds = getNotifiedIds();
            var newOnes = data.notifications.filter(function (n) { return !n.read_at && notifiedIds.indexOf(n.id) === -1; });
            if (newOnes.length > 0) {
                playBellSound();
                var latest = newOnes[0];
                showToast(latest.id, (latest.data && latest.data.message) ? latest.data.message : {!! json_encode(trans('lang.new_notification')) !!}, (latest.data && latest.data.url) ? resolveUrl(latest.data.url) : null);
                newOnes.forEach(function (n) { notifiedIds.push(n.id); });
                saveNotifiedIds(notifiedIds);
            }

            if (data.notifications.length === 0) {
                $list.html('<div class="text-center text-muted py-3 small">' + {!! json_encode(trans('lang.no_notifications')) !!} + '</div>');
                return;
            }

            var html = '';
            data.notifications.forEach(function (n) {
                var url = (n.data && n.data.url) ? resolveUrl(n.data.url) : 'javascript:void(0);';
                var msg = (n.data && n.data.message) ? n.data.message : '';
                html += '<a href="' + url + '" class="dropdown-item notification-item d-block px-3 py-2 border-bottom ' + (n.read_at ? '' : 'bg-light') + '" data-id="' + n.id + '">' +
                    '<div class="small fw-semibold">' + $('<span>').text(msg).html() + '</div>' +
                    '<div class="text-muted" style="font-size:0.7rem;">' + n.created_at + '</div>' +
                    '</a>';
            });
            $list.html(html);
        });
    }

    fetchNotifications();
    setInterval(fetchNotifications, 30000);

    $(document).on('click', '.notification-item', function () {
        $.ajax({ url: "{{ url('notifications') }}/" + $(this).data('id') + "/read", method: 'PATCH', headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    });

    $('#markAllRead').on('click', function () {
        $.ajax({ url: "{{ route('notifications.mark-all-read') }}", method: 'PATCH', headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, success: function () { fetchNotifications(); } });
    });
});
</script>
@endpush
