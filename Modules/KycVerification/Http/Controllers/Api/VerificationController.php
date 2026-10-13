<?php

namespace Modules\KycVerification\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Controller;
use Modules\KycVerification\Contract\VerificationContract;
use Modules\KycVerification\Processor\VerificationHandler;
use Modules\KycVerification\Http\Requests\User\AddressRequest;
use Modules\KycVerification\Http\Requests\User\VerificationRequest;

class VerificationController extends Controller
{
    protected $processor;

    /**
     * VerificationController constructor.
     *
     * This constructor assigns the verification provider and initializes
     * the processor and helper instances.
     */
    public function __construct()
    {
        // Assign the verification provider based on current settings
        VerificationHandler::assignVerificationProvider();

        // Initialize the verification processor
        $this->processor = app(VerificationContract::class);
    }

    /**
     * Return the verification requirements for identity verification.
     *
     * This method returns an array of the verification requirements for
     * the current provider. The array contains the requirement name as
     * the key and an array of the properties for that field as the value.
     * The properties include:
     *
     * - 'type': The type of the field (hidden, visible, etc.)
     * - 'required': Whether the field is required or not
     * - 'input_type': The type of input element to use for the field
     * - 'options': An array of options for the field, if applicable
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the
     *                                        verification requirements.
     */
    public function identityRequirements()
    {
        try {
            return $this->successResponse($this->processor->identityRequirements());
        } catch (Exception $exception) {
            return $this->unprocessableResponse($exception->getMessage());
        }
    }

    /**
     * Return the user's identity verification data.
     *
     * This method returns the user's identity verification data in the form of a
     * JSON response. The response contains the user's identity verification status,
     * identity type, and identity number (if applicable).
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the
     *                                        user's identity verification data.
     */
    public function identityData()
    {
        try {
            return $this->successResponse($this->processor->verificationData('identity'));
        } catch (Exception $exception) {
            return $this->unprocessableResponse($exception->getMessage());
        }

        
    }

    /**
     * Process user identity verification request
     *
     * This method processes the user verification request and displays
     * a success or error message based on the outcome.
     *
     * @param \Illuminate\Http\Request $request The request containing the
     *                                          verification details
     * @throws Exception If the verification fails.
     */
    public function processIdentity(VerificationRequest $request)
    {
        try {
            $response = $this->processor->processUserVerification($request);

            if (settings('kyc_provider') == 'manual') {
                unset($response['url']);
            }

            return $this->successResponse($response);

        } catch (Exception $exception) {

            return $this->unprocessableResponse($exception->getMessage());
        }
    }

    /**
     * Return the verification requirements for address verification.
     *
     * This method returns an array of the verification requirements for
     * the current provider. The array contains the requirement name as
     * the key and an array of the properties for that field as the value.
     * The properties include:
     *
     * - 'type': The type of the field (hidden, visible, etc.)
     * - 'required': Whether the field is required or not
     * - 'input_type': The type of input element to use for the field
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the
     *                                        verification requirements.
     */
    public function addressRequirements()
    {
        try {
            return $this->successResponse($this->processor->addressRequirements());
        } catch (Exception $exception) {
            return $this->unprocessableResponse($exception->getMessage());
        }
    }

    /**
     * Return the user's address verification data.
     *
     * This method returns the user's address verification data in the form of a
     * JSON response. The response contains the user's address verification status.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the
     *                                        user's address verification data.
     */
    public function addressData()
    {
        try {
            return $this->successResponse($this->processor->verificationData('address'));
        } catch (Exception $exception) {
            return $this->unprocessableResponse($exception->getMessage());
        }
    }

    /**
     * process user address verification request
     * @param \Illuminate\Http\AddressRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */

    public function processAddress(AddressRequest $request)
    {
        try {
            $this->processor->processAddressVerification($request);
            return $this->successResponse(__('User address verification updated successfully'));
        } catch (Exception $exception) {
            return $this->unprocessableResponse($exception->getMessage());
        }
    }
}
