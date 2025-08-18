<?php

use App\Models\State;
use App\Enums\StateCategory;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('POB.max_invoice_size', 5120);
        $this->migrator->add('POB.max_invoices', 1);
        $this->migrator->add('POB.start_state', State::query()
            ->where('is_system', true)
            ->where('category', StateCategory::PENDING)
            ->first()?->id);



    }
};
