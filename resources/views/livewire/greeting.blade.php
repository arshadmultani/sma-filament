<div>
    <style>
        .greeting-text {
            font-size: 0.875rem; /* text-sm */
        }

        @media (min-width: 1024px) { /* lg breakpoint */
            .greeting-text {
                font-size: 1.25rem; /* text-xl */
            }
        }
    </style>
    <h2 class="italic font-semibold greeting-text">{{ $greeting }}</h2>
</div>
