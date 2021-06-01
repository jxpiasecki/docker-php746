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

Get all configs/templates etc from vendors
---
```
    php artisan vendor:publish --all
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
---
But by appending ```auth:api``` to the end we are telling Laravel that we want to use the driver for the api guard which is set up in the config/auth.php and is defaulted to token.
---
```
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],
```

Middleware. Checking roles/permissions via middleware.
--
```
Route::group(
    [
        'middleware' => [
            'auth:api',
            HasAnyPermission::class . ':' . Permission::MANAGEMENT_USERS . ',' . Permission::MANAGEMENT_EMAILS,
        ],
    ],
    function () {
        /// Routes
    }
)

class HasAnyPermission
{

    /**
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$permissions
     * @return mixed
     * @throws PermissionDenied
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        /* @var User|OauthUser $user */
        $user = Auth::user();
        foreach ($permissions as $permission) {
            if ($user->permissions && $user->permissions->contains('code', $permission)) {
                return $next($request);
            }
        }

        throw new PermissionDenied();
    }
```

Request. Query/input get request send params
---
```
    //$_REQUEST 
    $name = $request->input('products.0.name');
    $names = $request->input('products.*.name');
    $all = $request->input();
    
    //$_GET
    $name = $request->query('name');
    $allGet = $request->query();
    
    // Json
    $name = $request->input('user.name');
    
    //The boolean method returns true for 1, "1", true, "true", "on", and "yes". 
    // Used for checkboxes
    $archived = $request->boolean('archived');
    
    // Dynamic
    $name = $request->name;
```

Request. Upload file.
---
```
    if ($request->file('photo')->isValid()) {
        $file = $request->file('photo');
        $path = $request->photo->path();
        // The extension method will attempt to guess the file's extension based on its contents.
        // This extension may be different from the extension that was supplied by the client
        $extension = $request->photo->extension();
        
        // Store file (directory path, disk) - unique ID will auto generate as the filename
        $path = $request->photo->store('images', 's3');
        // Store file (directory path, filename, disk)
        $path = $request->photo->storeAs('images', 'filename.jpg', 's3');
    }
```
Response.Redirect.Back.Download.Stream.Display
---
```
    return response('Hello World');
    return redirect('home/dashboard');
    return back()->withInput();
    return response()->download(base_path('/storage/app/public/images/fox.png'));
    return response()->streamDownload(
        function () {
            echo 'this is content';
        },
        'laravel-readme.md'
    );
    return response()->file(base_path('/storage/app/public/images/fox.png'));
```
View composer/creator
---
```
    View::composer('dashboard', function ($view) {
        // Inject variables after when render starts
        $view->with('count', 99);
    });
    View::composer('dashboard', function ($view) {
        // Inject variables  executed immediately after the view is instantiated.
        $view->with('count', 99);
    });
```

