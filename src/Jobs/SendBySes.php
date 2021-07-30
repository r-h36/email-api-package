<?php

namespace Rh36\EmailApiPackage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rh36\EmailApiPackage\Services\SesService;
use Rh36\EmailApiPackage\Models\EmailLog;

class SendBySes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $emailLog;

    public function __construct(EmailLog $emailLog)
    {
        $this->emailLog = $emailLog;
    }

    public function backoff()
    {
        return [1, 5, 10];
    }

    public function handle(SesService $ses)
    {
        $ses->deliver($this->emailLog);
    }
}
