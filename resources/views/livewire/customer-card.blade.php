<style>
    .border-secondary-500,
    .bg-color {
        border-color: orange;
    }

    .customer-button {
        background-color: orange;
        color: white;
    }
</style>
<div class="">
    <div class="text-lg font-semibold mb-2 mt-3">Customers</div>
    <div class="border-2 rounded-lg p-3 border-secondary-500 shadow-lg flex flex-col">
        <div class="flex flex-row gap-2 justify-between">
            <div class="bg-gray-100 w-1/2 rounded-lg p-2 flex flex-col gap-2 items-center ">
                <span class="text-md font-bold">{{ number_format($doctorCount) }}</span>
                <span class="text-sm">Doctors</span>
            </div>
            <div class="bg-gray-100 w-1/2 rounded-lg p-2 flex flex-col gap-2 items-center">
                <span class="text-md font-bold">{{ number_format($chemistCount) }}</span>
                <span class="text-sm">Chemists</span>
            </div>
        </div>
        <div>
            <button x-on:click="$dispatch('open-modal', { id: 'new-customer-modal' })"
                class="bg-warning-500 text-white px-4 py-2 my-2 rounded-md w-full">New Customer</button>
        </div>
        
        <x-filament::modal id="new-customer-modal">
            <x-slot name="heading">
                Add New
            </x-slot>
            <div class="flex flex-col gap-4">
                <div wire:navigate href="{{ route('filament.admin.resources.doctors.create') }}" class="bg-primary-500 text-white px-4 py-2 rounded-md w-full">New Doctor</div>
                <div wire:navigate href="{{ route('filament.admin.resources.chemists.create') }}" class="bg-primary-500 text-white px-4 py-2 rounded-md w-full">New Chemist</div>
            </div>
        </x-filament::modal>
        <x-filament::section collapsible collapsed compact>

                        <x-slot name="description">
                            Details
                        </x-slot>

                        <div class="flex flex-col gap-2">
                            <div class="flex flex-row gap-2 justify-between">
                                <div class="flex flex-col gap-1 p-1 rounded-lg w-full border text-center justify-center">
                                    <div class="font-bold text-sm">{{ number_format($pendingDoctors) }}</div>
                                    <div class="text-xs">Pending Doctors</div>
                                </div>
                                <div class="flex flex-col gap-1 p-1 rounded-lg w-full border text-center justify-center">
                                    <div class="font-bold text-sm">{{ number_format($pendingChemists) }}</div>
                                    <div class="text-xs">Pending Chemists</div>
                                </div>
                            </div>
                           
                            
                        </div>
                    </x-filament::section>
    </div>

</div>