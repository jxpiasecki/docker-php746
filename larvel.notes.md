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

Get all: configs/templates, classes stubs, docker files from: vendor, packages, etc.
---
It's generally safe to execute.
---
```
    php artisan vendor:publish --all
    php artisan stub:publish
    php artisan sail:publish
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
Url. Signed url
---
```
    // Create
    return URL::signedRoute('unsubscribe', ['user' => 1]);
    
    // Check signature
    if(!$request->query->get('signature')){
        dump('no signature');
    }

    if ($request->hasValidSignature()) {
        dump('Url valid');
    }
    else{
        dump('Url invalid');
    }
```
Session. Create custom Session driver
---
```
    1. Create handler: MongoSessionHandler
    2. Create provider: SessionServiceProvider with extend() method in provider boot()
    3. Change handler in ```config/session.php``` to 'mongo'
```
Validation.Rules
---
```
    $request->validate([
        'title' => 'bail|required|unique:posts|max:255', // bail - do not check all when first fail 
        'author.name' => 'required',                     // nested, when array used
        'author\.description' => 'required',             // when dot sign is used in name
        'publish_at' => 'nullable|date',                 // optional input, nullable when not provided
    ]);
```

Validation.Form Request Validation
---
```
    1. Create Form Request
        php artisan make:request StorePostRequest
    2. Define authorize() and rules() method
    3. Type-hint request param in your controller action method.
        public function store(StorePostRequest $request)
        {
            // Retrieve the validated input data
            $data = $request->validated();
            
            // Save
            /* @var Post $post */
            $post = Post::create();
            $post->fill($data);
            $post->save();
        }
```

Validation. Custom validation rules. 
---
```
    1. Run: php artisan make:rule Uppercase
    2. Define class passes() and messages() methods in app/Rules
    3. Add rule to validation via array creating new instance operator
        $request->validate([
            'name' => ['required', 'string', new Uppercase],
        ]);
        
    OR use closure (when validation rules used once)
        
    $validator = Validator::make($request->all(), [
        'title' => [
            'required',
            'max:255',
            function ($attribute, $value, $fail) {
                if ($value === 'foo') {
                    $fail('The '.$attribute.' is invalid.');
                }
            },
        ],
    ]);
```

Logging. The ```single``` and ```daily``` channels have optional options: permission.
---
Preventing log permission errors.
---
```
       'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'permission' => 0666,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'permission' => 0666,
        ],
```
