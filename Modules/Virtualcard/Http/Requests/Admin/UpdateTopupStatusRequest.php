<?php

namespace Modules\Virtualcard\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Virtualcard\Rules\NoUrls;
use Modules\Virtualcard\Rules\NoSpecialCharacters;

class UpdateTopupStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fund_approval_status' => ['required', 'string'],
            'cancellation_reason'  => [
                'required_if:fund_approval_status,Cancelled',
                'max:191',
                new NoUrls,
                new NoSpecialCharacters
            ]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
