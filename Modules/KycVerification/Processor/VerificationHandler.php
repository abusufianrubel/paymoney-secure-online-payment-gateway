<?php

namespace Modules\KycVerification\Processor;

use App\Facade\VerificationProviderManager;
use Modules\KycVerification\Contract\VerificationContract;

class VerificationHandler
{
    /**
     * Assigns the verification provider based on the current settings.
     *
     * This method retrieves the KYC provider from the application settings and
     * attempts to find and bind the corresponding provider concrete class to 
     * the VerificationContract. If the class does not exist, an exception is thrown.
     *
     * @return void
     * @throws \Exception if the verification processor is not found.
     */
    public static function assignVerificationProvider()
    {
        // Retrieve the current KYC provider setting
        $provider = settings('kyc_provider');

        // Find the concrete provider class using the verification provider manager
        $providerConcrete = VerificationProviderManager::find($provider);

        // Check if the provider class exists and bind it to the contract
        if (class_exists($providerConcrete)) {
            app()->bind(VerificationContract::class, $providerConcrete);
        } else {
            // Throw an exception if the provider class is not found
            throw new \Exception(__(":x Verification processor not found.", ["x" => ucfirst($provider)]));
        }
    }
}
