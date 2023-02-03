# Laravelのインストール

```
$ sudo apt-get install composer php-curl php-mysql
$ composer create-project laravel/laravel hello
$ sudo apt-get install php-xml # いらないかも？
$ composer require laravel/pint --dev # いらないかも

$ cd hello
$ composer update
$ php artisan serve --host 0.0.0.0 --port=8000
```

正常に起動しているように見えるので画面で動作確認してみるとエラー画面が表示される。

- `No application encryption key has been specified.`

上記エラーを解決するためにプロジェクトのルートディレクトリ(※`hello`)にて下記コマンドを実行する。

```
$ php artisan key:generate

   INFO  Application key set successfully.
```

再度画面にアクセスしたところ正常動作していると思われる画面が表示された。

# 初期設定

## タイムゾーンとロケールの設定

`confit/app.php`の`timezone`を変更する

```php
'timezone' => 'Asia/Tokyo',
'locale' => 'ja',
```

## デバッグバーのインストール

```
$ composer require barryvdh/laravel-debugbar:^3.7
```

インストール後にサーバを起動すると画面の下部にデバッグ用のツールバーが表示されていることが確認できる。デバッグバーをoffにする場合は`.env`の下記の値を切り替えること。

```
APP_DEBUG=true
```

基本的には`.env`を書き換えて画面をリロードすれば反映されるが、値を変更しても切り替わらない場合はキャッシュをクリアすること。

```
$ php artisan config:clear
$ php artisan cache:clear
```

## MySQLとPHPMyAdminのインストール

`database/docker-compose.yaml`を使用してインストールを行う。

```
$ docker-compose up -d
```

起動後、`http://localhost:4040`にアクセスしてPHPMyAdminの画面が表示されればOK。

## 開発用データベースとユーザを作成

PHPMyAdminから新規データベースとアクセス用ユーザを作成する。作成した内容に合わせて`.env`の該当項目を修正する。

- DB_DATABASE=hello
- DB_USERNAME=hello
- DB_PASSWORD=PassW0rd123

設定後、下記コマンドを実行しマイグレーションが正常に行えるかどうか確認する。

```
$ php artisan migrate
```

# Laravelの基本知識

## Routing

`routes/web.php`にルーティングの設定が記述されている。

```php
Route::get('/', function () {
    return view('welcome');
});
```

上記の記述から`/`にアクセスすると`welcome`のビューを表示しようとしていることがわかる。ビューは`resources/views`に存在する。ビューファイルは`welcome.blade.php`のように`*.blade.php`の拡張子を持つ。

## artisanコマンド

`php artisan list`で使用できるコマンドの一覧が確認できる

これまでに使用してきた`php artisan serve`や`php artisan migrate`なども表示されていることが確認できる。

### Modelを作成する

`php artisan make:model <モデル名>`でModelを生成できる。

```
$ php artisan make:model Test

   INFO  Model [app/Models/Test.php] created successfully.
```

※`php artisan make:model <モデル名> -mc`でマイグレーションとコントローラも同時に作成できる。

### Migrationを作成する

```
$ php artisan make:migration create_tests_table

   INFO  Migration [database/migrations/2023_02_03_233233_create_tests_table.php] created successfully.
```

ModelがTestならMigrationはsをつけて複数形になるようにする。生成されたファイルの中を見ると最小限のマイグレーション定義が記述されている。

```php
public function up()
{
   Schema::create('tests', function (Blueprint $table) {
      $table->id();
      $table->timestamps();
   });
}
```

`tests`テーブルに対し最低限のID列と作成日時/更新日時の列が定義されていることがわかる。ここに新しい列を定義してみる。

```php
public function up()
{
   Schema::create('tests', function (Blueprint $table) {
      $table->id();
      $table->string('text'); # 追加
      $table->timestamps();
   });
}
```

文字列型で`text`列を追加した。実際にマイグレーションを実行してみる。

```
$ php artisan migrate

   INFO  Running migrations.

  2023_02_03_233233_create_tests_table ..... 41ms DONE
```

PHPMyAdminから`tests`テーブルが追加されていることが確認できればOK。その他のコマンドとして下記の２つを使用する可能性があるのでここに記載しておく。

- php artisan migrate:fresh
- php artisan migrate:refresh

`fresh`はテーブルをすべて削除して再作成しなおす。`refresh`はロールバックしてから再生成する。
