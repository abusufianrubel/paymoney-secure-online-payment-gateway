<?php

namespace Modules\Virtualcard\Http\Requests;

use Modules\Virtualcard\Rules\NoUrls;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Virtualcard\Rules\NoSpecialCharacters;

class UpdateCardCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                new NoUrls,
                new NoSpecialCharacters
            ],
            'status' => 'required',
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
