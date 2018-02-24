#Laravel 5.5 API Authentication Using Passport

##STEP : 1  Create Laravel Application

>composer create-project --prefer-dist laravel/laravel auth-user

##STEP : 2 To get started, install Passport via the Composer package manager

>composer require laravel/passport

##STEP : 3  The Passport service provider registers its own database migration directory with the framework, so you should migrate your database after registering the provider.

>php artisan migrate

##STEP : 4 Install passport package

>php artisan passport:install

##STEP : 5  Use HasApiTokens in App\User model

```
<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
}
```
##STEP : 6 Call the Passport::routes in boot() on AuthServiceProvider with the help of use Laravel\Passport\Passport;

```
<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
```

##Finally, in your config/auth.php configuration file, you should set the driver option of the api authentication guard to passport. This will instruct your application to use Passport's TokenGuard when authenticating incoming API requests:

```
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```