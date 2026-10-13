<?php

namespace Modules\Virtualcard\Http\Requests\Admin;

use Modules\Virtualcard\Entities\{
    VirtualcardCategory,
    VirtualcardProvider,
    VirtualcardHolder
};
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Virtualcard\Enums\CardBrands;

class IssueVirtualcardRequest extends FormRequest
{
    public function getVirtualcardProvider(): VirtualcardProvider
    {
        return VirtualcardProvider::where('id', $this->virtualcardProviderId)->first();
    }

    public function getVirtualcardCategory(): VirtualcardCategory
    {
        return VirtualcardCategory::where('id', $this->virtualcardCategoryId)->first();
    }

    public function getVirtualcardHolder(): VirtualcardHolder
    {
        return VirtualcardHolder::where('id', $this->virtualcardHolderId)->first();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'virtualcardProviderId' => [
                'required',
                'exists:virtualcard_providers,id'
            ],
            'virtualcardHolderId' => [
                'required',
                'exists:virtualcard_holders,id'
            ],

            'virtualcardCategoryId' => [
                'required',
                'exists:virtualcard_categories,id'
            ],
            'cardType' => 'sometimes|required',

            'cardBrand' => [
                'required',
                new Enum(CardBrands::class),
            ],
            'currencyCode' => 'required|string',
            'amount' => 'nullable|sometimes|required|numeric',
            'cardExpiryDate' => 'required',
            'status' => 'sometimes|required'
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
