<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('microsite.showcase_count', 10);
        $this->migrator->add('microsite.max_showcase_video_size', 10240);
        $this->migrator->add('microsite.max_showcase_image_size', 5120);

        $this->migrator->add('microsite.review_count', 10);
        $this->migrator->add('microsite.max_review_image_size', 5120);
        $this->migrator->add('microsite.max_review_video_size', 10240);

    }
};
