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
<div>
    <div class="text-lg font-semibold mb-2">Customers</div>
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
            <button wire:navigate href="{{ route('filament.admin.resources.kofol-entries.create') }}"
                class="bg-warning-500 text-white px-4 py-2 my-2 rounded-md w-full">New Customer</button>
        </div>
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