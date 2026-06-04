<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArCreative extends Model
{
    /** Private S3 disk (MinIO locally, S3 in prod). Accessed via presigned URLs. */
    public const DISK = 's3';

    /** Below this trackability score the marker is treated as untrackable. */
    public const MIN_TRACKABLE_SCORE = 45;

    /** How long presigned media URLs stay valid. */
    public const URL_TTL_HOURS = 6;

    protected $fillable = [
        'name',
        'slug',
        'marker_image_path',
        'video_path',
        'mind_file_path',
        'tracking_score',
        'marker_width',
        'marker_height',
        'status',
        'play_mode',
    ];

    protected function casts(): array
    {
        return [
            'tracking_score' => 'integer',
            'marker_width' => 'integer',
            'marker_height' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ArCreative $creative): void {
            if (blank($creative->slug)) {
                $creative->slug = static::uniqueSlug($creative->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        // Readable prefix + unguessable random token, e.g. "calpol-reveal-k4p9x2".
        $prefix = Str::slug($name);
        $prefix = $prefix !== '' ? $prefix.'-' : '';

        do {
            $slug = $prefix.Str::lower(Str::random(6));
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isReady(): bool
    {
        return filled($this->marker_image_path)
            && filled($this->video_path)
            && filled($this->mind_file_path);
    }

    public function isTrackable(): bool
    {
        return $this->tracking_score !== null
            && $this->tracking_score >= self::MIN_TRACKABLE_SCORE;
    }

    public function trackabilityTier(): ?string
    {
        if ($this->tracking_score === null) {
            return null;
        }

        return match (true) {
            $this->tracking_score >= 70 => 'good',
            $this->tracking_score >= self::MIN_TRACKABLE_SCORE => 'fair',
            default => 'poor',
        };
    }

    public function trackabilityLabel(): string
    {
        return match ($this->trackabilityTier()) {
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor — won’t track',
            default => 'Not compiled',
        };
    }

    public function trackabilityColor(): string
    {
        return match ($this->trackabilityTier()) {
            'good' => 'success',
            'fair' => 'warning',
            'poor' => 'danger',
            default => 'gray',
        };
    }

    /** Marker height ÷ width, from dimensions captured at compile time. */
    public function markerAspectRatio(): float
    {
        if (! $this->marker_width || ! $this->marker_height) {
            return 1.0;
        }

        return round($this->marker_height / $this->marker_width, 4);
    }

    public function arUrl(): string
    {
        return route('ar.show', $this->slug);
    }

    /** Temporary presigned URL so a phone can fetch the private object directly. */
    protected function temporaryUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk(self::DISK)->temporaryUrl($path, now()->addHours(self::URL_TTL_HOURS));
    }

    public function markerImageUrl(): ?string
    {
        return $this->temporaryUrl($this->marker_image_path);
    }

    public function videoUrl(): ?string
    {
        return $this->temporaryUrl($this->video_path);
    }

    public function mindFileUrl(): ?string
    {
        return $this->temporaryUrl($this->mind_file_path);
    }
}
