<?php

namespace App\Listeners;

use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
    }
}
