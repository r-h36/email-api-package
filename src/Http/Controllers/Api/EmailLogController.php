<?php

namespace Rh36\EmailApiPackage\Http\Controllers\Api;

use Rh36\EmailApiPackage\Http\Requests\EmailRequest;
use Rh36\EmailApiPackage\Http\Controllers\ApiBaseController;
use Rh36\EmailApiPackage\Models\EmailLog;

class EmailLogController extends ApiBaseController
{
    private $limit = 20;

    public function __construct()
    {
        if (!auth()->check()) {
            abort(403, 'Only authenticated users can access emails.');
        }
    }

    public function index()
    {
        $paginator = EmailLog::paginate($this->limit);
        return response()->json([
            'status' => 'OK',
            'message' => 'Retrieved successfully',
            'data' => [
                'emails' => $paginator->getCollection(),
            ]
        ]);
    }

    public function show(EmailLog $emailLog)
    {
        return response()->json([
            'from' => $emailLog->from,
            'to' => $emailLog->to,
            'subject' => $emailLog->subject,

            'use_template' => $emailLog->use_template,
            'plain_content' => $emailLog->plain_content,
            'template_id' => $emailLog->template_id,
            'template_data' => $emailLog->template_data,

            'cc' => $emailLog->cc,
            'bcc' => $emailLog->bcc,
            'replyto' => $emailLog->replyto,
        ]);
    }

    public function store(EmailRequest $request)
    {
        $user = $request->user();

        $emailLog = $user->emailLogs()->create([
            'from' => $request->from,
            'to' => $request->to,
            'subject' => $request->subject,

            'use_template' => $request->use_template,
            'plain_content' => $request->plain_content,
            'template_id' => $request->template_id,
            'template_data' => $request->template_data,

            'cc' => $request->cc,
            'bcc' => $request->bcc,
            'replyto' => $request->replyto,
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Email queued.'
        ]);
    }
}
