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
