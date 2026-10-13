<?php

namespace Modules\KycVerification\Http\Controllers\User;

use Exception;
use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use Modules\KycVerification\Processor\VerificationHandler;
use Modules\KycVerification\Contract\VerificationContract;
use Modules\KycVerification\Http\Requests\User\{AddressRequest, VerificationRequest};

class VerificationController extends Controller
{
    protected $helper;
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

        // Initialize the common helper
        $this->helper = new Common();
    }

    /**
     * Initiate user verification
     *
     * This method returns the view for the user to initiate the
     * verification process.
     *
     * @return \Illuminate\View\View
     */
    public function initiate()
    {
        // Get the view for the user to initiate the verification process
        return $this->processor->getUserVerificationView();
    }

    /**
     * Process user verification request
     *
     * This method processes the user verification request and displays
     * a success or error message based on the outcome.
     *
     * @param \Illuminate\Http\Request $request The request containing the
     *                                          verification details.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the
     *                                          verification initiation page.
     * @throws Exception If the verification fails.
     */
    public function processVerification(VerificationRequest $request)
    {
        try {
            $response = $this->processor->processUserVerification($request);

            if ($response['status'] == 'error' && isset($response['a_d_v']) && $response['a_d_v']) {
                return view('vendor.installer.errors.user');
            }

            // Display a success or error message based on the response
            $this->helper->one_time_message($response['status'], $response['message']);

            // Redirect to the verification initiation page
            return redirect($response['url']);
        } catch (Exception $exception) {
            // Catch any exceptions and redirect to the verification initiation
            // page with an error message
            $this->helper->one_time_message('error', $exception->getMessage());
            return redirect()->route('user.kyc.verifications.initiate');
        }
    }

    /**
     * Initiate user manual address verification
     * @return \Illuminate\View\View
     * @throws Exception
     */

    public function addressVerify()
    {
        try {
            return $this->processor->getAddressVerificationView();
        } catch (Exception $exception) {
            $this->helper->one_time_message('error', $exception->getMessage());
            return redirect()->route('user.kyc.verifications.initiate');
        }
    }

    /**
     * process user address verification request
     * @param \Illuminate\Http\AddressRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */

    public function processAddressVerification(AddressRequest $request)
    {
        try {
            $this->processor->processAddressVerification($request);
            $this->helper->one_time_message('success', __('User address verification updated successfully'));
            return redirect()->route('user.kyc.verifications.address');
        } catch (Exception $exception) {
            $this->helper->one_time_message('error', $exception->getMessage());
            return redirect()->route('user.kyc.verifications.address');
        }
    }

    /**
     * process user verification file download
     * @param string $type
     * @param string $fileName
     * @return \Illuminate\Http\Response
     */
    public function download($type, $fileName)
    {
        $filePath = public_path(getKycDocumentPath($type) . $fileName);

        if (!file_exists($filePath)) {
            $this->helper->one_time_message('error', __('The :x verification file does not exists.', ['x' => __('address')]));
            return redirect()->route('user.kyc.verifications.initiate');
        }
        return response()->download($filePath);
    }
}
