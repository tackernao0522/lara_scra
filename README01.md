# スクレイピング講座

- __目的__  
会社情報、求人情報を収集する  
日本で人気なサイトをひとつ以上選んで、PHPブログラムを作成し、  
会社情報、求人情報を収集する。  
見積もりの時、目標サイトごとにして下さい。  
[例] mynavi 14日 30万円 / doda.jp 10日 20万円 ...  
[納品物] ソース テーブル設計SQL&Excel  

- __製作手順__  

1. 用件整理/仕様策定  
2. 技術選定  
3. 設計  
4. 実装  
5. 納品  

## 1. 用件整理  

- 求人サイトから会社、求人情報を集める  
  ->マイナビ転職をスクレイピング  

- ソースコード、テーブル設計SQL、Excelを納品  
  ->ソースコード、CSVを納品  

## 1. 仕様策定

- 求人ページのURL一覧を取得する  

- 各求人情報を取得する  

- CSVで求人情報を出力する  

- データは毎回削除し新規保存する  

## スクレイピングの注意事項

- robots.txtは事前に確認  

- サーバアクセスの間隔を1秒以上空けるように(robots.txtでもし制限が書いていない場合)  

- 用途 : 個人や家族間で使用、情報解析、Web検索サービスの提供  

## 2. 技術選定

- App : Laravel  

- DB : MySQL  

- Scraping : Goutte  

## 3. 設計 : システム構成図

(マイナビ転職) <--①-- (Laravel) <--②--> (MySQL)  
                        ③  
                        ↓
                     　(CSV)  

## 3. 設計 : テーブル設計

- __参考URL__ : <https://tenshoku.mynavi.jp/>  

`転職・求人情報から探す`で`この条件で探す`をクリックする  
ページネーション部分をクリックしてみる  
<https://tenshoku.mynavi.jp/list/pg2/?jobsearchType=4&searchType=8>のURLになり  
<https://tenshoku.mynavi.jp/list/pg3/>に変更すると3ページ目になると思われる  
タイトルをクリックすると詳細ページに遷移する  
まずは求人情報の一覧を集めたい  
その為にまずはテーブル設計を始める  

## テーブル設計

### mynavi_urls (求人情報URL一覧)

|Column|Type|Options|
|:------:|:----:|:-------:|
|id| | |
|url|string| |
|created_at| | |
|updated_at| | |

### mynavi_jobs (詳細情報)

|Column|Type|Options|
|:------:|:----:|:-------:|
|id| | |
|url|string| |
|title|string| |
|company_name|string| |
|features|text| |
|created_at| | |
|updated_at| | |

## goutteのインストール

- `% composer require weidner/goutte`を実行  

`config/app.php`を編集  

```php:app.php
<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        \Weidner\Goutte\GoutteServiceProvider::class, // 追加

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
        'Goutte' => \Weidner\Goutte\GoutteFacade::class, // 追加

];
```

## スクレイピング実行用コマンドの作成

- `% php artisan make:command ScrapeMynavi`を実行  

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapeMynavi.php
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
    protected $signature = 'scrape:mynavi'; // 編集

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape Mynavi'; // 編集

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo 10 . PHP_EOL; // 追加(動作確認用)
        return Command::SUCCESS;
    }
}
```

- `% php artisan list`を実行するとコマンドの詳細を確認できる  

```:terminal
 scrape
  scrape:mynavi          Scrape Mynavi
```

- `% php artisan scrape:mynavi`を実行  

```terminal
10
```

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapeMynavi.php
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
        // 編集
        $crawler = \Goutte::request('GET', 'https://duckduckgo.com/html/?q=Laravel');
        $crawler->filter('.result__title .result__a')->each(function ($node) {
            dump($node->text());
        });
        // ここまで
    }
}
```

- `% php artisan scrape:mynavi`を実行  

```:terminal
"Laravel - The PHP Framework For Web Artisans" // app/Console/Commands/ScrapeMynavi.php:32
"GitHub - laravel/laravel: Laravel is a web application framework with ..." // app/Console/Commands/ScrapeMynavi.php:32
"Laravel - The PHP Framework For Web Artisans" // app/Console/Commands/ScrapeMynavi.php:32
"Laravel - Wikipedia" // app/Console/Commands/ScrapeMynavi.php:32
"What Is Laravel, And How Do You Get Started with It? - How-To Geek" // app/Console/Commands/ScrapeMynavi.php:32
"The Laravel Framework · GitHub" // app/Console/Commands/ScrapeMynavi.php:32
"The Laravel PHP Framework - Web App Construction for Everyone - Kinsta" // app/Console/Commands/ScrapeMynavi.php:32
"Laravel Tutorial: The Ultimate Guide (2023) - Mastering Backend" // app/Console/Commands/ScrapeMynavi.php:32
"What is Laravel? | DigitalOcean" // app/Console/Commands/ScrapeMynavi.php:32
"Laravel Bootcamp" // app/Console/Commands/ScrapeMynavi.php:32
"An Introduction to the Laravel PHP Framework — SitePoint" // app/Console/Commands/ScrapeMynavi.php:32
"How To Install Laravel on Windows, macOS, and Linux - Kinsta®" // app/Console/Commands/ScrapeMynavi.php:32
"Laravel Tutorial: What It is, Framework, Features - Javatpoint" // app/Console/Commands/ScrapeMynavi.php:32
"Learn Laravel PHP Framework - W3Schools" // app/Console/Commands/ScrapeMynavi.php:32
"Laravel Tutorial for Beginners - Guru99" // app/Console/Commands/ScrapeMynavi.php:32
"Want To Be a Laravel Developer? Here's Everything You Need To Know - Kinsta" // app/Console/Commands/ScrapeMynavi.php:32
"Laravel Herd" // app/Console/Commands/ScrapeMynavi.php:32
"Laracasts: Laravel 8 From Scratch" // app/Console/Commands/ScrapeMynavi.php:32
"Building Web Applications From Scratch With Laravel" // app/Console/Commands/ScrapeMynavi.php:32
"Laravel - GeeksforGeeks" // app/Console/Commands/ScrapeMynavi.php:32
"Laravel Tutorial - Online Tutorials Library" // app/Console/Commands/ScrapeMynavi.php:32
```

## robots.txtの確認

`https://tenshoku.mynavi.jp/robots.txt`にアクセスする  

```txt:robots.text
User-agent: *
Sitemap: https://tenshoku.mynavi.jp/sitemap/sitemap_index.xml
Disallow: /a/
Disallow: /b/
Disallow: /c/
Disallow: /client/
Disallow: /entry/
Disallow: /healthcheck/
Disallow: /help/client/
Disallow: /jobset/
Disallow: /manage/
Disallow: /mt4/
Disallow: /o/
Disallow: /rss/
Disallow: /private/
Disallow: /return/
Disallow: /setting/
Disallow: /sonet/
Disallow: /useset/
Disallow: /centuserset/
Disallow: /info/
Disallow: /question/form/
Disallow: /job/form/
Disallow: /url-forwarder/
Disallow: /bookmark/
Disallow: /help/form/
Disallow: /ajax/*
Allow: /ajax/get-dynamic-related-link
Disallow: /plst/admin/
Disallow: /plst/client/
Disallow: /authenticate/
Disallow: /login-bookmarkConfirmation/
Disallow: /republish/
Disallow: /login-republishConfirmation/
Disallow: /fw/
User-agent: bingbot
Crawl-delay: 30 <!-- 30秒は空けること -->
```

## 一覧URLの取得

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapeMynavi.php
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
        // 編集
        $url = 'https://tenshoku.mynavi.jp/list/pg3/';
        $crawler = \Goutte::request('GET', $url);
        $crawler->filter('.result__title .result__a')->each(function ($node) {
            dump($node->text());
        });
        // ここまで
    }
}
```

## 取得したい部分のhtml

hrefの中のURLを取得したい  

```html:sample.html
<p class="cassetteRecruit__copy boxAdjust">
      <a class="js__ga--setCookieOccName" target="_blank" href="//tenshoku.mynavi.jp/jobinfo-209712-1-129-1/">【事務スタッフ】未経験歓迎♪在宅勤務あり♪関東/東海/関西募集</a>
      <span class="labelEmploymentStatus">正社員</span>
</p>
```

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapeMynavi.php
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
        // 編集
        $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
            dump($node->attr('href'));
        });
        // ここまで
    }
}
```

- `% php artisan scrape:mynavi`を実行  

```:terminal
groovy@groovy-no-MBP scraping_prac % php artisan scrape:mynavi
"//tenshoku.mynavi.jp/jobinfo-90887-1-352-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-155724-1-2-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-203477-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-233094-1-28-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-209249-1-15-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-200119-1-57-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-223003-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-350812-1-1-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-351373-1-1-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-4844-1-289-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-293432-1-11-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-347866-1-2-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-209712-1-135-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-339535-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-256551-1-11-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-310965-1-2-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-323672-1-12-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-212905-1-157-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-100465-1-186-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-332225-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-297967-1-283-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-340933-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-314472-1-5-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-111025-1-147-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-250792-1-92-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-257635-1-130-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-332980-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-170136-1-14-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-343434-1-10-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-326951-1-7-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-209712-1-134-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-197313-1-174-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-321112-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-311669-1-7-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-224262-1-49-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-188089-1-172-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-315030-1-7-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-139563-1-232-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-150886-1-8-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-128275-1-111-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-135431-1-298-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-223512-1-13-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-344556-1-5-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-134413-1-359-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-270707-1-105-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-239456-1-17-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-170601-1-361-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-212905-1-159-1/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-351739-1-2-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
"//tenshoku.mynavi.jp/jobinfo-329417-1-2-1/msg/" // app/Console/Commands/ScrapeMynavi.php:33
```

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapeMynavi.php
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
        // 編集
        $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
            $href = $node->attr('href');
            $fullUrl = 'https:' . $href;
            $trimmedUrl = str_replace(['https://tenshoku.mynavi.jp', 'msg/'], '', $fullUrl);
            dump($trimmedUrl);
        });
        // ここまで
    }
}
```

- `% php artisan scrape:mynavi`を実行  

```:terminal
"/jobinfo-90887-1-352-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-155724-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-203477-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-233094-1-28-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-209249-1-15-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-200119-1-57-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-223003-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-350812-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-351373-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-4844-1-289-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-293432-1-11-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-347866-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-209712-1-135-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-339535-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-256551-1-11-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-310965-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-323672-1-12-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-212905-1-157-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-100465-1-186-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-332225-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-297967-1-283-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-340933-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-314472-1-5-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-111025-1-147-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-250792-1-92-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-257635-1-130-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-332980-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-170136-1-14-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-343434-1-10-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-326951-1-7-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-209712-1-134-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-197313-1-174-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-321112-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-311669-1-7-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-224262-1-49-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-188089-1-172-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-315030-1-7-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-139563-1-232-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-150886-1-8-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-128275-1-111-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-135431-1-298-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-223512-1-13-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-344556-1-5-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-134413-1-359-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-270707-1-105-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-239456-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-170601-1-361-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-212905-1-159-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-351739-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:36
"/jobinfo-329417-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:36
```

## テーブル及びモデルの作成

`% php artisan make:model MynaviUrl`を実行  

`% php artisan make:migration create_mynavi_urls_table --create=mynavi_urls`を実行  

`database/migrations/create_mynavi_urls_table.php`を編集  

```php:create_mynavi_urls_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mynavi_urls', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mynavi_urls');
    }
};
```

- `% php artisan migrate`を実行  

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapeMynavi.php
<?php

namespace App\Console\Commands;

use Carbon\Carbon; // 追加
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // 追加

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
        // 編集
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
        // ここまで
    }
}
```

- `% php artisan scrape:mynavi`を実行  

## スクレイピング実行時はデータベースのデータを削除してから実行されるようにする(truncate)

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapeMynavi.php
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
        // 編集
        $this->truncateTables();
        $this->saveUrls();
    }

    // 追加
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
    // ここまで
}
```

- `% php artisan scrape:mynavi`を実行  

## 全てのページのURL一覧を取得する(現状ではpage3のみの50件分しか取得していない)

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapeMynavi.php
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
        // 追加 取り敢えず1ページ目〜2ページ目まで実行する書き方
        foreach (range(1, 2) as $num) {
            $url = 'https://tenshoku.mynavi.jp/list/pg' . $num . '/';
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
            sleep(30); // 必ず入れる
        }
    }
}
```

`% php artisan scrape:mynavi`を実行  

## 求人情報用モデル及びテーブルの作成(mynavi_jobs)

- `% php artisan make:model mynaviJob`を実行  

`app/Models/mynaviJob.php`を編集  

```php:mynaviJob.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mynaviJob extends Model
{
    use HasFactory;

    protected $guarded = []; // 追記
}
```

`database/migrations/create_mynavi_jobs_table.php`を編集  

```php:mynavi_jobs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mynavi_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('title');
            $table->string('company_name');
            $table->text('features');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mynavi_jobs');
    }
};
```

- `% php artisan migrate`を実行  

## 求人情報のスクレイピングの実装

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
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
        // $this->truncateTables(); // 取り敢えずこっちは動かないようにしておく
        $this->saveUrls();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate(); // 追加
    }

    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
            $url = 'https://tenshoku.mynavi.jp/list/pg' . $num . '/';
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
            // sleep(30);
        }
    }
}
```

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\MynaviUrl; // 追加
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
        // $this->truncateTables();
        // $this->saveUrls(); コメントアウトしておく
        $this->saveJobs(); // 追加
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
            $url = 'https://tenshoku.mynavi.jp/list/pg' . $num . '/';
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
            // sleep(30);
        }
    }

    // 追加
    private function saveJobs()
    {
        foreach (MynaviUrl::all() as $mynaviUrl) {
            $url = $mynaviUrl->url;
            dump($url);
        }
    }
    // ここまで
}
```

- `% php artisan scrape:mynavi`を実行  

```:terminal
"/jobinfo-345749-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-94446-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-325812-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-350810-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-134413-1-358-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-311669-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-161969-1-22-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-288996-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-115059-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-194596-1-27-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-350562-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-102553-1-123-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-111025-1-150-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-285227-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-350857-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-209712-1-130-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-201893-1-8-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-105609-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-170136-1-13-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-142336-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-104359-1-69-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-331154-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-189878-1-12-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-36728-1-68-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-331239-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-264297-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-177344-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-303174-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-121217-1-83-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-293882-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-332593-1-15-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-170601-1-360-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-352750-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-259912-1-14-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-46275-1-35-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-351634-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-348644-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-97461-1-51-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-352454-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-346410-1-5-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-187155-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-351909-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-323163-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-300676-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-188493-1-128-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-135431-1-298-3/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-340399-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-345749-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-94446-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-325812-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-350810-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-134413-1-358-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-311669-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-161969-1-22-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-288996-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-115059-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-194596-1-27-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-350562-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-102553-1-123-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-111025-1-150-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-285227-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-350857-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-209712-1-130-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-201893-1-8-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-105609-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-170136-1-13-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-142336-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-104359-1-69-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-331154-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-189878-1-12-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-36728-1-68-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-331239-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-264297-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-177344-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-303174-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-121217-1-83-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-293882-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-332593-1-15-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-170601-1-360-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-352750-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-259912-1-14-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-46275-1-35-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-351634-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-348644-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-97461-1-51-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-352454-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-346410-1-5-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-187155-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-351909-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-323163-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-300676-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-188493-1-128-1/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-135431-1-298-3/" // app/Console/Commands/ScrapeMynavi.php:69
"/jobinfo-340399-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:69
```

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\MynaviUrl;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp'; // 追加
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
        $this->saveJobs();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
            $url = 'https://tenshoku.mynavi.jp/list/pg' . $num . '/';
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
            // sleep(30);
        }
    }

    private function saveJobs()
    {
        foreach (MynaviUrl::all() as $mynaviUrl) {
            $url = $this::HOST . $mynaviUrl->url; // 編集
            dump($url);
        }
    }
}
```

- `% php artisan scrape:mynavi`を実行  

```:terminal
"https://tenshoku.mynavi.jp/jobinfo-345749-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-94446-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-325812-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-350810-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-134413-1-358-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-311669-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-161969-1-22-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-288996-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-115059-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-194596-1-27-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-350562-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-102553-1-123-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-111025-1-150-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-285227-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-350857-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-209712-1-130-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-201893-1-8-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-105609-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-170136-1-13-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-142336-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-104359-1-69-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-331154-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-189878-1-12-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-36728-1-68-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-331239-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-264297-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-177344-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-303174-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-121217-1-83-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-293882-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-332593-1-15-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-170601-1-360-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-352750-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-259912-1-14-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-46275-1-35-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-351634-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-348644-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-97461-1-51-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-352454-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-346410-1-5-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-187155-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-351909-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-323163-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-300676-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-188493-1-128-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-135431-1-298-3/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-340399-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-345749-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-94446-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-325812-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-350810-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-134413-1-358-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-311669-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-161969-1-22-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-288996-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-115059-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-194596-1-27-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-350562-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-102553-1-123-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-111025-1-150-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-285227-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-350857-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-209712-1-130-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-201893-1-8-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-105609-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-170136-1-13-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-142336-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-104359-1-69-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-331154-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-189878-1-12-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-36728-1-68-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-331239-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-264297-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-177344-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-303174-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-121217-1-83-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-293882-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-332593-1-15-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-170601-1-360-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-352750-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-259912-1-14-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-46275-1-35-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-351634-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-348644-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-97461-1-51-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-352454-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-346410-1-5-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-187155-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-351909-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-323163-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-300676-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-188493-1-128-1/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-135431-1-298-3/" // app/Console/Commands/ScrapeMynavi.php:70
"https://tenshoku.mynavi.jp/jobinfo-340399-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:70
```

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\MynaviUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp';
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
        $this->truncateTables(); // 一旦解除して試す
        $this->saveUrls(); // 一旦解除して試す
        $this->saveJobs();
    }

    private function truncateTables()
    {
        DB::table('mynavi_urls')->truncate();
        DB::table('mynavi_jobs')->truncate();
    }

    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
            $url = $this::HOST . '/list/pg' . $num . '/'; // 編集
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
            dump($url);
        }
    }
}
```

- `% php artisan scrape:mynavi`を実行  

```terminal
groovy@groovy-no-MBP scraping_prac % php artisan scrape:mynavi 
"https://tenshoku.mynavi.jp/jobinfo-345749-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-94446-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-325812-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-350810-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-134413-1-358-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-311669-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-161969-1-22-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-288996-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-115059-1-36-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-194596-1-27-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-350562-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-102553-1-123-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-111025-1-150-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-285227-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-350857-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-209712-1-130-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-201893-1-8-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-105609-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-170136-1-13-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-142336-1-25-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-104359-1-69-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-331154-1-6-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-189878-1-12-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-36728-1-68-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-331239-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-264297-1-9-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-177344-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-303174-1-4-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-121217-1-83-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-293882-1-16-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-332593-1-15-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-170601-1-360-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-352750-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-259912-1-14-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-46275-1-35-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-351634-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-348644-1-3-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-97461-1-51-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-352454-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-346410-1-5-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-187155-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-351909-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-323163-1-2-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-300676-1-1-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-188493-1-128-1/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-135431-1-298-3/" // app/Console/Commands/ScrapeMynavi.php:69
"https://tenshoku.mynavi.jp/jobinfo-340399-1-17-1/" // app/Console/Commands/ScrapeMynavi.php:69
```

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\MynaviUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp';
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
        $this->saveJobs();
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
            // 編集
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
            // ここまで
        }
    }
}
```

- __取得したい箇所__  

```html:sample.html
<h1>
    <span class="occName">※★年休126日＆10連休実績有【サポート事務】学歴不問/全国募集</span> <!--occNameのテキストを取得したい (title)-->
    <span class="companyName">株式会社コプロコンストラクション</span>
    <span class="companyNameAdd">育休取得率100％｜土日祝休｜有給平均取得10.95日｜転勤なし</span>
</h1>
```

`app/Console/Commands/ScrapeMynavi.php`を編集  

```php:ScrapingMynavi.php
<?php

namespace App\Console\Commands;

use App\Models\MynaviUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp';
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
        $this->saveJobs();
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
            dump($crawler->filter('.occName')->text()); // 編集
            break; // 一件取得したら抜ける
            sleep(30);
        }
    }
}
```

- `% php artisan scrape:mynavi`を実行  

```:terminal
"新店舗計画中！モスバーガーの【店舗スタッフ】★月給27万円～" // app/Console/Commands/ScrapeMynavi.php:70
```

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
        $this->saveJobs();
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
            // 編集
            mynaviJob::create([
                'url' => $url,
                'title' => $this->getTitle($crawler),
                'company_name' => '',
                'features' => '',
            ]);
            // ここまで
            break;
            sleep(30);
        }
    }

    // 追加
    private function getTitle($crawler)
    {
        return $crawler->filter('.occName')->text();
    }
    // ここまで
}
```

- `% php artisan scrape:mynavi`を実行(mynavi_jobsテーブルにデータが入ったか確認する)  

- __会社名を取得したい__  

```html:sample.html
<h1>
    <span class="occName">※★年休126日＆10連休実績有【サポート事務】学歴不問/全国募集</span>
    <span class="companyName">株式会社コプロコンストラクション</span> <!-- ここを取得したい -->
    <span class="companyNameAdd">育休取得率100％｜土日祝休｜有給平均取得10.95日｜転勤なし</span>
</h1>
```

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
        $this->saveJobs();
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
                'company_name' => $this->getCompanyName($crawler), // 編集
                'features' => '',
            ]);
            break;
            sleep(30);
        }
    }

    private function getTitle($crawler)
    {
        return $crawler->filter('.occName')->text();
    }

    // 追加
    private function getCompanyName($crawler)
    {
        return $crawler->filter('.companyName')->text();
    }
    // ここまで
}
```

- `% php artisan scrape:mynavi`を実行(company_nameにデータが入るか確認)  

- __featuresを取得したい__  

```html:sample.html
<ul class="cassetteRecruit__attribute cassetteRecruit__attribute-jobinfo"> <!-- まずここのクラス名を取得したい -->
    <li class="cassetteRecruit__attributeLabel"> <!-- 次にこのクラスを取得  -->
        <span class="labelEmploymentStatus">正社員</span> <!-- 最終的にここのテキストを取得したい -->
    </li>
    <li class="cassetteRecruit__attributeLabel">
        <span class="labelCondition">300万～500万円</span>
    </li>
    <li class="cassetteRecruit__attributeLabel">
        <span class="labelCondition">職種・業種未経験OK</span>
    </li>

    <li class="cassetteRecruit__attributeLabel">
        <span class="labelCondition">学歴不問</span>
    </li>
    <li class="cassetteRecruit__attributeLabel">
        <span class="labelCondition">第二新卒歓迎</span>
    </li>
    <li class="cassetteRecruit__attributeLabel">
        <span class="labelCondition">転勤なし</span>
    </li>
    <li class="cassetteRecruit__attributeLabel">
        <a href="/woman/"><span class="labelWoman">女性のおしごと掲載中</span></a>
    </li>
</ul>
```

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
        $this->saveJobs();
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
                'features' => $this->getFeatures($crawler), // 編集
            ]);
            break;
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

    // 追加
    private function getFeatures($crawler)
    {
        $features = $crawler->filter('.cassetteRecruit__attribute.cassetteRecruit__attribute-jobinfo .cassetteRecruit__attributeLabel span')->each(function ($node) {
            return $node->text();
        });

        return implode(',', $features);
    }
    // ここまで
}
```

- `% php artisan scrape:mynavi`を実行(データベースに入っているか確認)  
