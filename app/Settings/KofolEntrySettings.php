<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class KofolEntrySettings extends Settings
{

    public int $max_coupons;
    public int $max_invoice_size;

    public static function group(): string
    {
        return 'KofolEntry';
    }
}