<x-filament-panels::page>
    <div x-data="{ isMobile: window.innerWidth <= 768 }" x-init="
        window.addEventListener('resize', () => {
            isMobile = window.innerWidth <= 768
        })
    ">
        <template x-if="isMobile">
            <div>
                @livewire('greeting')
                @livewire('campaign-card')
                @livewire('customer-card')
            </div>
        </template>
        <template x-if="!isMobile">
            <div>
                @livewire('greeting')
                @livewire('heading', ['title' => 'Active Campaigns'])
                @livewire(App\Filament\Widgets\CampaignOverview::class)
                @livewire('heading', ['title' => 'Customers'])
                @livewire(App\Filament\Widgets\CustomerOverview::class)
            </div>
        </template>
    </div>
</x-filament-panels::page>

