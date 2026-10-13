<?php

namespace Modules\KycVerification\Http\Requests\Admin;

use App\Http\Helpers\Common;
use Illuminate\Foundation\Http\FormRequest;

class VerificationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'max:10', 'in:,approved,pending,rejected']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     */
    public function attributes(): array
    {

        return [
            'status' => __('Status')
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Common::has_permission(auth()->guard('admin')->id(), 'edit_kyc_verification');
    }
}
