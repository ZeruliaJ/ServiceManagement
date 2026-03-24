<x-app-layout :title="$title">

    @push('css')
    <style>
        .dash-hero {
            background: linear-gradient(to right, #1e2d6b 0%, #4a1a5c 45%, #c0172b 100%);
            border-radius: 12px;
            padding: 28px 32px;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        /* "+" cross grid pattern */
        .dash-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.12) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(ellipse at 80% 50%, black 20%, transparent 75%);
            -webkit-mask-image: radial-gradient(ellipse at 80% 50%, black 20%, transparent 75%);
            pointer-events: none;
        }

        .dash-hero-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .dash-hero-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 8px;
            line-height: 1.2;
        }

        .dash-hero-sub {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.72);
            margin: 0;
            font-weight: 500;
        }
    </style>
    @endpush

    <div class="container-fluid">

        <x-alert />

        {{-- Hero Banner --}}
        <div class="dash-hero">
            <div class="dash-hero-label">{{ trans('lang.dashboard') }}</div>
            <h2 class="dash-hero-title">{{ trans('lang.dashboard_title') }}</h2>
            <p class="dash-hero-sub">{{ trans('lang.dashboard_subtitle') }}</p>
        </div>

        {{-- ============================================================
             STAT CARDS — add your variables here when ready
             e.g. replace "—" with {{ $totalRecords }}
        ============================================================ --}}
        <div class="row g-2">

            <div class="col-xl-3 col-md-6">
                <div class="card custom-card h-100" style="border-top:3px solid #273d80;">
                    <div class="card-body" style="padding:.85rem .9rem;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:46px;height:46px;background:rgba(39,61,128,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bx bx-layer" style="font-size:1.4rem;color:#273d80;"></i>
                            </div>
                            <div style="min-width:0;flex:1;">
                                <div style="font-size:1.5rem;font-weight:800;color:#1e2a4a;line-height:1;">—</div>
                                <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">{{ trans('lang.card_1_label') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card custom-card h-100" style="border-top:3px solid #e94560;">
                    <div class="card-body" style="padding:.85rem .9rem;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:46px;height:46px;background:rgba(233,69,96,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bx bx-group" style="font-size:1.4rem;color:#e94560;"></i>
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:1.5rem;font-weight:800;color:#1e2a4a;line-height:1;">—</div>
                                <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">{{ trans('lang.card_2_label') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card custom-card h-100" style="border-top:3px solid #22c55e;">
                    <div class="card-body" style="padding:.85rem .9rem;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:46px;height:46px;background:rgba(34,197,94,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bx bx-trending-up" style="font-size:1.4rem;color:#22c55e;"></i>
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:1.5rem;font-weight:800;color:#1e2a4a;line-height:1;">—</div>
                                <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">{{ trans('lang.card_3_label') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card custom-card h-100" style="border-top:3px solid #f59e0b;">
                    <div class="card-body" style="padding:.85rem .9rem;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:46px;height:46px;background:rgba(245,158,11,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bx bx-bar-chart-alt-2" style="font-size:1.4rem;color:#f59e0b;"></i>
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:1.5rem;font-weight:800;color:#1e2a4a;line-height:1;">—</div>
                                <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">{{ trans('lang.card_4_label') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- Add more sections here --}}

    </div>

</x-app-layout>
