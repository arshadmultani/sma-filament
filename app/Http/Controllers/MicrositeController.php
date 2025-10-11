<?php

namespace App\Http\Controllers;

use App\Models\Microsite;
use Illuminate\Http\Request;

class MicrositeController extends Controller
{
    public function show($slug)
    {
        $microsite = Microsite::withoutGlobalScopes()
            ->where('url', $slug)
            ->with(['doctor.showcases' => fn($query) => $query->withoutGlobalScopes()])
            ->firstOrFail();
        $groupedShowcases = $microsite->doctor->showcases->groupBy('media_type');

        return view(
            'microsite.show',
            ['microsite' => $microsite, 'groupedShowcases' => $groupedShowcases]
        );
    }
}