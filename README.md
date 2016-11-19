Simple Router
================================================================================
[![Latest Stable Version](https://poser.pugx.org/jacko/router/v/stable.svg)](https://packagist.org/packages/jacko/router)
[![Total Downloads](https://poser.pugx.org/jacko/router/downloads.svg)](https://packagist.org/packages/jacko/router)
[![License](https://poser.pugx.org/jacko/router/license.svg)](https://packagist.org/packages/jacko/router)


How to Install
--------------------------------------------------------------------------------
Installation via composer is easy:

	composer require jacko/router:dev-master

How to Use
--------------------------------------------------------------------------------
Write into your index.php:

```php
require('vendor/autoload.php');

//
$router = new Jacko\Router();
$router->path = 'config/routes.php';
$router->start();
```

An Example Route
--------------------------------------------------------------------------------
Place into your config/routes.php
```php
Route::get('/', 'HomeController@index');
Route::any('/page', 'HomeController@page');
Route::post('/ajax', 'HomeController@ajax');

Route::get('/user/{id}', function($id) {
    var_dump($id);
});

Route::get('/{any}', 'HomeController@error404');
```
