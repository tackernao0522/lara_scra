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

## mynavi_jobsデータをCSVに書き込む

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

        fwrite($file, "\xEF\xBB\xBF"); // 追加(文字化け対策)

        if (!fputcsv($file, ['id', 'url', 'title', 'company_name', 'features'])) {
            throw new \Exception('ヘッダーの書き込みに失敗しました。');
        };

        // 追加
        foreach (mynaviJob::all() as $job) {
            if (!fputcsv($file, [$job->id, $job->url, $job->title, $job->company_name, $job->features])) {
                throw new \Exception('データの書き込みに失敗しました。');
            }
        }

        fclose($file);
        // ここまで
    }
}
```

- `% php artisan scrape:mynavi`を実行(データがCSVに書き込まれているか確認)  

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
    const PAGE_NUM = 1; // 追加
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
        $this->saveJobs();
        $this->exportCsv();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        // 編集
        foreach (range(1, $this::PAGE_NUM) as $num) {
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

        fwrite($file, "\xEF\xBB\xBF");

        if (!fputcsv($file, ['id', 'url', 'title', 'company_name', 'features'])) {
            throw new \Exception('ヘッダーの書き込みに失敗しました。');
        };

        foreach (mynaviJob::all() as $job) {
            if (!fputcsv($file, [$job->id, $job->url, $job->title, $job->company_name, $job->features])) {
                throw new \Exception('データの書き込みに失敗しました。');
            }
        }

        fclose($file);
    }
}
```

## リファクタリング

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
    const PAGE_NUM = 2;
    const WAIT_TIME = 30;

    protected $signature = 'scrape:mynavi';
    protected $description = 'Scrape Mynavi';

    public function handle()
    {
        $this->truncateTables();
        $this->saveUrls();
        $this->saveJobs();
        $this->exportCsv();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        foreach (range(1, $this::PAGE_NUM) as $index => $num) {
            $urls = $this->getUrls($num);
            DB::table('mynavi_urls')->insert($urls);
            if ($index > 2) {
                break;
            }
            $this->wait();
        }
    }

    private function getUrls($num)
    {
        $url = $this::HOST . '/list/pg' . $num . '/';
        $crawler = \Goutte::request('GET', $url);
        return $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
            $href = $node->attr('href');
            $fullUrl = 'https:' . $href;
            $trimmedUrl = str_replace(['https://tenshoku.mynavi.jp', 'msg/'], '', $fullUrl);
            return [
                'url' => $trimmedUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
    }

    private function saveJobs()
    {
        foreach (MynaviUrl::all() as $mynaviUrl) {
            $job = $this->getJob($mynaviUrl);
            mynaviJob::create($job);
            $this->wait();
        }
    }

    private function getJob($mynaviUrl)
    {
        $url = $this::HOST . $mynaviUrl->url;
        $crawler = \Goutte::request('GET', $url);
        return [
            'url' => $url,
            'title' => $this->getTitle($crawler),
            'company_name' => $this->getCompanyName($crawler),
            'features' => $this->getFeatures($crawler),
        ];
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
        $file = $this->openFile();
        $this->writeHeader($file);
        $this->writeData($file);
        fclose($file);
    }

    private function openFile()
    {
        $file = fopen(storage_path($this::FILE_PATH), 'w');
        if (!$file) {
            throw new \Exception('ファイルの作成に失敗しました');
        }
        fwrite($file, "\xEF\xBB\xBF");
        return $file;
    }

    private function writeHeader($file)
    {
        if (!fputcsv($file, ['id', 'url', 'title', 'company_name', 'features'])) {
            throw new \Exception('ヘッダーの書き込みに失敗しました。');
        }
    }

    private function writeData($file)
    {
        foreach (mynaviJob::all() as $job) {
            if (!fputcsv($file, [$job->id, $job->url, $job->title, $job->company_name, $job->features])) {
                throw new \Exception('データの書き込みに失敗しました。');
            }
        }
    }

    private function wait()
    {
        sleep($this::WAIT_TIME);
    }
}
```
