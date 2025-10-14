<?php

namespace App\Filament\Actions;

use App\Filament\Doctor\Resources\DoctorWebsiteResource;
use Filament\Actions\Action as Action;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;
use Filament\Infolists\Components\Actions\Action as InfoAction;


class DownloadQrAction
{
    public static function make(): Action
    {
        return self::baseConfig(Action::make('download_qr'));
    }

    public static function makeInfolist(): InfoAction
    {
        return self::baseConfig(InfoAction::make('download_qr'));
    }
    private static function baseConfig($action)
    {
        return $action
            ->modalWidth('md')
            ->label('QR Code')
            ->icon('heroicon-o-qr-code')
            ->modalHeading('Download QR')
            ->modalDescription('Do you want to download Website QR Code?')
            ->modalSubmitActionLabel('Yes, Download')
            ->modalCancelActionLabel('Cancel')
            ->action(fn($record) => self::handleDownload($record));
    }

    private static function handleDownload($record)
    {
        $doctorUser = DoctorWebsiteResource::currentDoctor();
        $url = route('microsite.show', ['slug' => $record->url]);
        $name = $record->doctor->name ?? $doctorUser->name ?? 'Website';

        // Generate QR code as data URI
        $qr = new QrCode($url);
        $writer = new PngWriter();
        $qrResult = $writer->write($qr);
        $qrDataUri = $qrResult->getDataUri();

        // Prepare HTML for PDF
        $html = view('pdf.microsite-qr', [
            'name' => $name,
            'qrDataUri' => $qrDataUri,
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, Str::slug($name) . '-qr.pdf');
    }





}