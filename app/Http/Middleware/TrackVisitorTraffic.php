<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visitor;

class TrackVisitorTraffic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya record GET request
        if ($request->isMethod('GET')) {
            $ipAddress = $request->ip();
            $today = now()->toDateString();
            
            // Catat visitor, hanya jika IP belum tercatat hari ini
            try {
                Visitor::firstOrCreate([
                    'ip_address' => $ipAddress,
                    'visited_date' => $today
                ]);
            } catch (\Exception $e) {
                // Abaikan error misal terjadi race condition unique constraint
            }
        }

        return $next($request);
    }
}
