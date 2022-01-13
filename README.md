# SoftExpires
A simple trait package for eloquent laravel/lumen which is useful for handling expired data automatically.

## Installation

Require this package, with [Composer](https://getcomposer.org/), in the root directory of your project. **Composer 2 is a must!**.

```bash
composer require andresuntoro/softexpires
```

## Configuration

You should add the "expired_at" column to your database table.

## Usage

Here you can see an example of you may use this package.
Just add ***AndreSuntoro\Database\Eloquent\SoftExpires;*** to your model and use it. You can combine with others trait too, for the example: ***SoftDeletes***.

```php
<?php
namespace App\Models;

use AndreSuntoro\Database\Eloquent\SoftExpires;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model {
    use SoftDeletes, SoftExpires;

    // ..

}

```

You can define when the data is considered expired. The data will not appear from the query results if it has reached the specified time limit, the default is 300 seconds(5 Mins).

```php
<?php
namespace App\Models;

use AndreSuntoro\Database\Eloquent\SoftExpires;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model {
    use SoftDeletes, SoftExpires;

    // in seconds, example 600 seconds (10 mins). The default is 300 seconds if not specified.
    const EXPIRED_AT_VALUE = 600; 

}

```

You can define your custom expired column if you don't use the default (expired_at).

```php
<?php
namespace App\Models;

use AndreSuntoro\Database\Eloquent\SoftExpires;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model {
    use SoftDeletes, SoftExpires;

    const EXPIRED_AT = 'my_expired_at';

}

```

Here you can see examples of using the available methods.

```php
use App\Models\Transaction;

// As noted above, soft expired models will automatically be excluded from query results. However, you may force soft expired models to be included in a query's results by calling the withExpired method on the query:
$trx = Transaction::withExpired()->get();

// The onlyExpired method will retrieve only soft deleted models:
$trx = Transaction::onlyExpired()->get();

// To restore a soft expired model, you may call the reset method on a model instance. The restore method will set the model's expired_at column to null or your specified datetime:
$trx = Transaction::find(1);
$trx->reset(); // Set to null
$trx->reset(date('Y-m-d H:i:s')); // Set to your desired date time value


```
