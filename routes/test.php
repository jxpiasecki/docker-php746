<?php

use App\Events\PodcastProcessed;
use App\Helpers\EloquentBuilder;
use App\Http\Middleware\SetDefaultLocaleForUrls;
use App\Jobs\ProcessPodcast;
use App\Mail\OrderShipped;
use App\Models\Session;
use App\Notifications\InvoicePaid;
use App\Services\Jp\Client as JpClient;
use App\Services\Jp\ClientWithoutProvider;
use App\Services\Jp\Facades\Jp as JpFacade;
use App\Services\Jp\Facades\Jp as JpFacadeRealTimeFacade;
use Illuminate\Container\Container;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

Route::get('db', function (Request $request) {
//    $r = DB::table('sessions')->get();
//    $r = DB::table('sessions')->first();
//    $r = DB::table('sessions')->pluck('payload', 'id');
//    var_dump($r);
//    dump($r);
//
//    DB::table('sessions')->orderBy('id')->chunk(2, function ($results, $page) {
//        dump($results, $page);
//    });

    $query = DB::table('sessions')->where('id' , '<>', '0');
    $items = $query->get();
    var_dump($query->paginate());
    dump($query->paginate());
    dump($query->paginate()->toJson());

    $paginator = new LengthAwarePaginator($items, $items->count(), 15, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
    var_dump($paginator);
    dump($paginator);
    dump($paginator->toJson());

    die;
});

Route::get('/queue', function () {
    /* @var User $user */
    $user = Auth::user() ? Auth::user() : User::find(rand(1, 2));
    $isActiveUser = true;
    $isNotActiveUser = false;

    ProcessPodcast::dispatch($user); // Add to job queue
    ProcessPodcast::dispatchIf($isActiveUser, $user); // Add to job queue if first param is true
    ProcessPodcast::dispatchUnless($isNotActiveUser, $user); // Add to job queue if first param is false
    ProcessPodcast::dispatchSync($user); // Execute immediately
    ProcessPodcast::dispatchAfterResponse($user); // Executes after last echo

    /// Add to job queue chain jobs
    Bus::chain([
        new ProcessPodcast($user),
        new ProcessPodcast($user),
        new ProcessPodcast($user),
    ])->dispatch();

    echo '==============over';
});

Route::get('/notifiable', function () {
    /* @var User $user */
    $user = Auth::user() ? Auth::user() : User::find(rand(1, 2));
    dump($user->notifications->toArray());

    $text = 'THIS IS INPUT TEXT';
    $invoicePaidNotification = new InvoicePaid($text);

    // 1. Notify by user model using trait Notifiable
    $invoicePaidNotification->text = 'AAA';
    $user->notify($invoicePaidNotification);
    // 2. Notify by user model using facade Notification
    $invoicePaidNotification->text = 'BBB';
    Notification::send($user, $invoicePaidNotification);
    // 3. Notify ad hoc on demand (without user model)
    $invoicePaidNotification->text = 'CCC';
    Notification::route('mail', 'janusz.szymanski@mailinator.com')
        ->notify($invoicePaidNotification);

    return $invoicePaidNotification->toMail($user);
});

Route::get('/mailable', function () {
    $text = 'this is the beginning';
    $orderShippedMail = new OrderShipped($text);

    // Sending Mail
    Mail::to('janusz.szymanski@mailinator.com')->send($orderShippedMail);
    // Queue Mail
    Mail::to('janusz.szymanski@mailinator.com')->queue($orderShippedMail);

    return $orderShippedMail;
});

Route::get('/test', function (
    Request               $request,
    JpClient              $jpClient,
    Container             $container,
    JpFacade              $jp,
    ClientWithoutProvider $clientWithoutProvider,
    PodcastProcessed      $podcastProcessedEvent
) {

    Mail::to('janusz.szymanski1@mailinator.com')->send(new OrderShipped('some random text'));

    // Http
    $response = Http::get('https://www.onet.pl/');
//    dump($response->body());


//    dd($response->body(), Http::dd()->get('http://wp.pl'));
//    $response = Http::post('http://example.com/users', [
//        'name' => 'Steve',
//        'role' => 'Network Administrator',
//    ]);
//    Http::dd()->get('http://wp.pl');


    // Events
    $sessionId = $request->getSession()->getId();
    $session = Session::find($sessionId)->first();
    dd($sessionId, $session);
    $podcastProcessedEvent->session = $session;
    Log::info(__FILE__ . '::' . __FUNCTION__ . '()' . ' Event dispatched: PodcastProcessed');
    Event::dispatch($podcastProcessedEvent);

    dd(__METHOD__);

    /* @var JpClient $jpClientDynamic */
    $jpClientDynamic = $container->get(JpClient::class);
    dump($jpClient->run(), $jpClientDynamic->run(), $clientWithoutProvider->run());

    dump($jp::run(), JpFacadeRealTimeFacade::run());

    $collection = collect([
        ['name' => 'Taylor Otwell', 'age' => 34],
        ['name' => 'Abigail Otwell', 'age' => 30],
        ['name' => 'Taylor Otwell', 'age' => 36],
        ['name' => 'Abigail Otwell', 'age' => 32],
    ]);

    $sorted = $collection->sortBy([
        fn($a, $b) => $a['name'] <=> $b['name'],
        fn($a, $b) => $b['age'] <=> $a['age'],
    ]);
    dd($sorted);


    dd(
        collect([2, 4, 6, 8])->search(function ($item, $key) {
            dump($item);
            return $item > 5;
        })
    );

    $collection = collect(['a', 'b', 'c', 'd', 'e', 'f']);
    $r = $collection->nth(4, 3);
    dd($r);

    $collection = collect([
        ['account_id' => 'account-x10', 'product' => 'Chair'],
        ['account_id' => 'account-x10', 'product' => 'Bookcase'],
        ['account_id' => 'account-x11', 'product' => 'Desk'],
    ]);
    $grouped = $collection->groupBy('account_id');
    $keyed = $collection->keyBy('account_id');
    dd($grouped, $keyed);


    dump(collect(['a', 'b', 'c'])->join(', ', ', and '));
    $collection = collect(['Desk', 'Sofa', 'Chair']);
    $intersect = $collection->intersect(['Desk', 'Chair', 'Bookcase']);
    dd($intersect->all());

    //dd(Config::get('app.locale'), env('JP_CLIENT_PASSWORD'), App::environment(['dev', 'local']));
    throw new \http\Exception\BadConversionException('aha');
    return Response::view('testing', ['name' => 'ty szczurze!!!']);
    return View::make('testing', ['name' => 'ty szczurze!!!']);
    return dd('---');

    return response()->download(base_path('/storage/app/public/images/fox.png'));
    return response()->streamDownload(
        function () {
            echo 'this is content';
        },
        'laravel-readme.md'
    );
    return response()->file(base_path('/storage/app/public/images/fox.png'));

    return Response::jsonp('ssss', ['name' => 'Abigail', 'state' => 'CA']);

});

Route::get('/unsubscribe', function (Request $request) {
    if (!$request->query->get('signature')) {
        dump('no signature');
    }

    if ($request->hasValidSignature()) {
        dump('Url valid');
    } else {
        dump('Url invalid');
    }

    return URL::signedRoute('unsubscribe', ['user' => 1]);
})->name('unsubscribe');


Route::get('/user', function (Request $request) {
    echo 'user';
});

Route::get('/user/{name}', function ($name) {
    echo 'Hello user: ' . $name;
})->where('name', '[A-Za-z]+');

Route::get('/user/{id}', function ($id) {
    echo 'User id: ' . $id;
});

Route::redirect('/here', '/test', 301);

Route::middleware(SetDefaultLocaleForUrls::class)
    ->get(
        '/{locale?}/posts',
        function ($locale = null) {
            $locale = ($locale === null) ? Config::get('app.locale') : $locale;
            dd('This is posts with locale: ' . $locale);
        }
    )
    ->name('post.index');























