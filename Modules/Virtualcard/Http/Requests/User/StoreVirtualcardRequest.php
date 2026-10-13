<?php

namespace Modules\Virtualcard\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Virtualcard\Enums\CardBrands;
use Illuminate\Validation\Rules\Enum;

class StoreVirtualcardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'virtualcardHolderId' => ['required', 'integer', 'min:0', 'not_in:0', 'exists:virtualcard_holders,id'],
            'preferredCurrency' => ['required', 'string', 'max:3'],
            'preferredCategory' => ['required', 'integer', 'min:0', 'not_in:0', 'exists:virtualcard_categories,id'],
            'cardBrand' => ['required', new Enum(CardBrands::class),],
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
            'virtualcardHolderId' => __('Card Creator'),
            'preferredCurrency' => __('Preferred Currency'),
            'preferredCategory' => __('Preferred Category'),
            'cardBrand' => __('Card Brand'),
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
