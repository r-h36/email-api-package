<?php

namespace Rh36\EmailApiPackage\Listeners;

use Rh36\EmailApiPackage\Events\EmailWasCreated;
use Rh36\EmailApiPackage\Jobs\SendByPostmark;

class EnqueueEmail
{
    public function handle(EmailWasCreated $event)
    {
        dispatch(new SendByPostmark($event->emailLog));
    }
}
