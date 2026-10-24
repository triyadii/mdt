<?php

namespace App\Console\Commands;

use App\Models\ScraperTask;
use App\Services\MapsScraperService;
use Illuminate\Console\Command;

class RunMapsScraperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:run 
                            {taskId? : ID tugas scraping} 
                            {--keyword= : Kategori atau kata kunci (contoh: cafe)} 
                            {--location= : Daerah atau kecamatan (contoh: Medan Selayang)} 
                            {--limit=30 : Batas maksimal leads}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Jalankan scraper Google Maps untuk mencari UMKM tanpa website';

    /**
     * Execute the console command.
     */
    public function handle(MapsScraperService $scraperService): int
    {
        $taskId = $this->argument('taskId');

        if ($taskId) {
            $task = ScraperTask::find($taskId);
            if (! $task) {
                $this->error("Tugas dengan ID {$taskId} tidak ditemukan.");

                return self::FAILURE;
            }
        } else {
            $keyword = $this->option('keyword') ?? 'cafe';
            $location = $this->option('location') ?? 'Medan';

            $task = ScraperTask::create([
                'title' => "Scrape {$keyword} di {$location}",
                'keyword' => $keyword,
                'location' => $location,
                'frequency' => 'once',
                'status' => 'pending',
            ]);
        }

        $limit = (int) $this->option('limit');
        $this->info("Menjalankan tugas #{$task->id}: {$task->keyword} di {$task->location} (Limit: {$limit})...");

        $scraperService->runTask($task, $limit);

        $task->refresh();
        $this->info("Tugas selesai dengan status: {$task->status}. Total leads: {$task->leads_count}");

        return self::SUCCESS;
    }
}
