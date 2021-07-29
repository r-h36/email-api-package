<?php

namespace Rh36\EmailApiPackage\Services;

use Rh36\EmailApiPackage\Models\EmailLog;
use Rh36\EmailApiPackage\Contracts\EmailPlatformInterface;
use Rh36\EmailApiPackage\Traits\EmailTemplateTrait;

use Mailgun\Mailgun;

class MailgunService implements EmailPlatformInterface
{
    use EmailTemplateTrait;

    protected $client;

    /**
     * @param PostmarkClient $client a postmark client that 
     */
    public function __construct(Mailgun $mg)
    {
        $this->client = $mg;
    }

    public function deliver(EmailLog $emailLog)
    {
        // get HTML email body and the text email body
        $bodyArr = $this->composeBody($emailLog);

        return $this->client->messages()->send(config('services.mailgun.domain'), [
            'from'    => $emailLog->from,
            'to'      => $emailLog->to,
            'subject' => $emailLog->subject,
            'html'    => $bodyArr['HtmlBody'],
            'text'    => $bodyArr['TextBody'],
            'cc'      => $emailLog->cc,
            'replyto' => $emailLog->replyto,
            'bcc'     => $emailLog->bcc,
        ]);
    }
}
