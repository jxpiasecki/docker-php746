<?php

namespace App\Providers;

use App\Events\PodcastProcessed;
use App\Listeners\SendPodcastNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PodcastProcessed::class => [
            SendPodcastNotification::class,
        ],

        'Illuminate\Http\Client\Events\RequestSending' => [
            'App\Listeners\LogRequestSending'
        ],

        'Illuminate\Http\Client\Events\ResponseReceived' => [
            'App\Listeners\LogResponseReceived',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //It do the same as listen variable.
//        Event::listen(
//            PodcastProcessed::class,
//            [SendPodcastNotification::class, 'handle']
//        );

//        Event::listen('*', function ($eventName, array $data) {
//            dd($eventName);
//        });
    }
}
