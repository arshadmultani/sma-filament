<?php

namespace App\Filament\Exports;

use App\Models\Doctor;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Log;



class DoctorExporter extends Exporter
{
    protected static ?string $model = Doctor::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('tags.name')->label('Tags'),
            ExportColumn::make('products.name')->label('Focus Product'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('user.name')->label('User'),
            ExportColumn::make('user.roles.name')->label('Designation'),
            ExportColumn::make('headquarter.name')->label('Headquarter'),
            ExportColumn::make('headquarter.area.name')->label('Area'),
            ExportColumn::make('headquarter.area.region.name')->label('Region'),
            ExportColumn::make('headquarter.area.region.zone.name')->label('Zone'),
            ExportColumn::make('user.division.name')->label('Division'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your doctor export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
    public function getJobTags(): array
    {
        return ["export{$this->export->getKey()}"];
    }
    public function getJobBatchName(): ?string
    {
        return 'doctor-export';
    }
    public function getJobRetryUntil(): ?CarbonInterface
    {
        return now()->addMinutes(10);
    }
    public function getFileName(Export $export): string
    {
        return "doctors-" . now()->format('Y-m-d-H-i-s') . ".csv";
    }
    /**
     * Override __invoke to add per-row error logging.
     */
    // public function __invoke($record): array
    // {
    //     try {
    //         Log::info('DoctorExporter: Exporting record', [
    //             'export_id' => $this->export->id ?? null,
    //             'doctor_id' => $record->id ?? null,
    //             'doctor_email' => $record->email ?? null,
    //         ]);
    //         $result = parent::__invoke($record);
    //         Log::info('DoctorExporter: Successfully exported record', [
    //             'export_id' => $this->export->id ?? null,
    //             'doctor_id' => $record->id ?? null,
    //         ]);
    //         return $result;
    //     } catch (\Throwable $e) {
    //         Log::error('DoctorExporter: Failed to export record', [
    //             'export_id' => $this->export->id ?? null,
    //             'doctor_id' => $record->id ?? null,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         throw $e; // rethrow for Filament to handle as usual
    //     }
    // }

    public static function modifyQuery($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->with('headquarter');
    }
}
