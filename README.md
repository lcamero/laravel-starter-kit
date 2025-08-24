# Starter Kit

Laravel v12
Livewire v3
Livewire Flux Pro

## Packages

### Laravel Boost

### Laravel Horizon

```BASH
php artisan horizon
```

### Laravel Pail
### Laravel Pint

```BASH
./vendor/bin/pint 
```

### Laravel Socialite

Current providers are listed below. For the most part, all you need to configure is an ENV variable for the provider credentials in the correct format. Typically, these credentials may be retrieved by creating a "developer application" within the dashboard of the service you will be authenticating with.

Then, check configuration within `config/services.php` to review any required set up.

You may find more information in the [Laravel Socialite Documentation](https://laravel.com/docs/12.x/socialite)

#### Google Provider

Get your credentials in the [Auth Console](https://console.cloud.google.com/auth/overview)

```ENV
GOOGLE_CLIENT_ID="XXXXXXX"
GOOGLE_CLIENT_SECRET="XXXXXXX"
GOOGLE_REDIRECT_URL="http://starter-kit.com"
```

### Laravel Telescope
### PestPHP
