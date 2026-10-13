<?php

namespace Modules\KycVerification\Contract;

use Exception;
use Illuminate\View\View;

abstract class VerificationContract
{
    /**
     * Get the view for user KYC verification.
     */
    abstract public function getUserVerificationView(): View|string;

    /**
     * Process user KYC verification.
     * @param \Illuminate\Http\Request $request
     */
    abstract public function processUserVerification($request): array;

    /**
     * Get the view for admin KYC verification details.
     * @param App\Models\DocumentVerification $verification
     */
    abstract public function adminVerificationView($verification): View|string;

    /**
     * Process admin KYC verification update.
     * @param \Illuminate\Http\Request $request
     * @param App\Models\DocumentVerification $verification
     */
    abstract public function updateAdminVerification($request, $verification): array;

    /**
     * Get the validation rules that apply to the user KYC verification
     */
    abstract public function userVerificationRules(): array;

    /**
     * Get custom attributes for validator errors for user KYC verification.
     */
    abstract public function userVerificationAttributes(): array;

    /**
     * Get the module alias
     */
    abstract public function moduleAlias(): string;

    /**
     * Magic method to handle calls to undefined methods.
     *
     * @param string $name
     * @param array $arguments
     * @throws Exception
     */
    public function __call(string $name, array $arguments)
    {
        throw new Exception("The method '{$name}' is not defined in " . static::class);
    }
}

