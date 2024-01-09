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
|url| | |
|created_at| | |
|updated_at| | |

### mynavi_jobs (詳細情報)

|Column|Type|Options|
|:------:|:----:|:-------:|
|id| | |
|url| | |
|title| | |
|company_name| | |
|features| | |
|created_at| | |
|updated_at| | |
