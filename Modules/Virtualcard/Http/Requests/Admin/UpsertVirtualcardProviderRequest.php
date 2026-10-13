<?php

namespace Modules\Virtualcard\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Modules\Virtualcard\Rules\NoUrls;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Virtualcard\Rules\NoSpecialCharacters;

class UpsertVirtualcardProviderRequest extends FormRequest
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
                new NoSpecialCharacters,
                Rule::unique('virtualcard_providers', 'name')->ignore($this->virtualcardprovider),
            ],
            'currency_id' => ['required'],
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
