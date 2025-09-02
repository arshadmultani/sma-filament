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
}" class="fixed bottom-0 left-0 right-0 h-20" style="max-width: 420px; margin: 0 auto;">
    <div
        class="bg-white/70 backdrop-blur-lg rounded-t-3xl shadow-[0_-5px_20px_rgba(0,0,0,0.08)] h-full flex justify-around items-center px-6">

        <div class="flex flex-col items-center">
            <x-filament::icon-button icon="heroicon-o-share" size="lg" @click="share()" label="Share" />
            <span class="text-xs mt-1 font-medium text-gray-600">Share</span>
        </div>

        <div class="flex flex-col items-center">
            <x-filament::icon-button icon="heroicon-o-phone" size="lg" :href="'tel:' . ($microsite->doctor->phone_number ?? '')" tag="a"
                label="Call" />
            <span class="text-xs mt-1 font-medium text-gray-600">Call</span>
        </div>

        <div class="flex flex-col items-center">
            <x-filament::icon-button icon="heroicon-o-envelope" size="lg" :href="'mailto:' . ($microsite->doctor->email ?? '')" tag="a"
                label="Email" />
            <span class="text-xs mt-1 font-medium text-gray-600">Email</span>
        </div>

    </div>
</footer>
