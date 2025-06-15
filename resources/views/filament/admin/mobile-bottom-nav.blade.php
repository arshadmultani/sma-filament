<style>
    @media (max-width: 768px) {
        body {
            background: #f4f6fb;
        }

        .mobile-bottom-nav {
            position: fixed;
            left: 50%;
            bottom: 24px;
            transform: translateX(-50%);
            background: #fff;
            border-radius: 32px;
            box-shadow: 0 8px 32px rgba(60, 60, 120, 0.12), 0 1.5px 6px rgba(60, 60, 120, 0.08);
            padding: 8px 18px 8px 18px;
            width: calc(100vw - 32px);
            max-width: 420px;
            z-index: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            transition: opacity 0.4s cubic-bezier(.4,0,.2,1), visibility 0.4s cubic-bezier(.4,0,.2,1);
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        .mobile-bottom-nav.hide-on-scroll {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #7b8190;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 13px;
            font-weight: 500;
            min-width: 64px;
            min-height: 56px;
            border-radius: 18px;
            transition: background 0.18s, color 0.18s;
            position: relative;
        }
        .nav-item.active {
            color: #10b981;
        }
        .nav-item .nav-icon,
        .nav-item .nav-label {
            position: relative;
            z-index: 1;
        }
        .nav-item .nav-icon {
            margin-bottom: 2px;
        }
        .nav-item .nav-icon svg {
            width: 24px;
            height: 24px;
            display: block;
            color: inherit;
            fill: none;
        }
        .nav-item.active .nav-icon svg {
            color: #10b981;
            fill: #10b981;
        }
        .nav-item .nav-label {
            font-size: 12px;
            font-weight: 500;
            margin-top: 2px;
        }
        .nav-item:active:not(.active),
        .nav-item:hover:not(.active) {
            background: #f1f5f9;
            color: #10b981;
        }
    }

    @media (min-width: 769px) {
        .mobile-bottom-nav {
            display: none;
        }
    }
</style>

<div class="mobile-bottom-nav" id="mobileBottomNav">
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