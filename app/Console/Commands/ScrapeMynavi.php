<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:mynavi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape Mynavi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->truncateTables();
        $this->saveUrls();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
    }

    private function saveUrls()
    {
        $url = 'https://tenshoku.mynavi.jp/list/pg3/';
        $crawler = \Goutte::request('GET', $url);
        $urls = $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
            $href = $node->attr('href');
            $fullUrl = 'https:' . $href;
            $trimmedUrl = str_replace(['https://tenshoku.mynavi.jp', 'msg/'], '', $fullUrl);
            return [
                'url' => $trimmedUrl,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        });

        DB::table('mynavi_urls')->insert($urls);
    }
}
