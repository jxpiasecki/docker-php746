<?php

use App\Http\Middleware\SetDefaultLocaleForUrls;
use App\Services\Jp\Client as JpClient;
use App\Services\Jp\ClientWithoutProvider;
use App\Services\Jp\Facades\Jp as JpFacade;
use App\Services\Jp\Facades\Jp as JpFacadeRealTimeFacade;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

Route::get('/test', function (
    Request $request,
    JpClient $jpClient,
    Container $container,
    JpFacade $jp,
    ClientWithoutProvider $clientWithoutProvider
) {


    dump($request);

    /* @var JpClient $jpClientDynamic */
    $jpClientDynamic = $container->get(JpClient::class);
    dump($jpClient->run(), $jpClientDynamic->run(), $clientWithoutProvider->run());

    dump($jp::run(), JpFacadeRealTimeFacade::run());

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
    if(!$request->query->get('signature')){
        dump('no signature');
    }

    if ($request->hasValidSignature()) {
        dump('Url valid');
    }
    else{
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























