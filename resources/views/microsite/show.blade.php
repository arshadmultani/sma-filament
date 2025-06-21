<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. {{ $microsite->doctor->name }}'s Microsite</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold mb-4">Dr. {{ $microsite->doctor->name }}</h1>
            <p class="text-gray-600 mb-6">{{ $microsite->doctor->qualification->name ?? 'Specialist' }}</p>

            <div class="mt-8">
                <h2 class="text-2xl font-bold mb-4">Reviews</h2>
                @forelse($microsite->reviews as $review)
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <p class="font-semibold">{{ $review->reviewer_name }}</p>
                        <p class="text-gray-700">Video review placeholder</p>
                    </div>
                @empty
                    <p>No reviews yet.</p>
                @endforelse 
                <p>Reviews section coming soon.</p>
            </div>
        </div>
    </div>
</body>
</html> 