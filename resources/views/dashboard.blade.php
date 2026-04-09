<x-app-layout :title="$title">

@push('css')
<style>
    .dash-hero {
        background: linear-gradient(135deg, #1e2d6b 0%, #273d80 40%, #c0172b 100%);
        border-radius: 12px;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .dash-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.10) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.10) 1px, transparent 1px);
        background-size: 28px 28px;
        mask-image: radial-gradient(ellipse at 80% 50%, black 20%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse at 80% 50%, black 20%, transparent 75%);
        pointer-events: none;
    }
    .stat-card { border-radius: 10px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,.07); transition: transform .18s, box-shadow .18s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.12); }
</style>
@endpush

<div class="container-fluid">

    <x-alert />

    {{-- Hero --}}
    <div class="dash-hero">
        <div style="font-size:.78rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;">{{ trans('lang.dashboard') }}</div>
        <h2 style="font-size:1.6rem;font-weight:800;color:#fff;margin:0 0 6px;line-height:1.2;">{{ trans('lang.dashboard_title') }}</h2>
        <p style="font-size:.88rem;color:rgba(255,255,255,.72);margin:0;">{{ trans('lang.dashboard_subtitle') }}</p>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-2">

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100" style="border-top:3px solid #273d80;">
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
            <div class="card stat-card h-100" style="border-top:3px solid #e94560;">
                <div class="card-body" style="padding:.85rem .9rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;background:rgba(233,69,96,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bx bx-group" style="font-size:1.4rem;color:#e94560;"></i>
                        </div>
                        <div style="min-width:0;flex:1;">
                            <div style="font-size:1.5rem;font-weight:800;color:#1e2a4a;line-height:1;">—</div>
                            <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">{{ trans('lang.card_2_label') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100" style="border-top:3px solid #22c55e;">
                <div class="card-body" style="padding:.85rem .9rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;background:rgba(34,197,94,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bx bx-trending-up" style="font-size:1.4rem;color:#22c55e;"></i>
                        </div>
                        <div style="min-width:0;flex:1;">
                            <div style="font-size:1.5rem;font-weight:800;color:#1e2a4a;line-height:1;">—</div>
                            <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">{{ trans('lang.card_3_label') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100" style="border-top:3px solid #f59e0b;">
                <div class="card-body" style="padding:.85rem .9rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;background:rgba(245,158,11,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bx bx-bar-chart-alt-2" style="font-size:1.4rem;color:#f59e0b;"></i>
                        </div>
                        <div style="min-width:0;flex:1;">
                            <div style="font-size:1.5rem;font-weight:800;color:#1e2a4a;line-height:1;">—</div>
                            <div class="text-muted fw-semibold" style="font-size:.78rem;margin-top:4px;">{{ trans('lang.card_4_label') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

</x-app-layout>
