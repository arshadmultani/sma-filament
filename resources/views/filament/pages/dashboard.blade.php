<x-filament-panels::page>

    @if (!auth()->user()->hasRole('doctor'))
        <div x-data="{ isMobile: window.innerWidth <= 768 }" x-init="window.addEventListener('resize', () => {
            isMobile = window.innerWidth <= 768
        })">
            <template x-if="isMobile">
                <div>
                    @livewire('greeting')

                    @livewire('heading', ['title' => 'Active Campaigns'])
                    @livewire(App\Filament\Widgets\CampaignList::class)

                    @livewire('heading', ['title' => 'Activities'])
                    @livewire(App\Filament\Widgets\ActivityOverview::class)

                    @livewire('heading', ['title' => 'Customers'])
                    @livewire(App\Filament\Widgets\CustomerOverview::class)

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
                    @livewire(App\Filament\Widgets\CampaignList::class)

                    @livewire('heading', ['title' => 'Activities'])
                    @livewire(App\Filament\Widgets\ActivityOverview::class)

                    @livewire('heading', ['title' => 'Customers'])
                    @livewire(App\Filament\Widgets\CustomerOverview::class)

                    @if (auth()->user()->can('view_user'))
                        @livewire('heading', ['title' => 'Users'])
                        @livewire(App\Filament\Widgets\UserOverview::class)
                    @endif

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
