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

<div class="mobile-bottom-nav">
    <div class="nav-container">
        <!-- Home -->
        <a href="{{ route('filament.admin.pages.dashboard') }}"
            class="nav-item {{ request()->routeIs('filament.admin.pages.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">
                @if(request()->routeIs('filament.admin.pages.dashboard'))
                    <!-- Heroicon: Home Solid -->
                    <x-heroicon-s-home />
                @else
                    <x-heroicon-o-home />

                @endif
            </span>
            <span class="nav-label">Home</span>
        </a>
        <!-- Customers -->
        <a wire:navigate href="{{ route('filament.admin.resources.users.index') }}"
            class="nav-item {{ request()->routeIs('filament.admin.resources.users.*') ? 'active' : '' }}">
            <span class="nav-icon">
                @if(request()->routeIs('filament.admin.resources.users.*'))
                  <x-heroicon-s-user />
                @else
                    <x-heroicon-o-user />
                @endif
            </span>
            <span class="nav-label">Customers</span>
        </a>
        <!-- Campaigns -->
        <a wire:navigate href="{{ route('filament.admin.resources.doctors.index') }}"
            class="nav-item {{ request()->routeIs('filament.admin.resources.campaigns.*') ? 'active' : '' }}">
            <span class="nav-icon">
                @if(request()->routeIs('filament.admin.resources.campaigns.*'))
                    <x-heroicon-s-bolt />
                @else
                    <x-heroicon-o-bolt />
                @endif
            </span>
            <span class="nav-label">Campaigns</span>
        </a>
        <!-- Reports -->
        <a wire:navigate href="{{ route('filament.admin.resources.chemists.index') }}"
            class="nav-item {{ request()->routeIs('filament.admin.pages.reports') ? 'active' : '' }}">
            <span class="nav-icon">
                @if(request()->routeIs('filament.admin.pages.reports'))
                    <x-heroicon-s-chart-bar />
                @else
                    <x-heroicon-o-chart-bar />
                @endif
            </span>
            <span class="nav-label">Reports</span>
        </a>

    </div>
</div>