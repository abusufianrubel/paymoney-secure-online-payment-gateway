<?php

namespace Modules\KycVerification\Http\Controllers\Admin;

use Excel;
use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\DocumentVerification;
use App\Facade\VerificationProviderManager;
use Modules\KycVerification\Entities\KycProvider;
use Modules\KycVerification\Exports\VerificationsExport;
use Modules\KycVerification\Datatable\VerificationsDatatable;
use Modules\KycVerification\Http\Requests\Admin\VerificationRequest;

class VerificationController extends Controller
{
    protected $kycVerification;
    public function __construct()
    {
        $this->kycVerification = new DocumentVerification();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(VerificationsDatatable $dataTable)
    {
        $data = [
            'menu' => 'kyc_verification',
            'sub_menu' => 'kyc_verifications',
            'statuses' => $this->kycVerification->groupBy('status')->get(['status']),
            'types' => $this->kycVerification->groupBy('verification_type')->get(['verification_type']),
            'providers' => $this->kycVerification::with('provider:id,name')->groupBy('provider_id')->get(['provider_id']),
            'from' => isset(request()->from) ? setDateForDb(request()->from) : null,
            'to' => isset(request()->to) ? setDateForDb(request()->to) : null,
            'provider' => isset(request()->provider) ? request()->provider : 'all',
            'type' => isset(request()->type) ? request()->type : 'all',
            'status' => isset(request()->status) ? request()->status : 'all',
        ];

        return $dataTable->render('kycverification::admin.verifications.index', $data);
    }

    /**
     * Display the edit form for the specified verification resource.
     *
     * This method retrieves the processor instance for the given provider ID
     * and attempts to show the admin verification view. If the processor instance
     * retrieval fails, it redirects back with an error message.
     *
     * @param App\Models\DocumentVerification $verification The verification entity to edit.
     * @return Illuminate\View\View The view for editing the verification or a redirect response.
     */
    public function edit(DocumentVerification $verification)
    {
        // Obtain the processor instance for the specific provider
        $processorInstance = $this->processorInstance($verification->provider_id);

        // Check if the processor instance retrieval failed
        if (is_array($processorInstance) && ! $processorInstance['status']) {
            // Display an error message and redirect to the verifications index page
            (new Common())->one_time_message('error', $processorInstance['message']);
            return redirect()->route('admin.kyc.verifications.index');
        }

        // Return the admin verification view for the specified verification
        return $processorInstance->adminVerificationView($verification);
    }

    /**
     * Update the specified resource in storage.
     *
     * This method processes the update request for a given verification resource.
     * It retrieves the processor instance for the specified provider and attempts
     * to update the verification. If the processor instance retrieval or update
     * fails, it redirects back with an error message.
     *
     * @param Modules\KycVerification\Http\Requests\Admin\VerificationRequest $request The request containing the update details.
     * @param App\Models\DocumentVerification $verification The verification entity to update.
     * @return Illuminate\Http\RedirectResponse A redirect response to the verifications index.
     */
    public function update(VerificationRequest $request, DocumentVerification $verification)
    {
        // Obtain the processor instance for the specific provider
        $processorInstance = $this->processorInstance($verification->provider_id);

        // Check if the processor instance retrieval failed
        if (is_array($processorInstance) && ! $processorInstance['status']) {
            // Display an error message and redirect to the verifications index page
            (new Common())->one_time_message('error', $processorInstance['message']);
            return redirect()->route('admin.kyc.verifications.index');
        }

        // Attempt to update the verification using the processor instance
        $response = $processorInstance->updateAdminVerification($request, $verification);

        // Display a success or error message based on the update response
        (new Common())->one_time_message(
            $response['status'] ? 'success' : 'error',
            $response['message']
        );

        // Redirect to the verifications index page
        return redirect()->route('admin.kyc.verifications.index');
    }

    /**
     * Export verifications list to CSV
     * @return \Illuminate\Http\RedirectResponse
     */

    public function csv()
    {
        return Excel::download(new VerificationsExport(), 'kyc_verifications_' . time() . '.csv');
    }

    /**
     * Export verifications list to PDF
     * @return \Illuminate\Http\RedirectResponse
     */

    public function pdf()
    {
        $from = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to = !empty(request()->endto) ? setDateForDb(request()->endto) : null;

        $data = [
            'date_range' => (isset($from) && isset($to)) ? $from . ' To ' . $to : 'N/A',
            'verifications' => $this->kycVerification->getVerifications()->get()
        ];

        generatePDF('kycverification::admin.verifications.pdf', 'verification_report_', $data);
    }

    /**
     * Get the verification processor instance.
     *
     * This method retrieves the processor instance for the specified provider ID.
     * If the provider or processor is not found, it returns an error message.
     *
     * @param int $providerId The ID of the provider.
     * @return array|Modules\KycVerification\Contract\VerificationContract The processor instance or an error message.
     */
    private function processorInstance($providerId)
    {
        // Retrieve the provider alias using the provider ID
        $provider = KycProvider::where('id', $providerId)->first(['alias']);

        // Check if the provider was found
        if (empty($provider)) {
            return [
                'status' => false,
                'message' => __('Verification provider not found.')
            ];
        }

        // Find the processor class from the alias
        $processor = VerificationProviderManager::find($provider->alias);

        // Check if the processor class was found
        if (empty($processor)) {
            return [
                'status' => false,
                'message' => __(":x is not found on the verification provider list.")
            ];
        }

        // Return the processor instance
        return app($processor);
    }
}
