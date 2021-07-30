<?php

namespace Rh36\EmailApiPackage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rh36\EmailApiPackage\Services\MailgunService;
use Rh36\EmailApiPackage\Models\EmailLog;
use Throwable;

class SendByMailgun implements ShouldQueue
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

    public function handle(MailgunService $mg)
    {
        $mg->deliver($this->emailLog);
    }

    public function failed(Throwable $exception)
    {
        // once the job failed, switch to another email service
        SendBySes::dispatch($this->emailLog);
        dispatch(new SendBySes($this->emailLog));
    }
}
