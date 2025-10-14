<?php

namespace App\Forms\Components;

use App\Support\YearOptions;
use Filament\Forms\Components\Select;

class YearSelect extends Select
{
    protected int $startYear = 1900;

    public function setStartYear(int $year): static
    {
        $this->startYear = $year;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Year')
            ->placeholder('Select a year')
            ->options(fn() => YearOptions::range($this->startYear))
            ->searchable()
            ->native(false)
            ->mutateDehydratedStateUsing(fn($state) => $state ? "{$state}-01-01" : null)
            ->afterStateHydrated(function (Select $component, $state) {
                if ($state) {
                    $component->state(date('Y', strtotime($state)));
                }
            });
    }
}