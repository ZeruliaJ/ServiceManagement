<nav class="app-sidebar horizontal-nav" style="background: #ffffff; border-bottom: 2px solid #e8e8e8; padding: 0; position: relative;">
    <div class="container-fluid">
        <ul class="nav flex-nowrap" style="padding: 0; gap: 0; overflow-x: auto; scrollbar-width: none;">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}"
                   style="padding: 13px 20px; display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.875rem; border-radius: 0; border-bottom: 3px solid {{ request()->routeIs('dashboard') ? '#1e2d6b' : 'transparent' }}; color: {{ request()->routeIs('dashboard') ? '#1e2d6b' : '#6b7280' }}; white-space: nowrap; margin-bottom: -2px;">
                    <i class="bx bx-home" style="font-size: 1.15rem;"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- TVS Service Dropdown --}}
            <li class="nav-item" style="position:relative;">
              
<a href="{{ route('tvs.job-cards.create') }}" class="nav-link {{ request()->routeIs('tvs.job-cards.create') ? 'nav-active' : '' }}"
   style="padding: 13px 20px; display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.875rem; border-radius: 0; border-bottom: 3px solid {{ request()->routeIs('tvs.job-cards.create') ? '#1e2d6b' : 'transparent' }}; color: {{ request()->routeIs('tvs.job-cards.create') ? '#1e2d6b' : '#6b7280' }}; white-space: nowrap; margin-bottom: -2px; text-decoration: none;">
    <i class="bx bx-wrench" style="font-size: 1.15rem;"></i>
    <span>Job Card</span>
</a>
                <ul class="tvs-dropdown-menu" style="display:none; position:absolute; top:100%; left:0; z-index:9999; background:#fff; border-radius:10px; border:1px solid #e8e8e8; min-width:220px; padding:6px 0; box-shadow:0 4px 16px rgba(0,0,0,.1); margin-top:0; list-style:none;">

                    <li>
                        <a href="{{ route('tvs.dashboard') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('tvs.dashboard') ? 'fw-600' : '' }}" style="font-size: 0.82rem; color: {{ request()->routeIs('tvs.dashboard') ? '#1e2d6b' : '' }};">
                            <i class="bx bx-tachometer" style="font-size: 1rem; color: #273d80;"></i>
                            TVS Dashboard
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-1"></li>
                    <li><div class="px-3 py-1" style="font-size: 0.68rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px;">Workflow</div></li>

                    <li>
                        <a href="{{ route('tvs.vehicles') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('tvs.vehicles*') ? 'fw-600' : '' }}" style="font-size: 0.82rem; color: {{ request()->routeIs('tvs.vehicles*') ? '#1e2d6b' : '' }};">
                            <i class="bx bx-car" style="font-size: 1rem; color: #c0172b;"></i>
                            Vehicles
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tvs.parties') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('tvs.parties*') ? 'fw-600' : '' }}" style="font-size: 0.82rem; color: {{ request()->routeIs('tvs.parties*') ? '#1e2d6b' : '' }};">
                            <i class="bx bx-group" style="font-size: 1rem; color: #c0172b;"></i>
                            Customers / Parties
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tvs.job-cards') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('tvs.job-cards*') ? 'fw-600' : '' }}" style="font-size: 0.82rem; color: {{ request()->routeIs('tvs.job-cards*') ? '#1e2d6b' : '' }};">
                            <i class="bx bx-file" style="font-size: 1rem; color: #c0172b;"></i>
                            Job Cards
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tvs.job-cards.create') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2" style="font-size: 0.82rem;">
                            <i class="bx bx-plus-circle" style="font-size: 1rem; color: #22c55e;"></i>
                            New Job Card
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-1"></li>
                    <li><div class="px-3 py-1" style="font-size: 0.68rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px;">Management</div></li>

                    <li>
                        <a href="{{ route('tvs.warranties') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('tvs.warranties*') ? 'fw-600' : '' }}" style="font-size: 0.82rem; color: {{ request()->routeIs('tvs.warranties*') ? '#1e2d6b' : '' }};">
                            <i class="bx bx-shield-quarter" style="font-size: 1rem; color: #273d80;"></i>
                            Warranties
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tvs.gate-passes') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('tvs.gate-passes*') ? 'fw-600' : '' }}" style="font-size: 0.82rem; color: {{ request()->routeIs('tvs.gate-passes*') ? '#1e2d6b' : '' }};">
                            <i class="bx bx-door-open" style="font-size: 1rem; color: #f59e0b;"></i>
                            Gate Pass
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-1"></li>

                    <li>
                        <a href="{{ route('tvs.reports') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 {{ request()->routeIs('tvs.reports*') ? 'fw-600' : '' }}" style="font-size: 0.82rem; color: {{ request()->routeIs('tvs.reports*') ? '#1e2d6b' : '' }};">
                            <i class="bx bx-bar-chart-alt-2" style="font-size: 1rem; color: #6366f1;"></i>
                            Reports & Analytics
                        </a>
                    </li>

                </ul>
            </li>

<li class="nav-item" style="position:relative;">
              
<a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.index') ? 'nav-active' : '' }}"
   style="padding: 13px 20px; display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.875rem; border-radius: 0; border-bottom: 3px solid {{ request()->routeIs('customers.index') ? '#1e2d6b' : 'transparent' }}; color: {{ request()->routeIs('customers.index') ? '#1e2d6b' : '#6b7280' }}; white-space: nowrap; margin-bottom: -2px; text-decoration: none;">
    <i class="bx bx-wrench" style="font-size: 1.15rem;"></i>
    <span>Customers</span>
</a></li>
<li class="nav-item" style="position:relative;">
              
<a href="{{ route('tvs.job-cards.create') }}" class="nav-link {{ request()->routeIs('tvs.job-cards.create') ? 'nav-active' : '' }}"
   style="padding: 13px 20px; display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.875rem; border-radius: 0; border-bottom: 3px solid {{ request()->routeIs('tvs.job-cards.create') ? '#1e2d6b' : 'transparent' }}; color: {{ request()->routeIs('tvs.job-cards.create') ? '#1e2d6b' : '#6b7280' }}; white-space: nowrap; margin-bottom: -2px; text-decoration: none;">
    <i class="bx bx-wrench" style="font-size: 1.15rem;"></i>
    <span>Vehicles</span>
</a></li>



        </ul>
    </div>
</nav>

@push('scripts')
<script>
(function () {
    var toggle = document.querySelector('.tvs-toggle');
    var menu   = document.querySelector('.tvs-dropdown-menu');
    var chevron = document.querySelector('.tvs-chevron');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var open = menu.style.display === 'block';
        menu.style.display = open ? 'none' : 'block';
        if (chevron) chevron.style.transform = open ? '' : 'rotate(180deg)';
    });

    document.addEventListener('click', function (e) {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
            if (chevron) chevron.style.transform = '';
        }
    });
})();
</script>
@endpush

<style>
.app-sidebar .nav::-webkit-scrollbar { display: none; }
.app-sidebar .nav-link { background: transparent !important; }
.app-sidebar .nav-link:hover { color: #1e2d6b !important; }
.app-sidebar .tvs-dropdown-menu .dropdown-item { color: #374151; }
.app-sidebar .tvs-dropdown-menu .dropdown-item:hover { background: #f3f4f6; color: #1e2d6b; }
.app-sidebar .tvs-dropdown-menu .dropdown-item.fw-600 { font-weight: 700; }
</style>

