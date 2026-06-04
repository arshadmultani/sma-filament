<?php

namespace Tests\Feature;

use App\Models\ArCreative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArCreativeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake the private S3 disk and give it a stub presigned-URL resolver
        // (the fake local adapter can't generate real temporary URLs).
        Storage::fake('s3');
        Storage::disk('s3')->buildTemporaryUrlsUsing(
            fn (string $path, $expiry) => 'https://example.test/'.$path
        );
    }

    private function makeReady(array $overrides = []): ArCreative
    {
        return ArCreative::create(array_merge([
            'name' => 'Calpol Box Reveal',
            'status' => 'published',
            'play_mode' => 'loop',
            'marker_image_path' => 'ar/markers/m.png',
            'video_path' => 'ar/videos/v.mp4',
            'mind_file_path' => 'ar/mind/calpol.mind',
            'tracking_score' => 85,
            'marker_width' => 1600,
            'marker_height' => 1027,
        ], $overrides));
    }

    public function test_slug_has_readable_prefix_and_random_token(): void
    {
        $a = ArCreative::create(['name' => 'Box Reveal', 'status' => 'draft', 'play_mode' => 'loop']);
        $b = ArCreative::create(['name' => 'Box Reveal', 'status' => 'draft', 'play_mode' => 'loop']);

        $this->assertMatchesRegularExpression('/^box-reveal-[a-z0-9]{6}$/', $a->slug);
        $this->assertNotSame($a->slug, $b->slug);
    }

    public function test_trackability_tiers_and_aspect(): void
    {
        $this->assertNull((new ArCreative(['tracking_score' => null]))->trackabilityTier());
        $this->assertSame('poor', (new ArCreative(['tracking_score' => 20]))->trackabilityTier());
        $this->assertFalse((new ArCreative(['tracking_score' => 20]))->isTrackable());
        $this->assertSame('fair', (new ArCreative(['tracking_score' => 45]))->trackabilityTier());
        $this->assertTrue((new ArCreative(['tracking_score' => 45]))->isTrackable());
        $this->assertSame('good', (new ArCreative(['tracking_score' => 80]))->trackabilityTier());

        $this->assertSame(0.6419, (new ArCreative(['marker_width' => 1600, 'marker_height' => 1027]))->markerAspectRatio());
    }

    public function test_published_ready_creative_is_public(): void
    {
        $creative = $this->makeReady();

        $this->get(route('ar.show', $creative))
            ->assertOk()
            ->assertSee('ar-start-button', false);
    }

    public function test_draft_is_hidden_from_public(): void
    {
        $this->get(route('ar.show', $this->makeReady(['status' => 'draft'])))->assertNotFound();
    }

    public function test_admin_can_preview_a_ready_draft(): void
    {
        $creative = $this->makeReady(['status' => 'draft']);

        $this->actingAs(User::factory()->create())
            ->get(route('ar.show', $creative))
            ->assertOk();
    }

    public function test_not_ready_creative_is_404(): void
    {
        $this->get(route('ar.show', $this->makeReady(['mind_file_path' => null])))->assertNotFound();
    }

    public function test_compile_endpoint_rejects_guests(): void
    {
        $creative = $this->makeReady(['mind_file_path' => null, 'tracking_score' => null]);

        $this->post(route('ar.compile', $creative), [])->assertStatus(302);
        $this->assertNull($creative->fresh()->mind_file_path);
    }

    public function test_compile_stores_mind_score_and_dimensions(): void
    {
        $creative = $this->makeReady(['mind_file_path' => null, 'tracking_score' => null, 'marker_width' => null, 'marker_height' => null]);
        $file = UploadedFile::fake()->create('target.mind', 12, 'application/octet-stream');

        $this->actingAs(User::factory()->create())
            ->post(route('ar.compile', $creative), [
                'mind' => $file,
                'tracking_score' => 88,
                'marker_width' => 1280,
                'marker_height' => 720,
            ])
            ->assertOk()
            ->assertJson(['ok' => true, 'tracking_score' => 88]);

        $creative->refresh();
        $this->assertSame("ar/mind/{$creative->slug}.mind", $creative->mind_file_path);
        $this->assertSame(88, $creative->tracking_score);
        $this->assertSame(1280, $creative->marker_width);
        Storage::disk('s3')->assertExists("ar/mind/{$creative->slug}.mind");
    }

    public function test_compile_endpoint_rejects_score_above_100(): void
    {
        $creative = $this->makeReady(['mind_file_path' => null, 'tracking_score' => null]);
        $file = UploadedFile::fake()->create('target.mind', 12, 'application/octet-stream');

        $this->actingAs(User::factory()->create())
            ->post(route('ar.compile', $creative), [
                'mind' => $file, 'tracking_score' => 250, 'marker_width' => 100, 'marker_height' => 100,
            ])
            ->assertSessionHasErrors('tracking_score');
    }
}
