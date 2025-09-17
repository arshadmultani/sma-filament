<div {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['w-full']) }}>
    @foreach ($getState() as $videoUrl)
        <div class="mb-4 overflow-hidden rounded-lg shadow-md">
            <video {{ $getVideoAttributes()->class(['w-full']) }}>
                <source src="{{ $videoUrl }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    @endforeach
</div>
