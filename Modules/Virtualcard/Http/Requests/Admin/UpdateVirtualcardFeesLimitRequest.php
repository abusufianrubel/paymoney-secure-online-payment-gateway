<?php
namespace Modules\Virtualcard\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVirtualcardFeesLimitRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'withdrawal_percentage_fee' => ['required', 'numeric', 'gt:0'],
            'withdrawal_fixed_fee' => ['required', 'numeric', 'gt:0'],
            'withdrawal_min_limit' => ['required', 'numeric', 'gt:0'],
            'withdrawal_max_limit' => ['required', 'numeric', 'gt:withdrawal_min_limit'],

            'topup_percentage_fee' => ['required', 'numeric', 'gt:0'],
            'topup_fixed_fee' => ['required', 'numeric', 'gt:0'],
            'topup_min_limit' => ['required', 'numeric', 'gt:0'],
            'topup_max_limit' => ['required', 'numeric', 'gt:topup_min_limit'],

            'virtualcard_provider_id' => ['required', 'exists:virtualcard_providers,id'],
            'virtualcard_currency_code' => [
                'required',
                'string',
                'max:3',
                Rule::unique('virtualcard_feeslimits')
                    ->where('virtualcard_provider_id', $this->virtualcard_provider_id)
                    ->ignore($this->route('virtualcardFeeslimit')->id),
            ],
            'card_creation_fee' => ['required', 'numeric', 'gt:0'],
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'virtualcard_currency_code.unique' => __('The selected currency code is already assigned to this provider.'),
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
