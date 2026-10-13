<?php

namespace Modules\KycVerification\Http\Requests\Admin;

use App\Http\Helpers\Common;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'kyc_mandatory' => ['required', 'string', 'max:10', 'in:Yes,No'],
            'kyc_required_for' => ['required', 'string', 'max:10'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     */
    public function attributes(): array
    {

        return [
            'kyc_mandatory' => __('KYC Mandatory'),
            'kyc_required_for' => __('KYC Required For')
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Common::has_permission(auth()->guard('admin')->id(), 'edit_kyc_setting');
    }
}
