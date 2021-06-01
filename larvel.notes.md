Install default starting point with users and auth
---
Web version
```
    php artisan breeze:install
```
React version
``` 
    php artisan breeze:install react
    npm install && npm run dev
    php artisan migrate
```

Useful syntax to check global variables (config can use env)
---
```
    Config::get('app.locale');
    env('JP_CLIENT_PASSWORD');
    App::environment(['dev', 'local'])

```

Routes. Resolve model and dependency injection using column other than id(primary key)
---
```
    use App\Models\Post;

    Route::get('/posts/{post:slug}', function (Post $post) {
        return $post;
    });
```

Routes. Define global pattern for route variables in boot() method of class App\Providers\RouteServiceProvider class
---
```
    public function boot()
    {
        Route::pattern('id', '^[1-9]{1}[0-9]*?$'); // Integers greater than 0
        Route::patterns(['uuid' => '^[1-9]{1}[0-9]*?$']);
        ...
    }
```

Routes. Rate Limiters To Routes
---
```
    1. Define rate limiters in configureRateLimiting() method of the App\Providers\RouteServiceProvider class.
    2. Attach Rate limiters to routes or route groups using the throttle middleware.
    Route::middleware(['throttle:uploads'])->group(function () {
        ...
    }

```

Routes/Auth. Typically when protecting routes from unauthenticated users, we use the ```auth``` middleware.
Byt by appending ```auth:api``` to the end we are telling Laravel that we want to use the driver for the api guard which is set up in the config/auth.php and is defaulted to token.
--
```
    
```



