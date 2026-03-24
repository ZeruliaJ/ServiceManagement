<nav class="app-sidebar horizontal-nav" style="background: #ffffff; border-bottom: 1px solid #e8e8e8; padding: 0; position: relative;">
    <div class="container-fluid">
        <ul class="nav nav-pills" style="padding: 12px 0; gap: 0;">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" 
                   class="nav-link"
                   style="padding: 8px 20px; display: flex; align-items: center; gap: 10px; color: #c0172b; font-weight: 600; border: none; background: none; font-size: 0.95rem;">
                    <i class="bx bx-home" style="font-size: 1.3rem; color: #c0172b;"></i>
                    <span>Dashboard</span>
                </a>
            </li>

        </ul>
    </div>
</nav>

@push('scripts')
<script>
$(function () {
    // Horizontal nav dropdown toggle
    $('.horizontal-nav .dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        $(this).next('.dropdown-menu').toggle();
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').hide();
        }
    });
});
</script>
@endpush
