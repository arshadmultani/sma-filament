<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KofolEntry;
use Illuminate\Support\Facades\DB;

class BackfillKofolEntryHeadquarter extends Command
{
    protected $signature = 'kofol-entry:backfill-headquarter {--dry-run : Preview changes without saving}';
    protected $description = 'Backfill the headquarter_id column in kofol_entries based on the customer (Doctor or Chemist)';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $updated = 0;
        $skipped = 0;
        $total = 0;

        KofolEntry::with('customer')->chunkById(100, function ($entries) use (&$updated, &$skipped, &$total, $dryRun) {
            foreach ($entries as $entry) {
                $total++;
                $customer = $entry->customer;
                $headquarterId = $customer?->headquarter_id;
                if ($headquarterId) {
                    if ($dryRun) {
                        $this->line("[DRY RUN] Would update KofolEntry ID {$entry->id} with headquarter_id {$headquarterId}");
                    } else {
                        $entry->headquarter_id = $headquarterId;
                        $entry->save();
                        $this->line("Updated KofolEntry ID {$entry->id} with headquarter_id {$headquarterId}");
                    }
                    $updated++;
                } else {
                    $this->warn("Skipped KofolEntry ID {$entry->id}: No customer or headquarter_id found");
                    $skipped++;
                }
            }
        });

        $this->info("Total processed: {$total}");
        $this->info("Total updated: {$updated}");
        $this->info("Total skipped: {$skipped}");
        return 0;
    }
} 