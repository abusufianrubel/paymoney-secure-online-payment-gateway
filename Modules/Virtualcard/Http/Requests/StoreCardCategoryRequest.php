<?php

namespace Modules\Virtualcard\Http\Requests;

use Modules\Virtualcard\Rules\NoUrls;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Virtualcard\Rules\NoSpecialCharacters;

class StoreCardCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'unique:virtualcard_categories,name',
            'max:50', new NoUrls, new NoSpecialCharacters],
            'status' => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'is_default' => 'Default value',
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
