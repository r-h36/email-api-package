<?php

namespace Rh36\EmailApiPackage\Services;

use Rh36\EmailApiPackage\Models\EmailLog;
use Rh36\EmailApiPackage\Contracts\EmailPlatformInterface;
use Rh36\EmailApiPackage\Traits\EmailTemplateTrait;
use Illuminate\Support\Str;
use Aws\Ses\SesClient;

class SesService implements EmailPlatformInterface
{
    use EmailTemplateTrait;

    protected $client;

    /**
     * @param PostmarkClient $client a postmark client that 
     */
    public function __construct(SesClient $client)
    {
        $this->client = $client;
    }

    public function deliver(EmailLog $emailLog)
    {

        $charSet = 'UTF-8';

        // get HTML email body and the text email body
        $bodyArr = $this->composeBody($emailLog);

        $config = [
            'Destination' => [
                'ToAddresses' => $this->getRecipients($emailLog->to),
            ],
            'Source' => $emailLog->from,
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => $charSet,
                        'Data' => $bodyArr['HtmlBody'],
                    ],
                    'Text' => [
                        'Charset' => $charSet,
                        'Data' => $bodyArr['TextBody'],
                    ],
                ],
                'Subject' => [
                    'Charset' => $charSet,
                    'Data' => $emailLog->subject,
                ],
            ],
        ];

        if (!empty($emailLog->cc)) {
            $config['Destination']['CcAddresses'] = $this->getRecipients($emailLog->cc);
        }
        if (!empty($emailLog->bcc)) {
            $config['Destination']['BccAddresses'] = $this->getRecipients($emailLog->bcc);
        }
        if (!empty($emailLog->replyto)) {
            $config['ReplyToAddresses'] = $this->getRecipients($emailLog->replyto);
        }

        return $this->client->sendEmail($config);
    }

    private function getRecipients($recipients): array
    {
        if (is_string($recipients) && Str::contains($recipients, ';')) {
            return explode(';', $recipients);
        }
        if (empty($recipients)) {
            return [];
        }

        return [$recipients];
    }
}
