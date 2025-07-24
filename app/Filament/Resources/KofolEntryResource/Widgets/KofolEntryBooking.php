<?php

namespace App\Filament\Resources\KofolEntryResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\KofolEntry;
use Illuminate\Support\Facades\DB;

class KofolEntryBooking extends ChartWidget
{
    // protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        // Get booking counts per day
        $data = KofolEntry::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->pluck('count', 'date');

        // Convert to cumulative (mountain/growth) data
        $cumulative = [];
        $sum = 0;
        foreach ($data as $date => $count) {
            $sum += $count;
            $cumulative[$date] = $sum;
        }

        // Keep only every 3rd day and always the last day
        $filteredDates = array_keys($cumulative);
        $filteredCumulative = [];
        $countDates = count($filteredDates);
        foreach ($filteredDates as $i => $date) {
            if ($i % 3 === 0 || $i === $countDates - 1) {
                $filteredCumulative[$date] = $cumulative[$date];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => array_values($filteredCumulative),
                    'fill' => 'start',
                ],
            ],
            'labels' => array_keys($filteredCumulative),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'elements' => [
                'line' => [
                    'tension' => 0, // 0 for straight lines, >0 for curves (0.4 is a good default)
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
