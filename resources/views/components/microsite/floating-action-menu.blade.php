@props(['microsite'])

<footer x-data="{
    share() {
        if (navigator.share) {
            navigator.share({
                title: `Dr. {{ $microsite->doctor->name }}'s Microsite`,
                text: 'Check out the profile of Dr. {{ $microsite->doctor->name }}.',
                url: window.location.href,
            }).catch((error) => console.log('Error sharing', error));
        } else {
            alert('Sharing is not supported on this browser. You can manually copy the link.');
        }
    }
}"
    class="fixed bottom-4 left-1/2 transform -translate-x-1/2
           px-9 gap-6 py-3 rounded-full 
         bg-white/40 backdrop-blur-md border border-white/30 shadow-md
         flex justify-around items-center space-x-8 z-50">

    {{-- Call to Action: Share --}}
    <div class="flex flex-col items-center text-center">
        <x-filament::icon-button icon="heroicon-o-share" size="lg" @click="share()" label="Share" />
        <span class="text-xs mt-1">Share</span>
    </div>

    {{-- Call to Action: Call --}}
    <div class="flex flex-col items-center text-center">
        <x-filament::icon-button icon="heroicon-o-phone" size="lg" :href="'tel:' . ($microsite->doctor->phone_number ?? '')" tag="a"
            label="Call" />
        <span class="text-xs mt-1">Call</span>
    </div>

    {{-- Call to Action: Email --}}
    {{-- <div class="flex flex-col items-center text-center">
        <x-filament::icon-button icon="heroicon-o-envelope" size="lg" :href="'mailto:' . ($microsite->doctor->email ?? '')" tag="a"
            label="Email" />
        <span class="text-xs mt-1 font-semibold text-sky-700">Email</span>
    </div> --}}

</footer>
