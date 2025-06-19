<style>
    @media (max-width: 768px) {
        body {
            background: #f4f6fb;
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100vw;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            padding: 0;
            min-height: 56px;
            box-shadow: none;
            border-radius: 0;
        }
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }
        .nav-item {
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #7b8190;
            font-size: 13px;
            font-weight: 500;
            min-width: 60px;
            min-height: 56px;
            border-radius: 0;
            background: none;
            transition: color 0.18s;
            position: relative;
            padding: 0 2px;
        }
        .nav-item.active {
            color: #10b981;
        }
        .nav-item .nav-icon svg {
            width: 24px;
            height: 24px;
            color: inherit;
        }
        .nav-label {
            font-size: 11px;
            font-weight: 500;
            margin-top: 2px;
            line-height: 1;
        }
    }

    @media (min-width: 769px) {
        .mobile-bottom-nav {
            display: none;
        }
    }
</style>

<div class="mobile-bottom-nav">
    <div class="nav-container">
        <!-- Home -->
        <a href="{{ route('filament.admin.pages.dashboard') }}"
            class="nav-item {{ request()->routeIs('filament.admin.pages.dashboard') ? 'active' : '' }}"
            aria-label="Home">
            <span class="nav-icon">
                @if(request()->routeIs('filament.admin.pages.dashboard'))
                    <!-- Heroicon: Home Solid -->
                    <x-heroicon-s-home />
                @else
                    <x-heroicon-o-home />
                @endif
            </span>
            @if(request()->routeIs('filament.admin.pages.dashboard'))
                <span class="nav-label">Home</span>
            @endif
        </a>
        <!-- Customers -->
        <a wire:navigate href="{{ route('filament.admin.pages.customers') }}"
            class="nav-item {{ request()->routeIs('filament.admin.pages.customers') ? 'active' : '' }}"
            aria-label="Customers">
            <span class="nav-icon">
                @if(request()->routeIs('filament.admin.pages.customers'))
                  <x-heroicon-s-user />
                @else
                    <x-heroicon-o-user />
                @endif
            </span>
            @if(request()->routeIs('filament.admin.pages.customers'))
                <span class="nav-label">Customers</span>
            @endif
        </a>
        <!-- Campaigns -->
        <a wire:navigate href="{{ route('filament.admin.pages.campaigns') }}"
            class="nav-item {{ request()->routeIs('filament.admin.pages.campaigns') ? 'active' : '' }}"
            aria-label="Campaigns">
            <span class="nav-icon">
                @if(request()->routeIs('filament.admin.pages.campaigns'))
                    <x-heroicon-s-bolt />
                @else
                    <x-heroicon-o-bolt />
                @endif
            </span>
            @if(request()->routeIs('filament.admin.pages.campaigns'))
                <span class="nav-label">Campaigns</span>
            @endif
        </a>
        <!-- Reports -->
        <!-- <a wire:navigate href="{{ route('filament.admin.pages.reports') }}"
            class="nav-item {{ request()->routeIs('filament.admin.pages.reports') ? 'active' : '' }}"
            aria-label="Reports">
            <span class="nav-icon">
                @if(request()->routeIs('filament.admin.pages.reports'))
                    <x-heroicon-s-chart-bar />
                @else
                    <x-heroicon-o-chart-bar />
                @endif
            </span>
            @if(request()->routeIs('filament.admin.pages.reports'))
                <span class="nav-label">Reports</span>
            @endif
        </a> -->
    </div>
</div>

<script>
(function() {
    let lastScrollY = window.scrollY;
    let ticking = false;
    let nav = document.getElementById('mobileBottomNav');
    let lastDirection = null;

    function onScroll() {
        if (window.innerWidth > 768) return;
        const currentScrollY = window.scrollY;
        if (currentScrollY > lastScrollY + 10) {
            // Scrolling down
            if (lastDirection !== 'down') {
                nav.classList.add('hide-on-scroll');
                lastDirection = 'down';
            }
        } else if (currentScrollY < lastScrollY - 4) {
            // Scrolling up
            if (lastDirection !== 'up') {
                nav.classList.remove('hide-on-scroll');
                lastDirection = 'up';
            }
        }
        lastScrollY = currentScrollY;
        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(onScroll);
            ticking = true;
        }
    });
})();
</script>

<div class="main-content" style="padding-bottom: 64px;">
    <!-- Your page content here -->
</div>