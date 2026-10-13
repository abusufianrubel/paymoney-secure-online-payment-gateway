<?php

namespace Modules\Virtualcard\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferenceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'card_creator' => ['required', 'string', 'max:10', 'in:Merchants,Users,Both'],
            'card_kyc' => ['required', 'string', 'in:No,Yes'],
            'card_limit' => ['required', 'numeric', 'min:1', 'max:99'],
            'holder_limit' => ['required', 'numeric', 'min:1', 'max:99'],
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
            'card_creator' => __('Card Creator'),
            'kyc' => __('KYC Verification'),
            'card_limit' => __('Card Creation Limit'),
            'holder_limit' => __('Card Holder Limit'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
