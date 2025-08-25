# Starter Kit

This starter kit comes pre-configured with a modern Laravel 12 stack, including [Livewire v3](https://livewire.laravel.com/) and [Livewire Flux Pro](https://fluxui.dev), along with a curated set of official Laravel packages and developer tooling.


## Packages

### Laravel Boost
[Laravel Boost](https://github.com/laravel/boost) accelerates AI-assisted development by providing the essential context and structure that AI needs to generate high-quality, Laravel-specific code.


### Laravel Horizon
[Laravel Horizon](https://laravel.com/docs/12.x/horizon) provides a beautiful dashboard and code-driven configuration for managing [Redis](https://redis.io/) queues.  
It allows you to monitor throughput, runtime, job retries, and failures in real-time.

```bash
php artisan horizon
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


### Laravel Telescope
[Laravel Telescope](https://laravel.com/docs/12.x/telescope) is a debugging and monitoring assistant for your application.  
It provides insight into requests, exceptions, database queries, queued jobs, mail, notifications, cache operations, and more through a clean UI.

```bash
php artisan telescope:install
php artisan migrate
```


### PestPHP
[Pest](https://pestphp.com/) is a modern PHP testing framework with a focus on simplicity and developer experience.  
It’s built on top of PHPUnit, but offers a much cleaner and expressive syntax, making test writing faster and more enjoyable.

Run your test suite with:

```bash
./vendor/bin/pest
```

