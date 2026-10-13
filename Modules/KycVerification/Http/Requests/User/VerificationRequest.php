<?php

namespace Modules\KycVerification\Http\Requests\User;

use App\Http\Requests\CustomFormRequest;
use Modules\KycVerification\Contract\VerificationContract;
use Modules\KycVerification\Processor\VerificationHandler;

class VerificationRequest extends CustomFormRequest
{
    protected $processor;

    public function __construct()
    {
        VerificationHandler::assignVerificationProvider();
        $this->processor = app(VerificationContract::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return $this->processor->userVerificationRules();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return $this->processor->userVerificationAttributes();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
