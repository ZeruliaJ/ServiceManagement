<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr" loader="enable">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }} : {{ isset($title) ? $title : '' }}</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; overflow: hidden; height: 100vh; }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ── Left Panel ── */
        .login-left {
            flex: 6;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 75%, #c23152 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 50px 80px;
            position: relative;
            overflow: hidden;
        }

        /* Dot grid overlay */
        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.07) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        /* Radial glow accents */
        .login-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle 280px at 15% 85%, rgba(233,69,96,0.18) 0%, transparent 100%),
                radial-gradient(circle 200px at 85% 10%, rgba(255,255,255,0.04) 0%, transparent 100%);
            pointer-events: none;
        }

        /* Bokeh circles */
        .bokeh-circle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }
        .bokeh-1 { width: 200px; height: 200px; background: rgba(255,255,255,0.05); top: -70px; left: -50px; filter: blur(25px); }
        .bokeh-2 { width: 130px; height: 130px; background: rgba(255,255,255,0.04); top: 40px; right: 8%; filter: blur(18px); }
        .bokeh-3 { width: 220px; height: 220px; background: rgba(233,69,96,0.12); bottom: -70px; right: -50px; filter: blur(35px); }
        .bokeh-4 { width: 110px; height: 110px; background: rgba(255,255,255,0.04); bottom: 22%; left: 4%; filter: blur(22px); }

        .login-left-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 480px;
        }

        .left-headline {
            font-size: 2.6rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.18;
            letter-spacing: -0.5px;
            margin-bottom: 14px;
        }
        .left-headline span { color: #e94560; }
        .left-headline .cursor {
            display: inline-block;
            width: 3px; height: 2.4rem;
            background: #e94560;
            margin-left: 3px;
            vertical-align: middle;
            animation: blink 1s step-end infinite;
        }
        @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0; } }

        .left-subtext {
            font-size: 0.93rem;
            color: rgba(255,255,255,0.58);
            line-height: 1.65;
            margin-bottom: 48px;
        }

        /* Feature cards */
        .feature-cards { display: flex; flex-direction: column; gap: 14px; }

        .feature-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 16px 20px;
            backdrop-filter: blur(6px);
            transition: background 0.2s;
        }
        .feature-card:hover { background: rgba(255,255,255,0.11); }

        .feature-icon {
            width: 42px; height: 42px;
            background: rgba(233,69,96,0.18);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon i { font-size: 20px; color: #e94560; }
        .feature-title { font-size: 0.9rem; font-weight: 700; color: #fff; margin-bottom: 3px; }
        .feature-desc  { font-size: 0.77rem; color: rgba(255,255,255,0.48); }

        /* Left footer */
        .left-footer {
            position: absolute;
            bottom: 22px; left: 0; right: 0;
            text-align: center;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
            z-index: 2;
        }

        /* ── Right Panel — Pure White ── */
        .login-right {
            flex: 4;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }

        /* Login form area */
        .login-card {
            width: 100%;
            max-width: 360px;
            padding: 0 40px;
            position: relative;
            z-index: 2;
        }

        /* Header */
        .lc-header { text-align: center; margin-bottom: 28px; }

        .lc-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .lc-logo img { max-height: 76px; max-width: 180px; object-fit: contain; }
        .lc-logo-fallback {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #1a1a2e, #0f3460);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
        }
        .lc-logo-fallback i { font-size: 32px; color: #e94560; }

        .lc-tagline {
            font-size: 0.82rem;
            font-style: italic;
            color: #9ca3af;
            margin-bottom: 12px;
        }
        .lc-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1a1a2e;
        }

        /* Error alert */
        .lc-alert {
            background: #fff5f7;
            border: 1px solid #fbd0d8;
            border-left: 4px solid #e94560;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
            display: flex; align-items: flex-start; gap: 8px;
        }
        .lc-alert i { color: #e94560; font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .lc-alert ul { margin: 0; padding: 0; list-style: none; }
        .lc-alert ul li { font-size: 0.82rem; color: #6b2030; }

        /* Input groups */
        .lc-input-group { margin-bottom: 16px; }

        .lc-input-group label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .lc-input-wrap { position: relative; }

        .lc-input-wrap .lc-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #c4cad8;
            font-size: 17px;
            pointer-events: none;
        }

        .lc-input-wrap input {
            width: 100%;
            padding: 12px 42px 12px 42px;
            border: 1.5px solid #e8ecf4;
            border-radius: 10px;
            font-size: 0.9rem;
            color: #1a1a2e;
            background: #f9fafc;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .lc-input-wrap input::placeholder { color: #b8bfcc; }
        .lc-input-wrap input:focus {
            border-color: #e94560;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(233,69,96,0.07);
        }

        .lc-toggle {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #c4cad8; cursor: pointer;
            font-size: 17px; padding: 0;
            transition: color 0.2s;
        }
        .lc-toggle:hover { color: #e94560; }

        /* Forgot row */
        .lc-forgot-row {
            text-align: right;
            margin-top: 6px;
            margin-bottom: 22px;
        }
        .lc-forgot {
            font-size: 0.82rem;
            color: #e94560;
            text-decoration: none;
            font-weight: 500;
        }
        .lc-forgot:hover { opacity: 0.8; }

        /* Button */
        .btn-lc-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #e94560;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: all 0.25s ease;
        }
        .btn-lc-login:hover {
            background: #d63652;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(233,69,96,0.35);
        }
        .btn-lc-login:active { transform: translateY(0); }
        .btn-lc-login:disabled { opacity: 0.6; cursor: not-allowed; transform: none !important; }

        /* Footer */
        .lc-footer { text-align: center; margin-top: 26px; }
        .lc-footer .version { font-size: 0.74rem; color: #c4cad8; }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            body { overflow: auto; height: auto; }
            .login-wrapper { flex-direction: column; min-height: 100vh; }
            .login-left { flex: none; padding: 44px 24px 56px; }
            .left-headline { font-size: 1.9rem; }
            .login-right { flex: 1; min-height: 60vh; padding: 40px 16px; }
            .login-card { max-width: 380px; padding: 0 20px; }
        }
        @media (max-width: 480px) {
            .login-left { padding: 32px 18px 52px; }
        }
    </style>
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

    {{ $slot }}

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/loader.js') }}"></script>
    @stack('scripts')
</body>
</html>
