<?php

namespace App\Console\Commands;

use App\Models\berita;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Clock\now;

class NewsScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:news-scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus berita yang sudah di tong sampang selama 30 hari';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::today()->toDateString();
        berita::onlyTrashed()->whereDate('target_delete' ,'<=' ,  $now)->forceDelete();
    }
}
