# Laravel Starter Kit

This starter kit comes pre-configured with a modern Laravel 12 stack, including [Livewire v3](https://livewire.laravel.com/) and [Livewire Flux](https://fluxui.dev), along with a curated set of Laravel packages and developer tooling.

It is created on top of the official [Laravel Livewire Starter Kit](https://github.com/laravel/livewire-starter-kit) and has been extended and modified to suit custom requirements.

---

## Steps to install

You may get started by running the following command

```bash
laravel new my-app --using=lcamero/laravel-starter-kit
```

Or, if you prefer, use the composer create-project command instead

```bash
composer create-project lcamero/laravel-starter-kit
```

You will be asked if you wish to install a Flux UI pro license after the project is created so it configures it right away. Otherwise, you may activate it later with the following command

```bash
php artisan flux:activate
```

---

## Setup

You should be able to start developing right away without making any modifications, but you may want to tune certain settings depending on the needs of your project. All packages that come pre-installed are enabled by default, so if you wish to manage some of them you may follow these instructions.

### Enable / Disable Two-Factor Authentication (2FA)

This starter kit uses [Laravel Fortify](https://laravel.com/docs/12.x/fortify) to manage two-factor authentication, which is enabled by default. If you wish to enable/disable it in your project set the following environment variable in your `.env` file

```env
TWO_FACTOR_AUTH_ENABLED=true
# or
TWO_FACTOR_AUTH_ENABLED=false
```

This will hide the settings views and skip the redirects in the login flow.

### Configure API Tokens Feature

This starter kit uses [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum) for API Token management, which is enabled by default.

Within the `Providers/Auth/SanctumServiceProvider.php` file you may enable or disable the feature. 

Also, you're able to configure the available and default permissions that exist for your application.

```php
Sanctum::enableApiTokens();
// or
Sanctum::enableApiTokens(false);

// Permission that will be selected for new tokens
Sanctum::defaultPermissions([
    'read',
]);

// Available permissions for your tokens
Sanctum::permissions([
    'create',
    'read',
    'update',
    'delete',
]);
```

### Enable / Disable Laravel Socialite

This starter kit uses [Laravel Socialite](https://laravel.com/docs/12.x/socialite) to handle OAuth authentication with different providers.

Currently the following providers are available, as long as you configure the provide the environment keys:

- Google

To disable this authentication method, remove or comment the appropriate section within `config/services.php`

```php
// 'google' => [
//     'client_id' => env('GOOGLE_CLIENT_ID'),
//     'client_secret' => env('GOOGLE_CLIENT_SECRET'),
//     'redirect' => env('GOOGLE_REDIRECT_URL').'/auth/google/callback',
// ],
```

More providers can be configured depending on the project needs, the starter kit just comes with the boilerplate for Google at this time.

### Enable / Disable Laravel Pulse

Set the following environment variable in your `.env` file

```env
PULSE_ENABLED=true
# or
PULSE_ENABLED=false
```

### Enable / Disable Laravel Telescope

Set the following environment variable in your `.env` file

```env
TELESCOPE_ENABLED=true
# or
TELESCOPE_ENABLED=false
```

### Enable / Disable Laravel Debugbar

Set the following environment variable in your `.env` file

```env
APP_DEBUG=true
# or
APP_DEBUG=false
```

---

## Packages Included

A series of packages have been pre-installed and configured to solve common needs in projects. Here's a breakdown of what's included:

### Production

### Laravel Fortify
[Laravel Fortify](https://laravel.com/docs/12.x/fortify) is a frontend agnostic authentication backend for Laravel. It provides features like two-factor authentication, registration, password reset, and email verification, giving you full control over the user interface.

This package was added mainly to enable 2 Factor Authentication (2FA) to the application and integrate it into the register/login flows, including the Laravel Socialite implementation.

### Laravel Horizon
[Laravel Horizon](https://laravel.com/docs/12.x/horizon) provides a beautiful dashboard and code-driven configuration for managing [Redis](https://redis.io/) queues.  
It allows you to monitor throughput, runtime, job retries, and failures in real-time.

```bash
php artisan horizon
```

### Laravel Pennant
[Laravel Pennant](https://laravel.com/docs/12.x/pennant) is a simple and light-weight feature flag package. Feature flags enable you to incrementally roll out new application features with confidence, A/B test new interface designs, complement a trunk-based development strategy, and much more.

### Laravel Pulse
[Laravel Pulse](https://laravel.com/docs/12.x/pulse) is a lightweight, real-time application performance monitoring tool.
It provides a dashboard to track key metrics such as application performance, slow queries, job throughput, and system resource usage, helping you identify bottlenecks and optimize your application.

Most Pulse recorders will automatically capture entries based on framework events dispatched by Laravel. However, the servers recorder and some third-party cards must poll for information regularly. To use these cards, you must run the check daemon on all of your individual application servers

```bash
php artisan pulse:check
```

After deployment:
```bash
php artisan pulse:restart
```

You can disable Laravel Pulse by adding the following environment variable to your `.env` file:

```env
PULSE_ENABLED=false
```

### Laravel Sanctum
[Laravel Sanctum](https://laravel.com/docs/12.x/sanctum) provides a featherweight authentication system for SPAs (single-page applications), mobile applications, and simple, token-based APIs. It allows your application to issue API tokens to your users, which may be used to authenticate API requests.

This starter kit has configured a Sanctum service that allows you to define "abilities" that can be assigned to tokens so you're free to configure them for your app. These abilities act as scopes, restricting the actions a token can perform.

The abilities for this application are defined in the `app/Providers/Auth/SanctumServiceProvider.php` file, on the `boot()` method. By default, no abilities are available to be assigned, but you can uncomment the code to get started:

```php
Sanctum::permissions([
    // 'create',
    // 'read',
    // 'update',
    // 'delete',
]);
```

You may configure default permissions that should be added to all new entities by calling the `defaultPermissions` method.

```php
Sanctum::defaultPermissions([
    // 'read',
]);
```

If you wish to enable/disable the API Tokens feature you may configure the following in the service provider

```php
Sanctum::enableApiTokens();
// or
Sanctum::enableApiTokens(false);
```

### Laravel Scout
[Laravel Scout](https://laravel.com/docs/12.x/scout) provides a simple, driver-based solution for adding full-text search to your Eloquent models. Using model observers, Scout will automatically keep your search indexes in sync with your Eloquent records.

You can import your models using the `scout:import` command:
```bash
php artisan scout:import "App\Models\User"
```

For large datasets, you can queue the import operation for better performance:
```bash
php artisan scout:queue-import "App\Models\User" --chunk=500
```

To remove all of a model's records from the search index, you can use the `scout:flush` command:
```bash
php artisan scout:flush "App\Models\User"
```

This starter kit is configured to use the `database` driver for Scout, which uses "where like" clauses and full text indexes (if configured per column) to filter results from your existing database. You can change the driver and queue settings in your `.env` file:

```env
SCOUT_DRIVER=database
SCOUT_QUEUE=true
```

### Laravel Socialite
[Laravel Socialite](https://laravel.com/docs/12.x/socialite) provides an expressive, fluent interface for OAuth authentication with popular providers like Google, Facebook, GitHub, and more.  

Current providers are listed below. For the most part, all you need to configure is an environment variable for the provider credentials in the correct format. Typically, these credentials may be retrieved by creating a "developer application" within the dashboard of the service you will be authenticating with.

Check `config/services.php` to review any further required setup.

#### Google Provider
Get your credentials in the [Google Cloud Auth Console](https://console.cloud.google.com/auth/overview).

```env
GOOGLE_CLIENT_ID="XXXXXXX"
GOOGLE_CLIENT_SECRET="XXXXXXX"
GOOGLE_REDIRECT_URL="http://starter-kit.com"
```

### Development

### Laravel Boost
[Laravel Boost](https://github.com/laravel/boost) accelerates AI-assisted development by providing the essential context and structure that AI needs to generate high-quality, Laravel-specific code.

### Laravel Debugbar
[Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) is a development package that adds a toolbar to your application, giving you real-time insights into queries, routes, views, logs, and performance. It helps you debug and profile your Laravel app directly in the browser. 

It can be paired with Laravel's Telescope package to gain insight of your application.

You may disable this package by setting the following environment variable in your `.env` file

```env
APP_DEBUG=false
```

### Laravel Pail
[Laravel Pail](https://laravel.com/docs/12.x/pail) is a developer tool for tailing your application logs directly from the terminal.  
It provides better insight into application events and errors without having to manually read log files.

```bash
php artisan pail
```

### Laravel Pint
[Laravel Pint](https://laravel.com/docs/12.x/pint) is an opinionated PHP code style fixer for Laravel projects.  
It helps enforce consistent code formatting using [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) under the hood.

```bash
./vendor/bin/pint
```

### Laravel Telescope
[Laravel Telescope](https://laravel.com/docs/12.x/telescope) is a debugging and monitoring assistant for your application.  
It provides insight into requests, exceptions, database queries, queued jobs, mail, notifications, cache operations, and more through a clean UI.

```bash
php artisan telescope:install
php artisan migrate
```

You can disable Laravel Telescope by adding the following environment variable to your `.env` file:

```env
TELESCOPE_ENABLED=false
```

### PestPHP
[Pest](https://pestphp.com/) is a modern PHP testing framework with a focus on simplicity and developer experience.  
It’s built on top of PHPUnit, but offers a much cleaner and expressive syntax, making test writing faster and more enjoyable.

Run your test suite with:

```bash
./vendor/bin/pest
```