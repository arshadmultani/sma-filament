<?php

namespace App\Settings;

use App\Traits\StateRolePermissions;
use Spatie\LaravelSettings\Settings;

class POBSettings extends Settings
{

    public int $max_invoice_size;
    public int $max_invoices;
    public ?string $start_state;



    public static function group(): string
    {
        return 'POB';
    }
}