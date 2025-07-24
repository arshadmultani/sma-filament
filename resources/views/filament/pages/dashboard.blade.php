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

                @livewire('heading', ['title' => 'Kofol Products'])
                @livewire(App\Filament\Resources\KofolEntryResource\Widgets\KofolProductTable::class)

                @livewire('heading', ['title' => 'Kofol Bookings'])
                

            </div>
        </template>
        <template x-if="!isMobile">
            <div>
                @livewire('greeting')


                @livewire('heading', ['title' => 'Active Campaigns'])
                @livewire(App\Filament\Widgets\CampaignOverview::class)

                @livewire('heading', ['title' => 'Customers'])
                @livewire(App\Filament\Widgets\CustomerOverview::class)

                @livewire('heading', ['title' => 'Kofol Products'])
                @livewire(App\Filament\Resources\KofolEntryResource\Widgets\KofolProductChart::class)

                @livewire('heading', ['title' => 'Kofol Bookings'])
                @livewire(App\Filament\Resources\KofolEntryResource\Widgets\KofolEntryBooking::class)


            </div>
        </template>
    </div>
</x-filament-panels::page>