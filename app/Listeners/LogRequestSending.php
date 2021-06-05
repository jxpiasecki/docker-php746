<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Monolog\Logger;

class LogRequestSending
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        // Every request other log.
//        Config::set('logging.channels.http.driver', 'single');
//        $logPathPattern = Config::get('logging.channels.http.pattern');
//        $logPathPattern = Str::replace('http.log', 'http-'.Carbon::now()->toDateTimeString().'.log', $logPathPattern);
//        Config::set('logging.channels.http.path', $logPathPattern);

        // Daily log
        Log::channel('http')->info('Http REQUEST listener handle => ' . __METHOD__ . '()', json_decode(json_encode($event), true));
    }
}
