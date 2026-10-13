<?php

if (!function_exists('verifyKycModulesCredentialRequirement')) {
    /**
     * Check if any KYC module requires credentials.
     *
     * @return bool
     */
    function verifyKycModulesCredentialRequirement()
    {
        $provider = \App\Facade\VerificationProviderManager::find(settings('kyc_provider'));

        // Check if  provider has credentials in config
        
        $providerInstance = app($provider);

        // If module alias and credentials are present, return true
        if (config("{$providerInstance->moduleAlias()}.credentials")) {
            return true;
        }

        return false;
    }
}

if (!function_exists('verifyModulesCredentialRequirement')) {
    /**
     * Check if any KYC module requires credentials.
     *
     * @return bool
     */
    function verifyModulesCredentialRequirement($moduleAlias)
    {
        $provider = \App\Facade\VerificationProviderManager::find($moduleAlias);

        if (empty($provider)) {
            return false;
        }

        // Check if  provider has credentials in config
        $providerInstance = app($provider);

        // If module alias and credentials are present, return true
        if (config("{$providerInstance->moduleAlias()}.credentials")) {
            return true;
        }

        return false;
    }
}

if (! function_exists('getProviderNameByAlias')) {

    /**
     * Get provider name by alias
     *
     * @return string
     */
    function getProviderNameByAlias($alias)
    {
        return \Modules\KycVerification\Entities\KycProvider::whereAlias($alias)->value('name') ?? ucfirst($alias);
    }
}

if (! function_exists('getKycDocumentPath')) {

    /**
     * Get the path for the KYC documents
     *
     * @param string $type The type of document, either 'address' or 'identity'
     * @return string The path for the KYC documents
     */
    function getKycDocumentPath($type)
    {
        return  $type == 'address' ? config('kycverification.kyc_document_path') . 'address-proof-files/' : config('kycverification.kyc_document_path') . 'identity-proof-files/';
    }
}

if (! function_exists('getKycProviderId')) {
    function getKycProviderId($alias)
    {
        return \Modules\KycVerification\Entities\KycProvider::whereAlias($alias)->value('id');
    }
}


