<div>
    <h1 class="text-lg font-semibold mb-2">Active Campaigns</h1>
    @if($campaigns->isEmpty())
        <p>No active campaigns at the moment.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4">
            @foreach($campaigns as $campaign)
                <div class="rounded-lg p-3 border-primary-500 w-full shadow-lg border-2">
                    <div class="flex flex-row gap-2 justify-between">
                        <p class="text-sm">
                            {{ $this->getDaysLeft($campaign) }}
                        </p>
                        <p class="text-sm {{ $campaign->is_active ? 'text-success-500' : 'text-danger-500' }}">
                            {{ $campaign->is_active ? 'Live' : 'Ended' }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 mt-5">
                        <h1 class="text-md font-bold text-center">{{ $campaign->name }}</h1>
                        <!-- <p class="text-sm text-gray-500 italic text-center">{{ $campaign->description }}</p> -->
                    </div>

                    <button wire:navigate href="{{ route('filament.admin.resources.kofol-entries.create') }}" class="bg-primary-500 text-white px-4 py-2 my-2 rounded-md w-full">New Booking</button>

                    <x-filament::section collapsible collapsed compact>

                        <x-slot name="description">
                            Details
                        </x-slot>

                        <div class="flex flex-col gap-2">
                            <div class="flex flex-row gap-2 justify-between">
                                <div class="flex flex-col gap-1 p-1 rounded-lg w-full border text-center justify-center">
                                    <div class="font-bold text-sm">₹{{ number_format($campaign->total_amount) }}</div>
                                    <div class="text-xs">Amount</div>
                                </div>
                                <div class="flex flex-col gap-1 p-1 rounded-lg w-full border text-center justify-center">
                                    <div class="font-bold text-sm">{{ number_format($campaign->coupon_count) }}</div>
                                    <div class="text-xs">Coupons</div>
                                </div>
                            </div>
                            <div class="flex flex-row gap-2 justify-between">
                                <div class="flex flex-col gap-1 p-1 rounded-lg w-full border text-center justify-center">
                                    <div class="font-bold text-sm">{{ $campaign->entries_count }}</div>
                                    <div class="text-xs">Total Bookings</div>
                                </div>
                                <div class="flex flex-col gap-1 p-1 rounded-lg w-full border text-center justify-center">
                                    <div class="font-bold text-sm">{{ $campaign->approved_entries_count }}</div>
                                    <div class="text-xs">Approved Bookings</div>
                                </div>
                            </div>
                        </div>
                    </x-filament::section>
                </div>
            @endforeach
        </div>
    @endif
</div>