# Laravel 5.5 API Authentication Using Passport

## STEP : 1  Create Laravel Application

>composer create-project --prefer-dist laravel/laravel auth-user

## STEP : 2 To get started, install Passport via the Composer package manager

>composer require laravel/passport

## STEP : 3  The Passport service provider registers its own database migration directory with the framework, so you should migrate your database after registering the provider.

>php artisan migrate 
>php artisan make:auth

## STEP : 4 Install passport package

>php artisan passport:install

## STEP : 5  Use HasApiTokens in App\User model

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
## STEP : 6 Call the Passport::routes in boot() on AuthServiceProvider with the help of use Laravel\Passport\Passport;

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

## STEP : 7 In your config/auth.php configuration file, you should set the driver option of the api authentication guard to passport. This will instruct your application to use Passport's TokenGuard when authenticating incoming API requests:

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

## STEP : 8 Create API Route in routes/api.php

### Here we will add new API routes to access the laravel application and following routes are help us to make web service using a passport.

```
Route::post('user-login','UserController@userLogin');
Route::post('user-registration', 'UserController@userRegistration');
Route::group(['middleware' => 'auth:api'], function(){
    //auth user routes
    Route::get('userDetails','UserController@userDetails');

});

```

## STEP : 9  Create Controller & Methods

```
<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
   public function userLogin(Request $request)
   {
       $validator = Validator::make($request->all(),[
            'email' => 'required|max:255',
            'password' => 'required'
       ]);

       if ($validator->fails()){
           return response()->json([
               'error' => true,
               'meassage' => $validator->errors(), 
               'status_code' => 400
           ], 400);
       }

       if(Auth::attempt(['email' => request('email'), 'password' => request('password')] )){
           
           $user = Auth::user();
           $response['token'] = $user->createToken('MyApp')->accessToken;
           
           return response()->json([
                'error' => false,
                'data' => $response, 
                'status_code' => 200
           ], 200);

       }else{

        return response()->json([
            'error' => true,
            'meassage' => 'Unauthorised', 
            'status_code' => 400
       ], 400);

       }
   }

   public function userRegistration(Request $request)
   {

        $validator = Validator::make($request->all(),[
                'name' => 'required|max:255',
                'email' => 'required|email',
                'password' => 'required'
        ]);

        if ($validator->fails()){
         
            return response()->json([
                'error' => true,
                'meassage' => $validator->errors(), 
                'status_code' => 400
            ], 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        
        $user = User::create($input);
        $response['token'] = $user->createToken('MyApp')->accessToken;
        $response['name'] = $user->name;

            return response()->json([
                'error' => false,
                'data' => $response, 
                'status_code' => 200
            ], 200);
        }

    public function userDetails()
    {
        $user = User::get();
        return response()->json([
            'error' => false,
            'data' => $user, 
            'status_code' => 200
        ], 200);
    }
}

```