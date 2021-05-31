<?php

use App\Http\Middleware\SetDefaultLocaleForUrls;
use App\Services\Jp\Client as JpClient;
use App\Services\Jp\ClientWithoutProvider;
use App\Services\Jp\Facades\Jp as JpFacade;
use App\Services\Jp\Facades\Jp as JpFacadeRealTimeFacade;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

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

    Cache::get('okokok');

    return dd('---');
});

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























