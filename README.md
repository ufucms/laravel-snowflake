# Laravel Snowflake

这个 Laravel 包生成 64 位标识符，就像 Twitter 中的雪花一样。

# Laravel 安装
```
composer require "ufucms/laravel-snowflake"

php artisan vendor:publish --provider="Ufucms\Snowflake\Providers\LaravelServiceProvider"
```

# Lumen 安装
- Install via composer
```
composer require "ufucms/laravel-snowflake"
```

- Bootstrap file changes
将以下代码段添加到 providers 部分下的 bootstrap/app.php 文件中，如下所示：
``` php
// Add this line
$app->register(Ufucms\Snowflake\Providers\LumenServiceProvider::class);
```

# Usage
Get instance
``` php
use Ufucms\Snowflake\Snowflake;

$snowflake = new Snowflake();
```
or
``` php
$snowflake = $this->app->make('Ufucms\Snowflake\Snowflake');
```
or
``` php
$snowflake = app('Ufucms\Snowflake\Snowflake');
```

Generate snowflake identifier
```
$id = $snowflake->nextId();
```
or
```
$id = $snowflake->id();
```
or
```
$id = Snowflake::nextId();
```
# Usage with Eloquent
将 `Ufucms\Snowflake\HasSnowflakePrimary` 特征添加到您的 Eloquent 模型中。
此特征使主键类型为`snowflake`。 Trait 会自动将 $incrementing 属性设置为 false。

``` php
<?php
namespace App;

use Ufucms\Snowflake\HasSnowflakePrimary;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasSnowflakePrimary, Notifiable;
}
```

Column type `id` is supported.

``` php
/**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}
```

# JavaScript support

由于 JavaScript 无法处理 64 位整数，因此还有 HasShortPrimary，它为 JavaScript 可以处理的 53 位整数创建 ID。

``` php
<?php
namespace App;

use Ufucms\Snowflake\HasShortPrimary;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasShortPrimary, Notifiable;
}
```

# Configuration
If `config/snowflake.php` not exist, run below:
```
php artisan vendor:publish
```

# Licence
[MIT licence](https://github.com/ufucms/laravel-snowflake/blob/master/LICENSE)
