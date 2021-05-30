<?php

use App\Services\Jp\Client as JpClient;
use App\Services\Jp\ClientWithoutProvider;
use App\Services\Jp\Facades\Jp as JpFacade;
use App\Services\Jp\Facades\Jp as JpFacadeRealTimeFacade;
use Illuminate\Container\Container;

;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
