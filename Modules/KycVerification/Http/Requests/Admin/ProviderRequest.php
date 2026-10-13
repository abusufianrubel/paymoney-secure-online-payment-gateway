<?php

namespace Modules\KycVerification\Http\Requests\Admin;

use App\Http\Helpers\Common;
use Illuminate\Foundation\Http\FormRequest;

class ProviderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:kyc_providers,name,' . $this->provider?->id],
            'is_default' => ['required', 'string', 'max:5']
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Common::has_permission(auth()->guard('admin')->id(), 'edit_kyc_provider');
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => __('Name'),
            'is_default' => __('Default')
        ];
    }
}
