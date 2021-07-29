<?php

namespace  Rh36\EmailApiPackage\Contracts;

use Rh36\EmailApiPackage\Models\EmailLog;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

interface EmailPlatformInterface
{
    /**
     * compose the email body based on the choice of use_template or not
     */
    public function composeBody(EmailLog $emaillog): array;

    /**
     * send request via client sendAsync to handle concurrent request
     */
    public function deliver(EmailLog $emaillog);
}
