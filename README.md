# Laravelのインストール

```
$ sudo apt-get install composer php-curl
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
