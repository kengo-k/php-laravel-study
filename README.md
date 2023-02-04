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

### Controllerを作成する

```
$ php artisan make:controller TestController

   INFO  Controller [app/Http/Controllers/TestController.php] created successfully.
```

## MVCによる画面表示

### ControllerからViewを表示する

作成したControllerにさらにViewを加えて画面を表示してみる。まずは作成したControllerを使うようにルーティングを追加する。

```php
use App\Http\Controllers\TestController;
Route::get('tests/test', [TestController::class, 'index']);
```

見たままの内容だが`tests/test`にアクセスされた際は`TestController`の`index`メソッドが呼び出されるようになる。自動生成されたControllerには`index`メソッドを定義していないので実装を追加する。

```php
class TestController extends Controller
{
    // 追加
    public function index()
    {
        return view('tests.test');
    }
}
```

`view`関数で指定している`tests.test`はViewディレクトリ(`resources/views`)内の`tests`サブディレクトリの`test`ファイルを表示する、という指定になる。`test`ファイルは前述した拡張子を持つためファイル名としては`test.blade.php`となる。とりあえず表示できることを確認できればよいので適当に中身をつくっておく。viewファイルを作成したらブラウザから`/tests/test`を指定して作成したViewの内容が表示されればOK。

### Modelからデータを取得しViewに表示する

上の例ではControllerが固定のViewを返していたため修正を加えてModel経由でデータベースの値を取得し、その値をViewに表示する。

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Test;

class TestController extends Controller
{
    // 追加
    public function index()
    {
        $values = Test::All();
        return view('tests.test', compact('values'));
    }
}
```

`view`関数の二番目の引数が追加されていることがわかる。この処理によりView内で`$values`変数を参照できるようになっている。ちなみに`compact`関数はPHPに組み込まれている関数でドキュメントによると以下の仕様となっている。

```
compact() は現在のシンボルテーブルにおいてその名前を有する変数を探し、 変数名がキー、変数の値がそのキーに関する値となるように追加します。
```

`$values`を参照するようにViewを修正する。

```php
<ul>
@foreach($values as $value)
  <li>ID: {{ $value->id }}</li>
  <li>Text: {{ $value->text }}</li>
@endforeach
</ul>
```

Testテーブルの内容が表示されればOK。

## データベース周りの処理(ORM)

上述したデータ取得処理では`Test::All()`によってTestテーブルの一覧を取得していたが、この処理はLaravelのORMである`Eloquent`のメソッド呼び出しとなる。`All`メソッドは`Collection`型を返す。実際に内容を確認してみる。

```
$values = Test::All();
// 取得したデータをdd関数に渡す
dd($values);
```
コントローラの処理で`$values`取得後に一行処理を追加した。これで画面を再表示すると下記の出力が得られる。

```php
Illuminate\Database\Eloquent\Collection {#719 ▼ // app/Http/Controllers/TestController.php:14
  #items: array:1 [▼
    0 => App\Models\Test {#1122 ▼
      ...省略...
      #attributes: array:4 [▼
        "id" => 1
        "text" => "Hello!"
        "created_at" => "2023-02-03 23:50:37"
        "updated_at" => "2023-02-03 23:50:37"
      ]
      ...省略...
    }
  ]
  #escapeWhenCastingToString: false
}
```

Eloquentで利用可能な他のメソッドを簡単に見ていく。

### 件数を取得

```php
$count = Test::count();
dd($count);
---
出力:
1 // app/Http/Controllers/TestController.php:15
```

### IDを指定して取得

```php
$first = Test::findOrFail(1);
dd($first);
---
出力:
App\Models\Test {#1364 ▼ // app/Http/Controllers/TestController.php:15
   ...省略...
  #attributes: array:4 [▼
    "id" => 1
    "text" => "Hello!"
    "created_at" => "2023-02-03 23:50:37"
    "updated_at" => "2023-02-03 23:50:37"
  ]
   ...省略...
}
```
※ちなみに存在しないIDを指定した場合はLaravelのエラー画面(404)になる模様。

## tinkerで簡単なDB操作を行う

`tinker`は対話型コマンドラインで簡単なDB操作を行うことができる。

```
$ php artisan tinker
Psy Shell v0.11.12 (PHP 8.1.2-1ubuntu2.10 — cli) by Justin Hileman
> $test = new App\Models\Test;
= App\Models\Test {#3745}

> $test->text = "Hello!";
= "Hello!"

> $test->save();
= true

> App\Models\Test::all();
= Illuminate\Database\Eloquent\Collection {#4441
    all: [
      App\Models\Test {#4695
        id: 1,
        text: "Hello!",
        created_at: "2023-02-03 23:50:37",
        updated_at: "2023-02-03 23:50:37",
      },
    ],
  }

> exit

   INFO  Goodbye.
```
