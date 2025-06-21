<style>
    .ping{
        
    }
</style>

@if ($campaign)
    <div class="w-full -mt-8">
        <div class="bg-white shadow-sm overflow-hidden rounded-lg border border-warning-200">
            <div class="p-4 md:p-6 relative">
                
                <div class="flex m-4 justify-between items-center">
                <p class="text-xs">{{ $this->countdownMessage }}</p>    
                <p class="text-xs">  {!! $this->campaignStatus !!}
                </p>
                <!-- <x-heroicon-o-circle-stack class="w-4 h-4 ping"/> -->
                </div>

                <div class="mt-4 flex flex-col md:flex-row items-center justify-between">
                    <div>
                        <h2 class="text-md md:text-2xl font-bold text-info-900">{{ $campaign->name }}</h2>
                        <!-- <p class="text-xs md:text-sm text-gray-600">
                            {{ $campaign->is_active ? 'Active Campaign' : 'Inactive Campaign' }} &bull; Healthcare
                        </p> -->
                    </div>
                    <!-- <div class="flex-shrink-0 mt-4 md:mt-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div> -->
                </div>

                <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mt-4 mb-2 md:mt-0 w-full md:w-auto">
                        <a href="#" class="block text-center rounded-lg bg-primary-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300 w-full">View Details</a>
                    </div>    
                <div class="flex justify-between gap-x-4 gap-y-2">
                        <div class="text-xs md:text-sm text-gray-800">
                            <span class="font-semibold">{{ $this->getAmount() }}</span>
                        </div>
                        <div class="text-xs md:text-sm text-gray-500 hidden md:block">&bull;</div>
                        <div class="text-xs md:text-sm text-gray-800">
                            <span class="font-semibold">{{ $this->entries}} Bookings</span> 
                        </div>
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
