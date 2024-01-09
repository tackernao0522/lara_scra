# CSVへの書き出し

## CSVへの書き出しの為の実装

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\mynaviJob;
use App\Models\MynaviUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp';
    const FILE_PATH = 'app/mynavi_jobs.csv'; // 追加
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
        // $this->truncateTables();
        // $this->saveUrls();
        // $this->saveJobs();
        $this->exportCsv(); // 追加
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
            $url = $this::HOST . '/list/pg' . $num . '/';
            $crawler = \Goutte::request('GET', $url);
            $urls = $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
                $href = $node->attr('href');
                $fullUrl = 'https:' . $href;
                $trimmedUrl = str_replace(['https://tenshoku.mynavi.jp', 'msg/'], '', $fullUrl);
                return [
                    'url' => $trimmedUrl,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            DB::table('mynavi_urls')->insert($urls);
            sleep(30);
        }
    }

    private function saveJobs()
    {
        foreach (MynaviUrl::all() as $mynaviUrl) {
            $url = $this::HOST . $mynaviUrl->url;
            $crawler = \Goutte::request('GET', $url);
            mynaviJob::create([
                'url' => $url,
                'title' => $this->getTitle($crawler),
                'company_name' => $this->getCompanyName($crawler),
                'features' => $this->getFeatures($crawler),
            ]);
            // break;
            sleep(30);
        }
    }

    private function getTitle($crawler)
    {
        return $crawler->filter('.occName')->text();
    }

    private function getCompanyName($crawler)
    {
        return $crawler->filter('.companyName')->text();
    }

    private function getFeatures($crawler)
    {
        $features = $crawler->filter('.cassetteRecruit__attribute.cassetteRecruit__attribute-jobinfo .cassetteRecruit__attributeLabel span')->each(function ($node) {
            return $node->text();
        });

        return implode(',', $features);
    }

    // 追加
    private function exportCsv()
    {
        $file = fopen(storage_path($this::FILE_PATH), 'w');
    }
    // ここまで
}
```

- `% php artisan scrape:mynavi`を実行(空のCSVができているか確認)  

## 例外処理

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\mynaviJob;
use App\Models\MynaviUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp';
    const FILE_PATH = 'app/mynavi_jobs.csv';
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
        // $this->truncateTables();
        // $this->saveUrls();
        // $this->saveJobs();
        $this->exportCsv();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
            $url = $this::HOST . '/list/pg' . $num . '/';
            $crawler = \Goutte::request('GET', $url);
            $urls = $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
                $href = $node->attr('href');
                $fullUrl = 'https:' . $href;
                $trimmedUrl = str_replace(['https://tenshoku.mynavi.jp', 'msg/'], '', $fullUrl);
                return [
                    'url' => $trimmedUrl,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            DB::table('mynavi_urls')->insert($urls);
            sleep(30);
        }
    }

    private function saveJobs()
    {
        foreach (MynaviUrl::all() as $mynaviUrl) {
            $url = $this::HOST . $mynaviUrl->url;
            $crawler = \Goutte::request('GET', $url);
            mynaviJob::create([
                'url' => $url,
                'title' => $this->getTitle($crawler),
                'company_name' => $this->getCompanyName($crawler),
                'features' => $this->getFeatures($crawler),
            ]);
            // break;
            sleep(30);
        }
    }

    private function getTitle($crawler)
    {
        return $crawler->filter('.occName')->text();
    }

    private function getCompanyName($crawler)
    {
        return $crawler->filter('.companyName')->text();
    }

    private function getFeatures($crawler)
    {
        $features = $crawler->filter('.cassetteRecruit__attribute.cassetteRecruit__attribute-jobinfo .cassetteRecruit__attributeLabel span')->each(function ($node) {
            return $node->text();
        });

        return implode(',', $features);
    }

    private function exportCsv()
    {
        $file = fopen(storage_path($this::FILE_PATH), 'w');

        if (!$file) {
            throw new \Exception('ファイルの作成に失敗しました');
        }
    }
}
```

## CSVヘッダー作成

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\mynaviJob;
use App\Models\MynaviUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp';
    const FILE_PATH = 'app/mynavi_jobs.csv';
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
        // $this->truncateTables();
        // $this->saveUrls();
        // $this->saveJobs();
        $this->exportCsv();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
            $url = $this::HOST . '/list/pg' . $num . '/';
            $crawler = \Goutte::request('GET', $url);
            $urls = $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
                $href = $node->attr('href');
                $fullUrl = 'https:' . $href;
                $trimmedUrl = str_replace(['https://tenshoku.mynavi.jp', 'msg/'], '', $fullUrl);
                return [
                    'url' => $trimmedUrl,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            DB::table('mynavi_urls')->insert($urls);
            sleep(30);
        }
    }

    private function saveJobs()
    {
        foreach (MynaviUrl::all() as $mynaviUrl) {
            $url = $this::HOST . $mynaviUrl->url;
            $crawler = \Goutte::request('GET', $url);
            mynaviJob::create([
                'url' => $url,
                'title' => $this->getTitle($crawler),
                'company_name' => $this->getCompanyName($crawler),
                'features' => $this->getFeatures($crawler),
            ]);
            // break;
            sleep(30);
        }
    }

    private function getTitle($crawler)
    {
        return $crawler->filter('.occName')->text();
    }

    private function getCompanyName($crawler)
    {
        return $crawler->filter('.companyName')->text();
    }

    private function getFeatures($crawler)
    {
        $features = $crawler->filter('.cassetteRecruit__attribute.cassetteRecruit__attribute-jobinfo .cassetteRecruit__attributeLabel span')->each(function ($node) {
            return $node->text();
        });

        return implode(',', $features);
    }

    private function exportCsv()
    {
        $file = fopen(storage_path($this::FILE_PATH), 'w');

        if (!$file) {
            throw new \Exception('ファイルの作成に失敗しました');
        }

        fputcsv($file, ['id', 'url', 'title', 'company_name', 'features']); // 追加
    }
}
```

- `% php artisan scrape:mynavi`を実行(CSVにヘッダーが作成してあるか確認)  

## 例外処理2

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\mynaviJob;
use App\Models\MynaviUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp';
    const FILE_PATH = 'app/mynavi_jobs.csv';
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
        // $this->truncateTables();
        // $this->saveUrls();
        // $this->saveJobs();
        $this->exportCsv();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
            $url = $this::HOST . '/list/pg' . $num . '/';
            $crawler = \Goutte::request('GET', $url);
            $urls = $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
                $href = $node->attr('href');
                $fullUrl = 'https:' . $href;
                $trimmedUrl = str_replace(['https://tenshoku.mynavi.jp', 'msg/'], '', $fullUrl);
                return [
                    'url' => $trimmedUrl,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            DB::table('mynavi_urls')->insert($urls);
            sleep(30);
        }
    }

    private function saveJobs()
    {
        foreach (MynaviUrl::all() as $mynaviUrl) {
            $url = $this::HOST . $mynaviUrl->url;
            $crawler = \Goutte::request('GET', $url);
            mynaviJob::create([
                'url' => $url,
                'title' => $this->getTitle($crawler),
                'company_name' => $this->getCompanyName($crawler),
                'features' => $this->getFeatures($crawler),
            ]);
            // break;
            sleep(30);
        }
    }

    private function getTitle($crawler)
    {
        return $crawler->filter('.occName')->text();
    }

    private function getCompanyName($crawler)
    {
        return $crawler->filter('.companyName')->text();
    }

    private function getFeatures($crawler)
    {
        $features = $crawler->filter('.cassetteRecruit__attribute.cassetteRecruit__attribute-jobinfo .cassetteRecruit__attributeLabel span')->each(function ($node) {
            return $node->text();
        });

        return implode(',', $features);
    }

    private function exportCsv()
    {
        $file = fopen(storage_path($this::FILE_PATH), 'w');

        if (!$file) {
            throw new \Exception('ファイルの作成に失敗しました');
        }

        if (!fputcsv($file, ['id', 'url', 'title', 'company_name', 'features'])) {
            throw new \Exception('ヘッダーの書き込みに失敗しました。');
        };
    }
}
```
