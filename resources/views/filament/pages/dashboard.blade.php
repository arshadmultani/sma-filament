<x-filament-panels::page>

    @if (!auth()->user()->hasRole('doctor'))
        <div x-data="{ isMobile: window.innerWidth <= 768 }" x-init="window.addEventListener('resize', () => {
            isMobile = window.innerWidth <= 768
        })">
            <template x-if="isMobile">
                <div>
                    @livewire('greeting')
                    </br>

                    @livewire(App\Filament\Widgets\CampaignList::class)

                    {{-- @livewire('campaign-card') --}}
                    @livewire('heading', ['title' => 'Activities'])
                    @livewire(App\Filament\Widgets\ActivityOverview::class)

                    @livewire('heading', ['title' => 'Customers'])
                    @livewire(App\Filament\Widgets\CustomerOverview::class)

                    {{-- @livewire('customer-card') --}}

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




                    {{-- <h1>{{ $this->getActiveCampaigns()->pluck('name') }}</h1>

                <h1>{{ $this->getActiveCampaigns()->count() }}</h1> --}}


                    {{-- @if ($this->getActiveCampaigns()->isNotEmpty())
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
                @endif --}}
                    @livewire('heading', ['title' => 'Active Campaigns'])
                    {{-- @livewire(App\Filament\Widgets\CampaignOverview::class) --}}
                    {{-- @livewire('heading', ['title' => '']) --}}
                    @livewire(App\Filament\Widgets\CampaignList::class)

                    @livewire('heading', ['title' => 'Activities'])
                    @livewire(App\Filament\Widgets\ActivityOverview::class)

                    @livewire('heading', ['title' => 'Customers'])
                    @livewire(App\Filament\Widgets\CustomerOverview::class)


                    @livewire('heading', ['title' => 'Users'])
                    @livewire(App\Filament\Widgets\UserOverview::class)


                    {{-- @livewire(App\Filament\Widgets\CampaignOverview::class)


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
    @else
        <div class="w-full md:w-1/2">
            <h2 class="text-2xl italic text-primary-500 font-semibold">Welcome to StepUp</h2>
            </br></br>

            <div class=" rounded-xl p-3 m-1 shadow-sm border-2">

                <div class="flex flex-row gap-2 justify-between items-center">
                    <div class="font-bold text-lg">Website</div>
                    <p class="font-md text-sm">
                        Manage your content
                    </p>
                </div>

                <div class="mt-6 w-full">
                    <x-filament::button class="w-full" tag="a"
                        href="{{ route('filament.doctor.resources.doctor-websites.index') }}"
                        icon="heroicon-s-arrow-long-right" icon-position="after">
                        Manage your website
                    </x-filament::button>
                </div>

            </div>

        </div>
    @endif

</x-filament-panels::page>
