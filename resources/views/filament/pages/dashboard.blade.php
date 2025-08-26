<x-filament-panels::page>
    <div x-data="{ isMobile: window.innerWidth <= 768 }" x-init="window.addEventListener('resize', () => {
        isMobile = window.innerWidth <= 768
    })">
        <template x-if="isMobile">
            <div>
                @livewire('greeting')
                @livewire('campaign-card')
                @livewire('customer-card')

                @livewire('heading', ['title' => 'Kofol Products'])
                @livewire(App\Filament\Resources\KofolEntryResource\Widgets\KofolProductTable::class)

                @if (auth()->user()->can('view_user'))
                    @livewire('heading', ['title' => 'Kofol Coupons'])
                    @livewire(App\Filament\Resources\KofolEntryResource\Widgets\KofolCoupon::class)
                @endif
            </div>
        </template>
        <template x-if="!isMobile">
            <div>
                @livewire('greeting')


                @livewire('heading', ['title' => 'Active Campaigns'])

                <h1>{{ $this->getActiveCampaigns()->pluck('name') }}</h1>

                <h1>{{ $this->getActiveCampaigns()->count() }}</h1>


                @if ($this->getActiveCampaigns()->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ($this->getActiveCampaigns() as $campaign)
                            <div class="p-4 border">
                                <h2>{{ $campaign->name }}</h2>
                                <p>Entry Type: {{ $campaign->allowed_entry_type }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center">
                        <p>No active campaigns found.</p>
                    </div>
                @endif

                {{-- @livewire(App\Filament\Widgets\CampaignOverview::class)

                @livewire('heading', ['title' => 'Customers'])
                @livewire(App\Filament\Widgets\CustomerOverview::class)

                @livewire('heading', ['title' => 'Kofol Products'])
                @livewire(App\Filament\Resources\KofolEntryResource\Widgets\KofolProductChart::class)

                @livewire('heading', ['title' => 'Kofol Bookings'])
                @livewire(App\Filament\Resources\KofolEntryResource\Widgets\KofolEntryBooking::class)

                @if (auth()->user()->can('view_user'))
                    @livewire('heading', ['title' => 'Kofol Coupons'])
                    @livewire(App\Filament\Resources\KofolEntryResource\Widgets\KofolCoupon::class)
                @endif --}}

            </div>
        </template>
    </div>
</x-filament-panels::page>
