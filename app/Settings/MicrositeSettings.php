<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MicrositeSettings extends Settings
{
    public int $max_showcase_video_size;
    public int $max_showcase_image_size;
    public int $showcase_count;
    public int $max_review_video_size;
    public int $max_review_image_size;
    public int $review_count;
    public array $panel_access_reasons;

    public static function group(): string
    {
        return 'microsite';
    }
}