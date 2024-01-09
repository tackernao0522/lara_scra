<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $url = 'https://tenshoku.mynavi.jp/list/pg3/';
        $crawler = \Goutte::request('GET', $url);
        $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
            $href = $node->attr('href');
            $fullUrl = 'https:' . $href;
            $trimmedUrl = str_replace(['https://tenshoku.mynavi.jp', 'msg/'], '', $fullUrl);
            dump($trimmedUrl);
        });
    }
}
