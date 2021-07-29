<?php

namespace Rh36\EmailApiPackage\Http\Controllers\Api;

use Rh36\EmailApiPackage\Http\Requests\TemplateRequest;
use Rh36\EmailApiPackage\Http\Controllers\ApiBaseController;
use Rh36\EmailApiPackage\Models\EmailTemplate;

class EmailTemplateController extends ApiBaseController
{
    private $limit = 10;

    public function __construct()
    {
        if (!auth()->check()) {
            abort(403, 'Only authenticated users can access templates.');
        }
    }

    public function index()
    {
        $paginator = EmailTemplate::paginate($this->limit);
        return response()->json([
            'status' => 'OK',
            'message' => 'Retrieved successfully',
            'data' => [
                'templates' => $paginator->getCollection(),
            ]
        ]);
    }

    public function show(int $templateId)
    {
        $template = EmailTemplate::where('id', $templateId)->first();

        return response()->json([
            'status' => 'OK',
            'message' => 'Retrieved successfully',
            'data' => [
                'template_name' => $template->template_name,
                'template_body' => $template->template_body,
            ]
        ]);
    }

    public function store(TemplateRequest $request)
    {
        $user = $request->user();

        $template = $user->emailTemplates()->create([
            'template_name' => $request->template_name,
            'template_body' => $request->template_body,
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Email template created.'
        ]);
    }


    public function update(TemplateRequest $request, int $templateId)
    {
        $template = EmailTemplate::where('id', $templateId)->first();

        $template->update($request->only(['template_name', 'template_body']));
        $template->refresh();

        return response()->json([
            'status' => 'OK',
            'message' => 'Update successfully',
            'data' => [
                'id' => $template->id,
                'template_name' => $template->template_name,
                'template_body' => $template->template_body,
            ]
        ]);
    }


    public function destroy(int $templateId)
    {
        $template = EmailTemplate::where('id', $templateId)->first();
        $template->delete();

        return response('', 204);
    }
}
