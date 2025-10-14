<?php

namespace App\Http\Controllers;

use App\Models\Microsite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MicrositeController extends Controller
{
    public function show($slug)
    {
        try {
            $microsite = Microsite::withoutGlobalScopes()
                ->where('url', $slug)
                ->with([
                    'doctor' => fn($query) => $query->withoutGlobalScopes(),
                    'doctor.reviews' => fn($query) => $query->whereNotNull('verified_at')->latest(),
                ])
                ->firstOrFail();

        } catch (ModelNotFoundException $e) {
            return response()->view('errors.404-doctor', [], 404);
        }

        $groupedShowcases = $microsite?->doctor?->showcases?->groupBy('media_type');
        return view(
            'microsite.show',
            ['microsite' => $microsite, 'groupedShowcases' => $groupedShowcases]
        );
    }
}