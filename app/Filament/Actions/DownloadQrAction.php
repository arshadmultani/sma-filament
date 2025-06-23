<?php

namespace App\Filament\Actions;

use Filament\Actions\Action as InfolistAction;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;

class DownloadQrAction
{
    public static function makeInfolist(): InfolistAction
    {
        return InfolistAction::make('download_qr')
            ->modalWidth('sm')
            ->label('QR Code')
            ->icon('heroicon-o-qr-code')
            ->modalHeading('Download QR')
            ->modalDescription('Do you want to download QR?')
            ->modalSubmitActionLabel('Yes, Download')
            ->modalCancelActionLabel('Cancel')
            ->action(function ($record) {
                $url = url('/doctor/' . $record->url);
                $name = $record->doctor->name ?? $record->name ?? 'Microsite';

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
            });
    }
}