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
            
            \Illuminate\Support\Facades\Log::info('Visitor Access', [
                'ip' => $ipAddress,
                'ips' => $request->ips(),
                'headers' => $request->headers->all(),
            ]);
            
            // Catat visitor, hanya jika IP belum tercatat hari ini
            try {
                Visitor::firstOrCreate([
                    'ip_address' => $ipAddress,
                    'visited_date' => $today
                ]);
            } catch (\Exception $e) {
                // Catat error jika terjadi masalah (misal constraint)
                \Illuminate\Support\Facades\Log::error('Gagal mencatat visitor', [
                    'ip' => $ipAddress,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $next($request);
    }
}
