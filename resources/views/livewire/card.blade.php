<style>
    .ping {}
</style>

@if ($campaign)
    <div class="w-full -mt-8">
        <div class="bg-white shadow-sm overflow-hidden rounded-lg border border-warning-200">
            <div class="p-4 md:p-6 relative">

                <div class="flex m-4 justify-between items-center">
                    <p class="text-xs">{{ $this->countdownMessage }}</p>
                    <p class="text-xs"> {!! $this->campaignStatus !!}</p>
                </div>

                <div class="p-2">
                    <h2 class="text-md md:text-2xl font-bold text-info-900">{{ $campaign->name }}</h2>
                </div>


                <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between">
                    <x-filament::button wire:click="admin.resources.kofol-entries.create" icon-position="after">
                        New Booking
                    </x-filament::button>   
                    </div>
                    
                    <div class="flex  justify-between gap-x-4 gap-y-2">
                        <div class="flex flex-col text-xs md:text-sm text-gray-800">
                            <span class="font-semibold">{{ $this->getAmount() }}</span>
                            <span>Invoice Total</span>
                        </div>
                        <div class="text-xs md:text-sm text-gray-500 hidden md:block">&bull;</div>
                        <div class="text-xs md:text-sm text-gray-800 flex flex-col items-center">
                            <span class="font-semibold">{{ $this->entries}}</span>
                            <span class="">Bookings</span>
                        </div>
                    </div>
                

            </div>
        </div>
        
    </div>

@else
    <div class="rounded-lg bg-white p-6 text-center shadow-md">
        <p class="text-gray-500">No active campaigns at the moment.</p>
    </div>
@endif