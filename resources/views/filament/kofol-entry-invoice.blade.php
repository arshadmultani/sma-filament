@php
use Illuminate\Support\Carbon;
@endphp

<div class="font-sans m-8 text-gray-800">
    <div class="flex justify-between items-center mb-8">
        <div class="text-3xl font-bold">Invoice # KSV/POB/{{ $kofolEntry->id }}</div>
        <div>
            @php
                $statusColors = [
                    'Pending' => 'bg-orange-500',
                    'Approved' => 'bg-blue-500',
                    'Rejected' => 'bg-red-500'
                ];
                $statusColor = $statusColors[$kofolEntry->status] ?? 'bg-gray-500';
            @endphp
            <span class="inline-block py-1 px-3 rounded-lg text-white text-base {{ $statusColor }}">
                {{ $kofolEntry->status }}
            </span>
        </div>
    </div>

    <table class="w-full mb-8">
        <tr>
            <td class="py-2 pr-4">
                <strong>Campaign:</strong> {{ $kofolEntry->kofolCampaign->name ?? '-' }}
            </td>
            <td class="py-2 pr-4">
                <strong>Customer:</strong> {{ $kofolEntry->customer->name ?? '-' }} 
                ({{ class_basename($kofolEntry->customer_type) }})
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-4">
                <strong>Submitted By:</strong> {{ $kofolEntry->user->name ?? '-' }}
            </td>
            <td class="py-2 pr-4">
                <strong>Submission Date:</strong> 
                @if($kofolEntry->created_at)
                    {{ Carbon::parse($kofolEntry->created_at)->format('d-m-Y H:i') }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <h3 class="text-xl font-semibold mb-4">Products</h3>
    
    @if($kofolEntry->products && count($kofolEntry->products) > 0)
        <table class="w-full border-collapse mb-8">
            <thead>
                <tr>
                    <th class="border border-gray-300 p-3 text-left bg-gray-100">Product</th>
                    <th class="border border-gray-300 p-3 text-left bg-gray-100">Quantity</th>
                    <th class="border border-gray-300 p-3 text-left bg-gray-100">Price (INR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kofolEntry->products as $product)
                    <tr>
                        <td class="border border-gray-300 p-3">
                            {{ \App\Models\Product::find($product['product_id'])?->name ?? '-' }}
                        </td>
                        <td class="border border-gray-300 p-3">
                            {{ $product['quantity'] ?? 0 }}
                        </td>
                        <td class="border border-gray-300 p-3">
                            ₹{{ number_format($product['price'] ?? 0, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-gray-500 italic mb-8">No products found</div>
    @endif

    <div class="text-xl font-bold text-right">
        Total Amount: ₹{{ number_format($kofolEntry->invoice_amount ?? 0, 2) }}
    </div>

    {{-- Print styles for Filament --}}
    <style>
        @media print {
            .fi-sidebar,
            .fi-topbar,
            .fi-breadcrumbs,
            .print\\:hidden {
                display: none !important;
            }
            
            body {
                margin: 0 !important;
                padding: 1rem !important;
            }
        }
    </style>
</div>