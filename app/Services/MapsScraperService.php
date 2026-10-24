<?php

namespace App\Services;

use App\Models\ScraperTask;
use App\Models\UmkmLead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class MapsScraperService
{
    /**
     * Run scraper for a given task
     */
    public function runTask(ScraperTask $task, int $limit = 30): void
    {
        $task->update([
            'status' => 'running',
            'last_run_at' => now(),
            'log_message' => 'Proses scraping dimulai...',
        ]);

        $scriptPath = base_path('scraper/google_maps_scraper.cjs');
        $nodePath = file_exists('/home/dii/.nvm/versions/node/v20.20.2/bin/node')
            ? '/home/dii/.nvm/versions/node/v20.20.2/bin/node'
            : 'node';

        $noWebFlag = ($task->filter_no_website ?? true) ? 'true' : 'false';
        $hasPhoneFlag = ($task->filter_has_phone ?? true) ? 'true' : 'false';
        $minReviews = $task->min_reviews ?? 5;
        $minRating = $task->min_rating ?? 0;

        $chromePath = file_exists('/opt/google/chrome/chrome')
            ? '/opt/google/chrome/chrome'
            : (file_exists('/usr/bin/google-chrome') ? '/usr/bin/google-chrome' : null);

        $locationParts = array_map('trim', explode(',', $task->location));
        $targetDistrict = null;
        if (count($locationParts) >= 3 && ! str_contains(strtolower($locationParts[0]), 'semua kecamatan')) {
            $targetDistrict = $locationParts[0];
        }

        $command = [
            $nodePath,
            $scriptPath,
            "--keyword={$task->keyword}",
            "--location={$task->location}",
            "--limit={$limit}",
            "--taskId={$task->id}",
            "--filterNoWebsite={$noWebFlag}",
            "--filterHasPhone={$hasPhoneFlag}",
            "--minReviews={$minReviews}",
            "--minRating={$minRating}",
        ];

        if ($targetDistrict) {
            $command[] = "--district={$targetDistrict}";
        }

        if ($chromePath) {
            $command[] = "--chromePath={$chromePath}";
        }

        try {
            $process = new Process($command, base_path(), [
                'PATH' => '/home/dii/.nvm/versions/node/v20.20.2/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
            ]);
            $process->setTimeout(3600); // 1 hour max

            $leadsCount = 0;
            $logs = [];
            $hasFatalError = false;
            $fatalErrorMessage = null;

            $process->run(function ($type, $buffer) use ($task, &$leadsCount, &$logs, &$hasFatalError, &$fatalErrorMessage) {
                $lines = explode("\n", trim($buffer));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || ! str_starts_with($line, '{')) {
                        continue;
                    }

                    $data = json_decode($line, true);
                    if (! $data || ! isset($data['type'])) {
                        continue;
                    }

                    if ($data['type'] === 'lead') {
                        $this->saveLead($task, $data);
                        $leadsCount++;
                        $task->update(['leads_count' => $leadsCount]);
                    } elseif ($data['type'] === 'progress') {
                        $task->update([
                            'progress_percent' => $data['percent'] ?? 0,
                            'current_step' => $data['step'] ?? null,
                            'eta_seconds' => $data['etaSeconds'] ?? null,
                        ]);
                    } elseif ($data['type'] === 'status') {
                        $logs[] = '['.now()->format('H:i:s').'] '.$data['message'];
                        if (count($logs) > 30) {
                            array_shift($logs);
                        }
                        $task->update(['log_message' => implode("\n", $logs)]);
                    } elseif ($data['type'] === 'error') {
                        $hasFatalError = true;
                        $fatalErrorMessage = $data['message'];
                        $logs[] = '['.now()->format('H:i:s').'] [ERROR] '.$data['message'];
                        $task->update(['log_message' => implode("\n", $logs)]);
                    }
                }
            });

            if ($process->isSuccessful() && ! $hasFatalError) {
                $task->update([
                    'status' => 'completed',
                    'progress_percent' => 100,
                    'current_step' => 'Scraping selesai!',
                    'eta_seconds' => 0,
                    'log_message' => ($task->log_message ? $task->log_message."\n" : '').'['.now()->format('H:i:s')."] SUKSES: Berhasil menyelesaikan scraping. Total {$leadsCount} UMKM potensial ditemukan & tersimpan.",
                ]);
            } else {
                $errorOutput = trim($process->getErrorOutput());
                $cleanError = $fatalErrorMessage ?: ($errorOutput ? Str::limit($errorOutput, 500) : 'Proses scraper terhenti atau mengalami kesalahan.');
                $task->update([
                    'status' => 'failed',
                    'current_step' => 'Gagal dieksekusi: '.$cleanError,
                    'eta_seconds' => 0,
                    'log_message' => ($task->log_message ? $task->log_message."\n" : '').'['.now()->format('H:i:s').'] GAGAL: '.$cleanError,
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('MapsScraperService Error: '.$e->getMessage());
            $task->update([
                'status' => 'failed',
                'log_message' => ($task->log_message ? $task->log_message."\n" : '').'['.now()->format('H:i:s').'] ERROR: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Save or update lead data
     */
    protected function saveLead(ScraperTask $task, array $data): void
    {
        $phoneWa = $data['phone_wa'] ?? null;
        $mapsUrl = $data['maps_url'] ?? null;

        // Cari duplikat berdasarkan phone_wa atau maps_url
        $existing = null;
        if ($phoneWa) {
            $existing = UmkmLead::where('phone_wa', $phoneWa)->first();
        }
        if (! $existing && $mapsUrl) {
            $existing = UmkmLead::where('maps_url', $mapsUrl)->first();
        }

        $leadData = [
            'task_id' => $task->id,
            'name' => $data['name'],
            'category' => $data['category'] ?? $task->keyword,
            'phone_raw' => $data['phone_raw'] ?? null,
            'phone_wa' => $phoneWa,
            'address' => $data['address'] ?? null,
            'review_count' => $data['review_count'] ?? 0,
            'rating' => $data['rating'] ?? null,
            'maps_url' => $mapsUrl,
            'has_website' => false,
            'website_url' => $data['website_url'] ?? null,
            'location_tag' => $data['location_tag'] ?? $task->location,
        ];

        if ($existing) {
            $existing->update($leadData);
        } else {
            $leadData['status'] = 'new';
            UmkmLead::create($leadData);
        }
    }
}
