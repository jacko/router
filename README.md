Simple Router
================================================================================
[![Latest Stable Version](https://poser.pugx.org/jacko/router/v/stable)](https://packagist.org/packages/jacko/router)
[![Latest Unstable Version](https://poser.pugx.org/jacko/router/v/unstable)](https://packagist.org/packages/jacko/router)
[![Total Downloads](https://poser.pugx.org/jacko/router/downloads)](https://packagist.org/packages/jacko/router)
[![License](https://poser.pugx.org/jacko/router/license)](https://packagist.org/packages/jacko/router)


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
Check your .htaccess, it's must looking like that:
```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .* index.php [L]
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

Dependency Injection in your Controllers
--------------------------------------------------------------------------------
Please, make sure that your Models contains call methods like all(), first(), etc
```php
class HomeController
{
	public function __construct(User $user, Order $order)
	{
		$this->user = $user;
		$this->order = $order;
	}
	
	public function page(Page $page)
	{
		$users = $this->user->all();
		$home = $page->first();
		
		var_dump($users, $home);
	}
}
```
