<?php

namespace Modules\KycVerification\Contract;

use Illuminate\View\View;

abstract class AutomatedContract extends VerificationContract
{
    /**
     * Get the view for KYC verification provider credential settings.
     */
    abstract public function verificationProviderCredentialView(): View|string;

    /**
     * Get the view for automated KYC verification success.
     */
    abstract public function verificationSuccessView(): View|string;
}
