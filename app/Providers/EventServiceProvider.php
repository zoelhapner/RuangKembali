<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
protected $listen = [
    \App\Events\TaskAssigned::class => [
        \App\Listeners\SendTaskNotification::class,
    ],
    \App\Events\TaskFileUploaded::class => [
        \App\Listeners\SendTaskNotification::class,
    ],
    \App\Events\TaskApproved::class => [
        \App\Listeners\SendTaskNotification::class,
    ],
    \App\Events\TaskRejected::class => [
        \App\Listeners\SendTaskNotification::class,
    ],
];

    public function boot(): void
    {
        //
    }
}
