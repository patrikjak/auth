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
        "@php artisan vendor:publish --tag=config --force",
    ]
}
```

It will publish config file every time you update your composer packages.

Laravel cannot merge multidimensional arrays in config files.

## General

You can choose your custom User model by define `AUTH_MODEL` in your `.env` file.

```env
AUTH_MODEL=App\Models\User
```

By default `Patrikjak\Auth\Models\User` model is used.

Also you can change the default user repository implementation. You need to change in `config/pjauth.php` file.

```php
'repositories' => [
    'user' => \Patrikjak\Auth\Repositories\UserRepository::class,
],
```

## Routes
In routes, we use default laravel middleware group `web` and `guest` middleware.

```php
Route::middleware(['web', 'guest']);
```

## Migrations

You should publish the migrations:

```bash
php artisan vendor:publish --tag="migrations"
```

## Roles
- How to insert default roles?

You can insert default roles by running the following command:

```bash
php artisan php artisan seed:user-roles --enum=Patrikjak\\Auth\\Models\\RoleType
```

Enum is default `Patrikjak\Auth\Models\RoleType` enum class. You can create your own enum class and pass it as an argument.
It must use `Patrikjak\Utils\Common\Traits\EnumValues` trait.