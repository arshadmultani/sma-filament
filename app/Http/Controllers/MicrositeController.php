<?php

namespace App\Http\Controllers;

use App\Models\Microsite;
use Illuminate\Http\Request;

class MicrositeController extends Controller
{
    public function show($slug)
    {
        $microsite = Microsite::where('url', $slug)->firstOrFail();

        return view('microsite.show', compact('microsite'));
    }
}