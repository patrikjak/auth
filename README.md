# Auth

Simple auth package for laravel apps.

## Installation

Install the package via Composer:

```bash
composer require patrikjak/auth
```

## Setup
After installing the package, add the package provider to the providers array in bootstrap/providers.php.

```php
use Patrikjak\Auth\AuthServiceProvider;
 
return [
    ...
    AuthServiceProvider::class,
];
```

You need to have installed and configured `patrikjak/utils` package.

After that you need to publish the package assets (if you configured `patrikjak/utils` package, you don't need to publish assets again):

```bash
php artisan vendor:publish --tag="assets" --force
```

You should publish the config file:

```bash
php artisan vendor:publish --tag="config" --force
```

or if you want to publish views:

```bash
php artisan vendor:publish --tag="views" --force
```

If you don't publish config file, you will miss all features of this package. I recommend add this script to your `composer.json` file:

```json
"scripts": {
    "post-update-cmd": [
        "@php artisan vendor:publish --tag=config --force"
    ]
}
```

It will publish config file every time you update your composer packages.

Laravel cannot merge multidimensional arrays in config files.


## Routes
In routes, we use default laravel middleware group `web` and `guest` middleware.

```php
Route::middleware(['web', 'guest']);
```