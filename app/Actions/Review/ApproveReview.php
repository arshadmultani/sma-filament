<?php

namespace App\Actions\Review;

use App\Models\Review;
use Illuminate\Support\Facades\Log;

class ApproveReview
{

    public function handle(Review $review)
    {
        $review->update([
            'verified_at' => now(),
            'state_id' => $review->state->isFinalized ? $review->state_id : $review->state->finalizedState()->first()->id,
        ]);

        Log::info('Review approved', ['review_id' => $review->id, 'doctor_id' => $review->doctor_id]);
    }
}