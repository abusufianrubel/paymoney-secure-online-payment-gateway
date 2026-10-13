<?php

namespace Modules\KycVerification\Http\Controllers\Admin;

use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use Modules\KycVerification\Contract\VerificationContract;
use Modules\KycVerification\Processor\VerificationHandler;

class CredentialSettingController extends Controller
{
    /**
     * CredentialSettingController constructor.
     *
     * This constructor assigns the verification provider by calling
     * the static method assignVerificationProvider from the VerificationHandler.
     */
    public function __construct()
    {
        // Assign the verification provider based on current settings
        VerificationHandler::assignVerificationProvider();
    }

    /**
     * return the active provider credential settings view
     *
     * @param VerificationContract $processor
     * @return \Illuminate\Http\Response
     */
    public function activeProviderCredentialSetting(VerificationContract $processor)
    {
        try {
            // Get the verification provider credential view
            return $processor->verificationProviderCredentialView();
        } catch (\Exception $exception) {
            // Handle the exception and display an error message to the user
            (new Common())->one_time_message('error', $exception->getMessage());
            // Redirect the user to the create credential settings page
            return redirect()->route('admin.kyc.providers.index');
        }
    }
}
