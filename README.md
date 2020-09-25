# Laravel API with JSON Web Token

## Step 1: Install Laravel using Composer

```bash
laravel new laravel-api
```

Then navigate to `laravel-api` directory. Open this project in your favorite text editor or IDE like Visual Studio Code/vim/Sublime Text or PHPStorm.

## Step 2: Update Database credentials in environmental file

Now open the `.env` file and change following:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

## Step 3: Install JWT package

In command line interface (CLI) enter following command:

```bash
composer require tymon/jwt-auth:dev-develop --prefer-source
```

## Step 4: Publish the vendor

Enter following command, select `Tymon\JWTAuth\Providers\LaravelServiceProvider` and hit enter:

```bash
php artisan vendor:publish
```

It will copy jwt.php file from `/vendor/tymon/jwt-auth/config/config.php` to `/config` directory.

## Step 5: Generate JWT secret key

Enter the following command:

```bash
php artisan jwt:secret
```

## Step 6: Use `Authenticate` middleware inside the `routeMiddleware` array of Kernel.php

Now open the Kernel php file from the `app/http` folder and add following code inside the `routeMiddleware` array:

```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /* ... */
    protected $routeMiddleware = [
        /* ... */
        'auth.jwt' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    ];
}
?>
```

## Step 7: Define routes for the api

Now we need to use some route for the api endpoinds. For this open `api.php` file inside the routes api:

```php
<?php

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');

Route::group(
    ['middleware' => 'auth.jwt'],
    function () {
        Route::get('logout', 'AuthController@logout');
        Route::resource('tasks', 'TaskController');
    }
);
?>

```

## Step 8: Set up JWT authentication in User model

Now open the User model from the Models directory and add `impolements` keyword after the `extend` keyword then use `JWTSubject` interface. And don't forget to import it.

```php
<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
/** ... **/

class User extends Authenticatable implements JWTSubject
{
?>
```

Now inside the User class add two required functions: `getJWTIdentifier` and `getJWTCustomClaims`:

```php
<?php
public function getJWTIdentifier() {
    return $this->getKey();
}

public function getJWTCustomClaims() {
    return [];
}
?>
```

## Step 9: Create AuthController

To create `AuthController` enter following command in CLI:

```bash
php artisan make:controller AuthController
```

Now if you navigate to `app/Http/Controllers` directory you will see a new file called `AuthController`. Open it and create a new public variable/property `$loginAfterSignUp` and set it to true.
Then make a method called `login`. Use Request class as an argument. Then inside the method define `$credentials` variable and set it to `$request->only('email', 'password')`.
Create another variable called `$token` and set it to null. Now use an if statement to check if `JWTAUTH::attempt($credentials)` does match with `$token` or not. If it fails then we are showing a JSON response that the user is unauthenticated.
To use JWTAuth we need to import it. So we can write `use JWTAUTH` or `use Tymon\JWTAuth\Facades\JWTAuth` on top of the file.

Now create another method called `register`. It will take Request class as an argument. We need to validate user inputs so that user don't forget to enter required details to register.
We also trying to validate unique email, so that email does not populate with more duplicate emails. For the password validation we are saying that the minimum length of the password should be 6 and maximum is 10.
After that we are instantiate User model with `$user` variable. Then we are assigning name, email and password property with request property. We are using `Hash` facades to encryping user password and storing it to database using `$user->save()` method.

Then we are checking if `$loginAfterSignUp` is truthy, if is it then we are returning the token from the login method.

And after that we are displaying a JSON response with status of true and the created user.

Next we are going to create a logout method with Request argument. In our logout method we are validating if token is present in the request, because we will be sending `token` parameters in our get method whenever we try to access authenticated API endpoints.
Then inside the try block we are invalidating our token then showing a JSON response saying that the user has been successfully logged out.

And inside the catch block if anything goes wrong we will show an error message that the user cannot be logged out.

## Step 10: Fixing namespace issue in route for the Laravel 8.x version

At the moment of writing this steps, the version of Laravel is 8.x there are lots of changes happened. If you are using latest version of Laravel, you will see all models are now resides in `Models` directory. There are more significant changes happened. If you want to know more about Laravel changes you can follow this [release note](https://laravel.com/docs/8.x/releases#laravel-8).
Another issue is that in Laravel 8.x the namespace in `RouteServiceProvider` is null by default. So if you are trying to use AuthController in your api route it will not find the specified class because namespace is null by default. If you want to use it, you need to manually type the class name and prefix with the namespace.
So, better option is you can open `RouteServiceProvider` and create a new property called `namespace` and assign the value with `App\Http\Controller` and its done. So, whenever you try to access any controller you don't need to worry about this problem.
