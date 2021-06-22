Pominiete:
---
https://laravel.com/docs/8.x/broadcasting
https://laravel.com/docs/8.x/helpers


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

Service Container. Get facade from service container.
---
```
    $logger = App::get('log');
    $logger = App::make('log');
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
Cache. Smart cache remember, get existing cached value or when doesn't exist make action to set cache
---
```
    // 
    $value = Cache::remember('users', $seconds, function () {
        return DB::table('users')->get();
    });
    // Remember forever
    $value = Cache::rememberForever('users', function () {
        return DB::table('users')->get();
    });
```
Collections. 
---
groupBy() - use Collection, list all items as numerable keys
---
keyBy() - use array, list only last items for given keys (like in normal array values with the same key override by last)
---
```
    $collection = collect([
        ['account_id' => 'account-x10', 'product' => 'Chair'],
        ['account_id' => 'account-x10', 'product' => 'Bookcase'],
        ['account_id' => 'account-x11', 'product' => 'Desk'],
    ]);
    $grouped = $collection->groupBy('account_id');
    $keyed = $collection->keyBy('account_id');
```
```
    |--------------------------------------------------------------------------
    | DUMP. groupBy
    |--------------------------------------------------------------------------
    Illuminate\Support\Collection {#366 ▼
      #items: array:2 [▼
        "account-x10" => Illuminate\Support\Collection {#341 ▼
          #items: array:2 [▼
            0 => array:2 [▼
              "account_id" => "account-x10"
              "product" => "Chair"
            ]
            1 => array:2 [▼
              "account_id" => "account-x10"
              "product" => "Bookcase"
            ]
          ]
        }
        "account-x11" => Illuminate\Support\Collection {#432 ▼
          #items: array:1 [▼
            0 => array:2 [▼
              "account_id" => "account-x11"
              "product" => "Desk"
            ]
          ]
        }
      ]
    }
    
    |--------------------------------------------------------------------------
    | DUMP. keyBy
    |--------------------------------------------------------------------------
    Illuminate\Support\Collection {#362 ▼
      #items: array:2 [▼
        "account-x10" => array:2 [▼
          "account_id" => "account-x10"
          "product" => "Bookcase"
        ]
        "account-x11" => array:2 [▼
          "account_id" => "account-x11"
          "product" => "Desk"
        ]
      ]
    }
```
Lazy Collections. Used to loop over big data without less using memory (yield).
---
https://www.php.net/manual/en/language.generators.overview.php
---
```
    use Illuminate\Support\LazyCollection;
    
    LazyCollection::make(function () {
        $handle = fopen('log.txt', 'r');
    
        while (($line = fgets($handle)) !== false) {
            yield $line;
        }
        
        fclose($handle);
    });
```
Arrow Functions. Just like in JS.
---
https://www.php.net/manual/en/functions.arrow.php
---
```
    $y = 1;
 
    $fn1 = fn($x) => $x + $y;
    
    // equivalent to using $y by value:
    $fn2 = function ($x) use ($y) {
        return $x + $y;
    };

    var_export($fn1(3)); // 4
```
Events. How events work.
---
```
    Event::dispatcher(new Event());     // Dispatch event
    EventServiceProvider::class         // $listeners => Match event with listeners
                                        // $subscribers => Listen for events and handle via event type
    Listener::handle();                 // Handle event by listener
```
Storage. temporary url.
---
```
    use Illuminate\Support\Facades\Storage;
    
    $url = Storage::temporaryUrl(
        'file.jpg', now()->addMinutes(5)
    );
```
Storage. Storing uploaded files.
---
```
Note that we only specified a directory name and not a filename. 
By default, the putFile method will generate a unique ID to serve as the filename. 
The file's extension will be determined by examining the file's MIME type.
The path to the file will be returned by the putFile method so you can store the path, including the generated filename, in your database.
The putFile and putFileAs methods also accept an argument to specify the "visibility" of the stored file.

    use Illuminate\Http\File;
    use Illuminate\Support\Facades\Storage;
    
    // Automatically generate a unique ID for filename...
    $path = Storage::putFile('photos', new File('/path/to/photo'), 'public');
    
    // Manually specify a filename...
    $path = Storage::putFileAs('photos', new File('/path/to/photo'), 'photo.jpg');
    
    // We
    $path = $request->file('avatar')->store('avatars');
    $path = $request->file('avatar')->storeAs(
        'avatars', $request->user()->id
    );
    $path = Storage::putFile('avatars', $request->file('avatar'));
    $path = Storage::putFileAs(
        'avatars', $request->file('avatar'), $request->user()->id
    );
```
Logging. Logging to own log file.
---
```
step1: create a channel inside the config/logging.php file
    'http' => [
        'driver' => 'daily',
        'pattern' => storage_path('logs/http/http.log'),
        'path' => storage_path('logs/http/http.log'),
        'permission' => 0666,
    ],
step2: Create dynamic path to log file and override existing
    // Single log - Every request other log file.
    //        Config::set('logging.channels.http.driver', 'single');
    //        $logPathPattern = Config::get('logging.channels.http.pattern');
    //        $logPathPattern = Str::replace('http.log', 'http-'.Carbon::now()->toDateTimeString().'.log', $logPathPattern);
    //        Config::set('logging.channels.http.path', $logPathPattern);

    // Daily log.
        Log::channel('http')->info('Http REQUEST listener handle => ' . __METHOD__ . '()', json_decode(json_encode($event), true));
step 3: Create dynamic log channel via Monolog logger
    // Create a log channel.
    $log = new Logger('http2'); // Move to service container
    $logFile = storage_path('logs/http/http2-' . Carbon::now()->toDateString() . '.log');
    $log->pushHandler(new StreamHandler($logFile));
    $log->warning('Http REQUEST listener handle => ' . __METHOD__ . '()', json_decode(json_encode($event), true));
```
Localization.
---
```
    $locale = 'pl';
    App::setLocale($locale);
    $locale = App::currentLocale();
    if (App::isLocale('en')) { ... }
    
    # Pluralization
    'apples' => 'There is one apple|There are many apples',
    echo trans_choice('apples', 2);
```
Mailable(long messages).
---
```
    ~ php artisan make:mail OrderShipped

    Route::get('/mailable', function () {
        $text = 'this is the beginning';
        $orderShippedMail = new OrderShipped($text);
    
        // Sending Mail
        Mail::to('janusz.szymanski@mailinator.com')->send($orderShippedMail);
        // Queue Mail
        Mail::to('janusz.szymanski@mailinator.com')->queue($orderShippedMail);
    
        return $orderShippedMail;
    });
```

Notifiable(short messages).
---
```
    ~ php artisan make:notification InvoicePaid
    
    Route::get('/notifiable', function () {
        /* @var User $user */
        $user = Auth::user() ? Auth::user() : User::first();
    
        $text = 'THIS IS INPUT TEXT';
        $invoicePaidNotification = new InvoicePaid($text);
    
        // 1. Notify by user model using trait Notifiable
        $user->notify($invoicePaidNotification);
        // 2. Notify by user model using facade Notification
        Notification::send($user, $invoicePaidNotification);
        // 3. Notify ad hoc on demand (without user model)
        Notification::route('mail', 'janusz.szymanski@mailinator.com')
            ->notify($invoicePaidNotification);
    
        return $invoicePaidNotification->toMail($user);
    });
    
```
