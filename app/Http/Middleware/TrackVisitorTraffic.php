<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use App\Services\TelegramService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorTraffic
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya record GET request
        if ($request->isMethod('GET')) {
            $ipAddress = $request->header('CF-Connecting-IP')
                        ?? $request->header('X-Forwarded-For')
                        ?? $request->ip();

            if (str_contains($ipAddress, ',')) {
                $ipAddress = explode(',', $ipAddress)[0];
            }
            $ipAddress = trim($ipAddress);
            $today = now()->toDateString();

            Log::info('Visitor Access', [
                'ip' => $ipAddress,
                'ips' => $request->ips(),
                'headers' => $request->headers->all(),
            ]);

            // Abaikan request API atau static assets
            if ($request->is('api/*', 'assets/*', 'build/*', 'storage/*')) {
                return $next($request);
            }

            // Catat visitor, hanya jika IP belum tercatat hari ini
            try {
                $visitor = Visitor::firstOrCreate([
                    'ip_address' => $ipAddress,
                    'visited_date' => $today,
                ]);

                // Kirim notifikasi Telegram jika ini kunjungan baru
                if ($visitor->wasRecentlyCreated) {
                    try {
                        app(TelegramService::class)->sendVisitorNotification($ipAddress, $request->path());
                    } catch (\Throwable $te) {
                        Log::warning('Telegram visitor notification failed: '.$te->getMessage());
                    }
                }
            } catch (\Exception $e) {
                // Catat error jika terjadi masalah (misal constraint)
                Log::error('Gagal mencatat visitor', [
                    'ip' => $ipAddress,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $next($request);
    }
}
