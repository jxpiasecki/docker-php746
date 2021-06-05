<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogResponseReceived
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
        // Daily log
        Log::channel('http')->info('Http RESPONSE listener handle => ' . __METHOD__ . '()', json_decode(json_encode($event), true));

        // Create a log channel.
        $log = new Logger('http2'); // Move to service container
        $logFile = storage_path('logs/http/http2-' . Carbon::now()->toDateString() . '.log');
        $log->pushHandler(new StreamHandler($logFile));
        $log->warning('Http RESPONSE listener handle => ' . __METHOD__ . '()', json_decode(json_encode($event), true));
    }
}
