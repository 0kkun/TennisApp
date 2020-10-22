#  README : About this application

## 概要
- ユーザーの好みに合わせてテニスの情報をまとめることができるアプリ
- お気に入り選手・ブランドを登録することで、それに基づいた最新ニュース・動画を表示する
- 大会情報・最新ランキングを他サイトからスクレイピングして表示する

## 本番環境
- デプロイ先：AWS (予定)
- リンク：
- テストユーザーアカウント

> **ID :** admin@mail.com
> **PASS :** secret

## 制作背景
- 色々なサイトにアクセスして情報を集める手間を省き、効率よく必要とする情報を閲覧できるようにするため

## 工夫したポイント
- スクレイピング技術を使用し、複数サイトからの情報を保管できるようにした
- google apiを用いて動画情報を検索・取得できるようにした
- レスポンシブデザインを採用

## 使用技術
### サーバーサイド
- Laravel
- PHP
- MySQL
- google api
- scraping
- Repository pattern

### フロントエンド
- HTML/CSS
- Bootstrap
- javascript
- jquery
- Chart.js (予定)

## 今後実装したい機能
- 選手のランキング推移をチャートで表示
- 大会ページからチャットページに遷移しチャットができる機能

# DB設計

## usersテーブル
|Column|Type|Options|
|------|----|-------|
|name|string|null: false|
|email|string|null: false, unique: true|
|email_verified_at|string|-|
|password|string|null: false|

## playersテーブル
|Column|Type|Options|
|------|----|-------|
|name_jp|string|unique: true|
|name_en|string|unique: true|
|country|string|-|
|wiki_url|string|-|
|age|integer|-|
### Association
- has_many :favorite_players

## favorite_playersテーブル
|Column|Type|Options|
|------|----|-------|
|user_id|integer|null: false, foreign_key: true|
|player_id|integer|null: false, foreign_key: true|
### Association
- belongs_to :users
- belongs_to :players

## brandsテーブル
|Column|Type|Options|
|------|----|-------|
|name_jp|string|unique: true|
|name_en|string| - |
|country|string|-|
### Association
- has_many :favorite_brands

## favorite_brandsテーブル
|Column|Type|Options|
|------|----|-------|
|user_id|integer|null: false, foreign_key: true|
|brand_id|integer|null: false, foreign_key: true|
### Association
- belongs_to :users
- belongs_to :brands

## tour_informationsテーブル
|Column|Type|Options|
|------|----|-------|
|name|string|unique: true|
|category|string| - |
|location|string|-|
|surface|string|-|
|draw_num|string|-|
|year|year|unique: true|
|start_date|date|-|
|end_date|date|-|

## news_articlesテーブル
|Column|Type|Options|
|------|----|-------|
|title|string|unique: true|
|url|string| - |
|post_time|date|unique: true|

## brand_news_articlesテーブル
|Column|Type|Options|
|------|----|-------|
|title|string|unique: true|
|url|string| - |
|brand_name|string| - |
|post_time|date|unique: true|

## youtube_videosテーブル
|Column|Type|Options|
|------|----|-------|
|title|string|unique: true|
|url|string| - |
|post_time|date|-|
|player_id|integer|null: false, foreign_key: true|

# 開発コマンド一覧
- 選手の情報をwikipediaから取得

```
php artisan command:scrapePlayers
```
- 最新のATPランキングを取得
```
php artisan command:scrapeATPRankings
```
- Youtube動画を取得
```
php artisan command:getPlayersYoutube
```
- 大会情報を取得
```
php artisan command:scrapeTourInfo
```
- ブランドのニュースを取得
```
php artisan command:scrapeBrandNews
```
- テニスのニュースを取得
```
php artisan command:scrapeTennisNews
```
- リポジトリパターン用のファイル作成
```
php artisan make:repository モデル名
```

# 環境構築

## ローカルにリポジトリをクローン

```
$ git clone https://github.com/0kkun/TennisApp.git 
```

## Docker build

```
make build
```

## .envファイルを編集

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=password
```
## configディレクトリのdatabase.phpを編集

```
'port' => env('DB_PORT', '3306'),
'database' => env('DB_DATABASE', 'laravel'),
'username' => env('DB_USERNAME', 'root'),
'password' => env('DB_PASSWORD', 'password'),
```

## Sequel ProなどでDBに接続

```
ホスト : 0.0.0.0
ポート : 13306
ユーザー名 : root
パスワード : password
```

## Docker up

```
make up
```
- ローカル環境で確認
> http://localhost:10080/
