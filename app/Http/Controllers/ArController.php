<?php

namespace App\Http\Controllers;

use App\Models\ArCreative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArController extends Controller
{
    /**
     * The public AR experience a doctor lands on after scanning the QR.
     */
    public function show(ArCreative $creative)
    {
        // A half-finished creative can't render for anyone.
        if (! $creative->isReady()) {
            throw new NotFoundHttpException;
        }

        // Drafts are previewable by a signed-in admin only; the public (the
        // doctor scanning the QR, with no session) only sees published ones.
        if (! $creative->isPublished() && ! auth()->check()) {
            throw new NotFoundHttpException;
        }

        // Never cache the HTML, so a phone always loads the page that points at
        // the latest built JS/CSS and fresh presigned media URLs.
        return response()
            ->view('ar.show', ['creative' => $creative])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Store the MindAR ".mind" tracking file compiled in the admin's browser,
     * plus the trackability score and marker dimensions it measured.
     */
    public function storeMind(Request $request, ArCreative $creative)
    {
        $request->validate([
            'mind' => ['required', 'file', 'max:20480'], // up to ~20 MB
            'tracking_score' => ['required', 'integer', 'min:0', 'max:100'],
            'marker_width' => ['required', 'integer', 'min:1'],
            'marker_height' => ['required', 'integer', 'min:1'],
        ]);

        if ($creative->mind_file_path) {
            Storage::disk(ArCreative::DISK)->delete($creative->mind_file_path);
        }

        $path = Storage::disk(ArCreative::DISK)->putFileAs(
            'ar/mind',
            $request->file('mind'),
            "{$creative->slug}.mind",
            'private',
        );

        $creative->update([
            'mind_file_path' => $path,
            'tracking_score' => (int) $request->integer('tracking_score'),
            'marker_width' => (int) $request->integer('marker_width'),
            'marker_height' => (int) $request->integer('marker_height'),
        ]);

        return response()->json([
            'ok' => true,
            'tracking_score' => $creative->tracking_score,
        ]);
    }
}
