<?php

namespace Rh36\EmailApiPackage\Services;

use Rh36\EmailApiPackage\Models\EmailLog;
use Rh36\EmailApiPackage\Contracts\EmailPlatformInterface;
use Rh36\EmailApiPackage\Traits\EmailTemplateTrait;

use Postmark\PostmarkClient;

class PostmarkService implements EmailPlatformInterface
{
    use EmailTemplateTrait;

    private $client = null;

    /**
     * @param PostmarkClient $client a postmark client that 
     */
    public function __construct(PostmarkClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param EmailLog $emailLog
     * 
     * @return DynamicResponseModel
     */
    public function deliver(EmailLog $emailLog)
    {
        // get HTML email body and the text email body
        $bodyArr = $this->composeBody($emailLog);

        // send with Postmark client
        return $this->client->sendEmail(
            $emailLog->from,
            $emailLog->to,
            $emailLog->subject,
            $bodyArr['HtmlBody'],
            $bodyArr['TextBody'],
            null,
            null,
            $emailLog->replyto,
            $emailLog->cc,
            $emailLog->bcc
        );
    }
}
