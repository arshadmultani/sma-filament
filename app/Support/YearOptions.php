<?php

namespace App\Support;


class YearOptions
{

    public static function range(int $start = 1900, ?int $end = null): array
    {
        $end = $end ?? now()->year;

        $years = range($end, $start);

        return array_combine($years, $years);
    }
}