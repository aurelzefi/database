# Database

A small package that allows yout to easily interact with MySQL databases.

## Requirements

- PHP 7.4 or higher

## Installation

Clone this repo, add the package to your repositories and then require it:

```bash
git clone git@github.com:aurelzefi/database.git
```

```json
"repositories": [
    {
        "type": "path",
        "url": "./../database"
    }
]
```

```bash
composer require aurelzefi/database
```

## Usage

```php
use Aurel\Database\Database;
```

### Create A New Connection

```php
Database::setConfig([
    'database' => 'your-database',
    'host' => 'localhost',
    'username' => 'your-username',
    'password' => 'your-password',
]);
```

### Get Records

```php
Database::table('users')->get();
```

### Get A Single Record

```php
Database::table('users')->where('id = :id', ['id' => 1])->first();
```

### Select Specific Columns

```php
Database::table('users')->select('id', 'username')->get();
```

### Using Where Clauses

```php
Database::table('users')
    ->where('id = :id', ['id' => 1])
    ->orWhere('code is not null')
    ->get();
```

### Using Joins, Order By and Group By

```php
Database::table('users')
    ->select('users.*', 'posts.id post_id')
    ->innerJoin('posts on users.id = posts.user_id')
    ->orderBy('users.id', 'asc')
    ->orderBy('users.is_admin', 'desc')
    ->groupBy('users.id')
    ->get();
```

### Using Limit and Offset

```php
Database::table('users')->limit(10)->offset(10)->get();
```

### Get The Count

```php
Database::table('users')->count();
```

### Get Records As Instances Of A Given Class

```php
Database::table('users')->asInstancesOf(Model::class)->get();
```

### Using Pagination

```php
Database::table('users')->paginate();
Database::table('users')->paginate(20);
```

That will return an instance of `\Aurel\Database\Paginator` and you can get the underlying
items by calling `$users->items()`, or you can loop through the items directly:

```php
foreach ($users as $user) {
    echo $user->id;
}
```

### Store Data

```php
Database::table('users')->insert([
    'name' => 'Aurel Zefi',
    'email' => 'aurelzefi1994@gmail.com',
]);
```

### Store Data And Get the ID

```php
$id = Database::table('users')->insertGetId([
    'name' => 'Aurel Zefi',
    'email' => 'aurelzefi1994@gmail.com',
]);
```

### Update A Record

```php
Database::table('users')
    ->where('id = :id', ['id' => 1])
    ->update([
        'name' => 'Aurel Zefi',
        'email' => 'aurelzefi1994@gmail.com',
    ]);
```

### Delete A Record

```php
Database::table('users')->where('id = :id', ['id' => 1])->delete();
```

### Using Database Methods Directly

If the query you need is more complex than the query builder can handle, then just use the methods in the `Aurel\Database\Database` directly:

```php
Database::select('select * from users where id = :id', ['id' => 1]);
Database::selectOne('select * from users where id = :id', ['id' => 1]);
Database::insert('insert into users (name) values (:name)', ['name' => 'Aurel Zefi']);
Database::update('update users set name = :name where id = :id', ['id' => 1, 'name' => 'Aurel Zefi']);
Database::delete('delete from users where id = :id', ['id' => 1]);
```
