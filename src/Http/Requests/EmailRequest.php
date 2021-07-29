<?php

namespace Rh36\EmailApiPackage\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from' => 'required|email',
            'to' => 'required|email',
            'subject' => 'required',
            'use_template' => 'required|integer',
            'plain_content' => 'exclude_if:use_template,1|required',
            'template_id' => 'exclude_if:use_template,0|required|exists:email_templates,id',
            'template_data' => 'exclude_if:use_template,0|required',
        ];
    }
}
