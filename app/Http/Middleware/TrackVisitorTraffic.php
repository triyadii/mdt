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
            // Jangan catat jika admin sedang login
            if (auth()->check()) {
                return $next($request);
            }

            // Abaikan request API, static assets, atau route admin
            if ($request->is(
                'api/*',
                'assets/*',
                'build/*',
                'storage/*',
                'dashboard*',
                'users*',
                'roles*',
                'company-profiles*',
                'abouts*',
                'keunggulans*',
                'partners*',
                'faqs*',
                'testimonials*',
                'jasas*',
                'project-types*',
                'portfolios*',
                'permohonans*',
                'beritas*',
                'chat-histories*',
                'profile*',
                'login*',
                'logout*',
                'favicon.ico',
                'robots.txt',
                'sitemap.xml',
                'up'
            )) {
                return $next($request);
            }

            $ipAddress = $request->header('CF-Connecting-IP')
                        ?? $request->header('X-Forwarded-For')
                        ?? $request->ip();

            if (str_contains($ipAddress, ',')) {
                $ipAddress = explode(',', $ipAddress)[0];
            }
            $ipAddress = trim($ipAddress);
            $today = now()->toDateString();

            $sessionKey = 'visitor_last_tracked_at';
            $lastTrackedAt = $request->hasSession() ? $request->session()->get($sessionKey) : null;

            // Jika sesi ini baru saja tercatat dalam 15 menit terakhir, jangan duplikat
            if ($lastTrackedAt && now()->diffInMinutes($lastTrackedAt) < 15) {
                return $next($request);
            }

            // Jika IP ini sudah tercatat kurang dari 2 menit yang lalu, cegah spam reload
            $hasRecentIp = Visitor::where('ip_address', $ipAddress)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->exists();

            if ($hasRecentIp) {
                return $next($request);
            }

            try {
                $visitor = Visitor::create([
                    'ip_address' => $ipAddress,
                    'visited_date' => $today,
                ]);

                if ($request->hasSession()) {
                    $request->session()->put($sessionKey, now());
                }

                // Kirim notifikasi Telegram
                try {
                    app(TelegramService::class)->sendVisitorNotification($ipAddress, $request->path());
                } catch (\Throwable $te) {
                    Log::warning('Telegram visitor notification failed: '.$te->getMessage());
                }
            } catch (\Exception $e) {
                Log::error('Gagal mencatat visitor', [
                    'ip' => $ipAddress,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $next($request);
    }
}
