<div>
    <style>
        .greeting-text {
            font-size: 1.2rem; /* text-sm */
        }

        @media (min-width: 1024px) { /* lg breakpoint */
            .greeting-text {
                font-size: 1.5rem; /* text-xl */
                font-size: 1.5rem; /* text-xl */
            }
        }
    </style>
    <h2 class="italic font-bold greeting-text text-primary-500">{{ $greeting }}</h2>
</div>
