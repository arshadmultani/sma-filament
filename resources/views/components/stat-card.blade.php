@props(['value', 'label', 'color' => 'text-green-600', 'bgColor' => 'bg-white', 'labelColor' => 'text-gray-800'])
<div class="rounded-2xl shadow border p-4 flex flex-col items-center justify-center {{ $bgColor }} min-w-[120px] min-h-[90px]">
    <span class="text-4xl font-semibold {{ $color }}">{{ $value }}</span>
    <span class="text-lg font-medium text-center mt-1 {{ $labelColor }}">{{ $label }}</span>
</div> 