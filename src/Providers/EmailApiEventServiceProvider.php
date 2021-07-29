<?php

namespace Rh36\EmailApiPackage\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Rh36\EmailApiPackage\Events\EmailWasCreated;
use Rh36\EmailApiPackage\Listeners\EnqueueEmail;

class EmailApiEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        EmailWasCreated::class => [
            EnqueueEmail::class,
        ]
    ];

    public function boot()
    {
        parent::boot();
    }
}
