<!-- @props(['tabs' => []])
<div class="w-full border-b bg-white flex items-center justify-between px-4" style="min-height: 56px;">
    <div class="flex flex-1">
        @foreach ($tabs as $tab)
            <a href="{{ $tab['url'] }}"
               class="flex flex-row items-center gap-2 justify-center px-4 py-2 transition-colors duration-150 min-w-[80px] rounded-t-md
                   {{ $tab['active'] ? 'bg-emerald-600 text-white font-semibold' : 'bg-white text-emerald-600 hover:bg-emerald-50' }}"
            >
                @if (!empty($tab['icon']))
                    <x-dynamic-component :component="$tab['icon']" class="w-5 h-5 {{ $tab['active'] ? 'text-white' : 'text-emerald-600' }}" />
                @endif
                <span class="text-xs">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>  -->

<!-- <div x-data="{ selectedTab: 'groups' }" class="w-full">
	<div x-on:keydown.right.prevent="$focus.wrap().next()" x-on:keydown.left.prevent="$focus.wrap().previous()" class="flex gap-2 overflow-x-auto border-b border-outline dark:border-outline-dark" role="tablist" aria-label="tab options">
		<button x-on:click="selectedTab = 'groups'" x-bind:aria-selected="selectedTab === 'groups'" x-bind:tabindex="selectedTab === 'groups' ? '0' : '-1'" x-bind:class="selectedTab === 'groups' ? 'font-bold text-primary border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' : 'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'" class="flex h-min items-center gap-2 px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelGroups" >
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-4">
				<path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM1.49 15.326a.78.78 0 0 1-.358-.442 3 3 0 0 1 4.308-3.516 6.484 6.484 0 0 0-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 0 1-2.07-.655ZM16.44 15.98a4.97 4.97 0 0 0 2.07-.654.78.78 0 0 0 .357-.442 3 3 0 0 0-4.308-3.517 6.484 6.484 0 0 1 1.907 3.96 2.32 2.32 0 0 1-.026.654ZM18 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM5.304 16.19a.844.844 0 0 1-.277-.71 5 5 0 0 1 9.947 0 .843.843 0 0 1-.277.71A6.975 6.975 0 0 1 10 18a6.974 6.974 0 0 1-4.696-1.81Z" />
			</svg>
			Groups
		</button>
		<button x-on:click="selectedTab = 'likes'" x-bind:aria-selected="selectedTab === 'likes'" x-bind:tabindex="selectedTab === 'likes' ? '0' : '-1'" x-bind:class="selectedTab === 'likes' ? 'font-bold text-primary border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' : 'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'" class="flex h-min items-center gap-2 px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelLikes" >
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-4">
				<path d="m9.653 16.915-.005-.003-.019-.01a20.759 20.759 0 0 1-1.162-.682 22.045 22.045 0 0 1-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 0 1 8-2.828A4.5 4.5 0 0 1 18 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 0 1-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 0 1-.69.001l-.002-.001Z" />
			</svg>
			Likes
		</button>
		<button x-on:click="selectedTab = 'comments'" x-bind:aria-selected="selectedTab === 'comments'" x-bind:tabindex="selectedTab === 'comments' ? '0' : '-1'" x-bind:class="selectedTab === 'comments' ? 'font-bold text-primary border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' : 'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'" class="flex h-min items-center gap-2 px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelComments" >
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-4">
				<path d="M3.505 2.365A41.369 41.369 0 0 1 9 2c1.863 0 3.697.124 5.495.365 1.247.167 2.18 1.108 2.435 2.268a4.45 4.45 0 0 0-.577-.069 43.141 43.141 0 0 0-4.706 0C9.229 4.696 7.5 6.727 7.5 8.998v2.24c0 1.413.67 2.735 1.76 3.562l-2.98 2.98A.75.75 0 0 1 5 17.25v-3.443c-.501-.048-1-.106-1.495-.172C2.033 13.438 1 12.162 1 10.72V5.28c0-1.441 1.033-2.717 2.505-2.914Z" />
				<path d="M14 6c-.762 0-1.52.02-2.271.062C10.157 6.148 9 7.472 9 8.998v2.24c0 1.519 1.147 2.839 2.71 2.935.214.013.428.024.642.034.2.009.385.09.518.224l2.35 2.35a.75.75 0 0 0 1.28-.531v-2.07c1.453-.195 2.5-1.463 2.5-2.915V8.998c0-1.526-1.157-2.85-2.729-2.936A41.645 41.645 0 0 0 14 6Z" />
			</svg>
			Comments
		</button>
		
	</div>
</div> -->

