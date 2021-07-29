<?php

namespace Rh36\EmailApiPackage\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Rh36\EmailApiPackage\Models\EmailLog;

class EmailWasCreated
{
    use Dispatchable, SerializesModels;

    public $emailLog;

    public function __construct(EmailLog $emailLog)
    {
        $this->emailLog = $emailLog;
    }
}
