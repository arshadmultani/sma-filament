<?php

namespace App\Support;

use App\Models\ArCreative;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;

/**
 * Pure-PHP QR generation (endroid/qr-code). No external service is contacted.
 */
class Qr
{
    public static function forCreative(ArCreative $creative, string $format = 'png'): ResultInterface
    {
        $writer = $format === 'svg' ? new SvgWriter : new PngWriter;

        return (new Builder(
            writer: $writer,
            data: $creative->arUrl(),
            // High correction so the code still scans if a logo is overlaid or smudged.
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 600,
            margin: 16,
            labelText: $creative->name,
        ))->build();
    }
}
