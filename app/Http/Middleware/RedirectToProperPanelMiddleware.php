<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Pages\Dashboard;
use Filament\Facades\Filament;
use Symfony\Component\HttpFoundation\Response;

class RedirectToProperPanelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $currentPanel = Filament::getCurrentPanel()?->getId();

        // If user is not in their allowed panel, kick them to the right one
        if ($currentPanel !== $user->panelId()) {
            return redirect()->to($user->panelRoute());
        }

        // Already in correct panel
        return $next($request);
    }
}
