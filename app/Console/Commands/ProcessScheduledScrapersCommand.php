<?php

namespace App\Console\Commands;

use App\Models\ScraperTask;
use App\Services\MapsScraperService;
use Illuminate\Console\Command;

class ProcessScheduledScrapersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:process-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memproses tugas scraping yang sudah masuk jadwal';

    /**
     * Execute the console command.
     */
    public function handle(MapsScraperService $scraperService): int
    {
        $now = now();

        // 1. Tugas sekali jalan yang sudah waktunya
        $dueOnceTasks = ScraperTask::where('frequency', 'once')
            ->where('status', 'pending')
            ->where(function ($q) use ($now) {
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now);
            })
            ->get();

        // 2. Tugas harian yang belum jalan hari ini
        $dueDailyTasks = ScraperTask::where('frequency', 'daily')
            ->where(function ($q) use ($now) {
                $q->whereNull('last_run_at')
                    ->orWhere('last_run_at', '<=', $now->copy()->subDay());
            })
            ->where('status', '!=', 'running')
            ->get();

        // 3. Tugas mingguan yang belum jalan minggu ini
        $dueWeeklyTasks = ScraperTask::where('frequency', 'weekly')
            ->where(function ($q) use ($now) {
                $q->whereNull('last_run_at')
                    ->orWhere('last_run_at', '<=', $now->copy()->subWeek());
            })
            ->where('status', '!=', 'running')
            ->get();

        $allTasks = $dueOnceTasks->merge($dueDailyTasks)->merge($dueWeeklyTasks);

        if ($allTasks->isEmpty()) {
            $this->info('Tidak ada jadwal scraping yang perlu diproses saat ini.');

            return self::SUCCESS;
        }

        $this->info("Menemukan {$allTasks->count()} tugas scraping terjadwal.");

        foreach ($allTasks as $task) {
            $this->info("Menjalankan tugas #{$task->id}: {$task->keyword} di {$task->location}...");
            $scraperService->runTask($task, 30);
        }

        return self::SUCCESS;
    }
}
