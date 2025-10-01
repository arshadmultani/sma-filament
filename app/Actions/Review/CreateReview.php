<?php

namespace App\Actions\Review;

use App\Models\State;
use App\Models\Review;
use Illuminate\Container\Attributes\CurrentUser;

class CreateReview
{
    public function handle(#[CurrentUser] $user, array $data)
    {
        Review::create([
            'doctor_id' => $user->userable->id,
            'reviewer_name' => $data['reviewer_name'],
            'review_text' => $data['review_text'] ?? null,
            'media_url' => $data['media_url'] ?? null,
            'verified_at' => $data['verified_at'] ?? null,
            'state_id' => State::pending()->first()->id,
            'is_verified' => false,

        ]);
    }
}