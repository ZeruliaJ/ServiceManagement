<x-guest-layout :title="$title">
    <div class="login-wrapper">

        <!-- Left Panel -->
        <div class="login-left">
            <div class="bokeh-circle bokeh-1"></div>
            <div class="bokeh-circle bokeh-2"></div>
            <div class="bokeh-circle bokeh-3"></div>
            <div class="bokeh-circle bokeh-4"></div>

            <div class="login-left-content">

                <h2 class="left-headline">
                    {{ trans('lang.login_headline_1') }}<br>
                    <span>{{ trans('lang.login_headline_2') }}</span><span class="cursor"></span>
                </h2>
                <p class="left-subtext">{{ trans('lang.login_subtext') }}</p>

                <div class="feature-cards">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bx bx-shield-quarter"></i></div>
                        <div>
                            <div class="feature-title">{{ trans('lang.feature_1_title') }}</div>
                            <div class="feature-desc">{{ trans('lang.feature_1_desc') }}</div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bx bx-git-branch"></i></div>
                        <div>
                            <div class="feature-title">{{ trans('lang.feature_2_title') }}</div>
                            <div class="feature-desc">{{ trans('lang.feature_2_desc') }}</div>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bx bx-time-five"></i></div>
                        <div>
                            <div class="feature-title">{{ trans('lang.feature_3_title') }}</div>
                            <div class="feature-desc">{{ trans('lang.feature_3_desc') }}</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="left-footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </div>
        </div>

        <!-- Right Panel -->
        <div class="login-right">
            <div class="login-card">

                <div class="lc-header">
                    <div class="lc-logo">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('app.name') }}"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="lc-logo-fallback" style="display:none">
                            <i class="bx bx-shield-quarter"></i>
                        </div>
                    </div>
                    <div class="lc-tagline">{{ trans('lang.app_tagline') }}</div>
                    <div class="lc-title">{{ trans('lang.sign_in_to', ['app' => config('app.name')]) }}</div>
                </div>

                @if ($errors->any())
                    <div class="lc-alert">
                        <i class="bx bx-error-circle"></i>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="lc-input-group">
                        <label>{{ trans('lang.email') }}</label>
                        <div class="lc-input-wrap">
                            <i class="bx bx-user lc-icon"></i>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="{{ trans('lang.enter_email') }}" required autocomplete="off">
                        </div>
                    </div>

                    <div class="lc-input-group">
                        <label>{{ trans('lang.password') }}</label>
                        <div class="lc-input-wrap">
                            <i class="bx bx-lock lc-icon"></i>
                            <input type="password" name="password" id="loginPassword"
                                placeholder="{{ trans('lang.enter_password') }}" required autocomplete="off">
                            <button type="button" class="lc-toggle" id="togglePassword">
                                <i class="bx bx-hide"></i>
                            </button>
                        </div>
                    </div>

                    <div class="lc-forgot-row">
                        <a href="#" class="lc-forgot">{{ trans('lang.forgot_password') }}</a>
                    </div>

                    <button type="submit" class="btn-lc-login" id="loginBtn">
                        <i class="bx bx-right-arrow-alt" style="font-size:20px;"></i>
                        <span class="login-text">{{ trans('lang.log_in') }}</span>
                    </button>
                </form>

                <div class="lc-footer">
                    <div class="version">{{ trans('lang.version') }} {{ config('app.version', '1.0.0') }}</div>
                </div>

            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('togglePassword');
            var passInput = document.getElementById('loginPassword');
            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', function () {
                    var type = passInput.type === 'password' ? 'text' : 'password';
                    passInput.type = type;
                    toggleBtn.querySelector('i').className = type === 'password' ? 'bx bx-hide' : 'bx bx-show';
                });
            }

            var loginBtn  = document.getElementById('loginBtn');
            var loginForm = document.getElementById('loginForm');
            if (loginBtn && loginForm) {
                loginForm.addEventListener('submit', function () {
                    loginBtn.disabled = true;
                    var text = loginBtn.querySelector('.login-text');
                    if (text) {
                        text.innerHTML = '<i class="bx bx-loader-alt" style="animation:spin 1s linear infinite;display:inline-block;margin-right:6px;"></i>{!! trans("lang.signing_in") !!}';
                    }
                });
            }
        });
    </script>
    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
    @endpush
</x-guest-layout>
