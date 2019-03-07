Connecting to the database

```php
\Database\Database::setConfig([
    'database' => 'your-database',
    'host' => 'localhost',
    'username' => 'your-username',
    'password' => 'your-password',
]);
```

Using the Query Builder

Get multiple records
```php
$users = \Database\Database::table('users')->get();
```

Get a single record
```php
$user = \Database\Database::table('users')->where('id = :id', ['id' => 1])->first();
```

Specifying columns to select
```php
$users = \Database\Database::table('users')->select('id', 'username')->get();
```

Using Wheres
```php
$users = \Database\Database::table('users')
                ->where('id = :id', ['id' => 1])
                ->orWhere('code is not null')
                ->get();
```

Using Joins, Order By, Group By
```php
$users = \Database\Database::table('users')
                ->select('users.*', 'posts.id post_id')
                ->innerJoin('posts on users.id = posts.user_id') // the same for leftJoin() and crossJoin()
                ->orderBy('users.id', 'asc')
                ->orderBy('users.is_admin', 'desc')
                ->groupBy('users.id')
                ->get();
```

Using Limit and Offset
```php
$users = \Database\Database::table('users')->limit(10)->offset(10)->get();
```

Get the count of the selected records
```php
\Database\Database::table('users')->count();
```

Get records as instances of a given class
```php
$users = \Database\Database::table('users')->asInstancesOf(Model::class)->get();
```

Paginating Query Builder Results
```php
$users = \Database\Database::table('users')->paginate();
$users = \Database\Database::table('users')->paginate(20);
```
That will return an instance of ```\Database\Paginator``` and you can get the underlying
items by calling ```$users->items()```, or you can loop through the items directly:
```php
foreach ($users as $user) {
    echo $user->id;
}
```

Store data
```php
\Database\Database::table('users')->insert([
    'name' => 'Aurel Zefi',
    'email' => 'aurelzefi1994@gmail.com',
]);
```

Store data and get the id
```php
$id = \Database\Database::table('users')->insertGetId([
    'name' => 'Aurel Zefi',
    'email' => 'aurelzefi1994@gmail.com',
]);
```

Update a record
```php
\Database\Database::table('users')
    ->where('id = :id', ['id' => 1])
    ->update([
        'name' => 'Aurel Zefi',
        'email' => 'aurelzefi1994@gmail.com',
    ]);
```

Delete a record
```php
\Database\Database::table('users')->where('id = :id', ['id' => 1])->delete();
```

If the query you need is more complex than the query builder can handle, then just use the methods in the Database directly:
```php
\Database\Database::select('select * from users where id = :id', ['id' => 1]);
\Database\Database::selectOne('select * from users where id = :id', ['id' => 1]);
\Database\Database::insert('insert into users (name) values (:name)', ['name' => 'Aurel Zefi']);
\Database\Database::update('update users set name = :name where id = :id', ['id' => 1, 'name' => 'Aurel Zefi']);
\Database\Database::delete('delete from users where id = :id', ['id' => 1]);
