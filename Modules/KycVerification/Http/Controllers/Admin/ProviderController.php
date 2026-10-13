<?php

namespace Modules\KycVerification\Http\Controllers\Admin;

use Cache;
use App\Models\Setting;
use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Facade\VerificationProviderManager;
use Modules\KycVerification\Entities\KycProvider;
use Modules\KycVerification\Contract\AutomatedContract;
use Modules\KycVerification\Datatable\ProvidersDatatable;
use Modules\KycVerification\Http\Requests\Admin\ProviderRequest;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param ProvidersDatatable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(ProvidersDatatable $dataTable)
    {
        $data = [
            'menu' => 'kyc_verification',
            'sub_menu' => 'kyc_providers',
        ];
        return $dataTable->render('kycverification::admin.providers.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param KycProvider $provider
     * @return \Illuminate\View\View
     */
    public function edit(KycProvider $provider)
    {
        $data = [
            'menu' => 'kyc_verification',
            'sub_menu' => 'kyc_providers',
            'provider' => $provider
        ];

        return view('kycverification::admin.providers.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param ProviderRequest $request
     * @param KycProvider $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProviderRequest $request, KycProvider $provider)
    {
        if ($request->is_default == 'Yes') {

            $response = $this->providerRequiredCredentialSettings($request->alias);

            if ($response['status']) {
                (new Common())->one_time_message('error', $response['message']);
                return redirect()->route('admin.kyc.providers.index');
            }
        }

        (new KycProvider())->updateProvider($request, $provider);
        (new Setting())->updateSettingsValue('kyc_provider', 'kyc_verification', $request->alias);
        Cache::forget(config('cache.prefix') . '-settings');
        (new Common())->one_time_message('success', __('The :x has been successfully saved.', ['x' => __('provider')]));
        return redirect()->route('admin.kyc.providers.index');
    }

    /**
     * Check if the provider requires credential settings.
     *
     * This method checks if the specified provider exists and whether it requires credentials for verification.
     * If the provider requires credentials and is not an instance of AutomatedContract, it returns an error message.
     *
     * @param string $providerAlias The alias of the provider.
     * @return array An array containing the status and message of the operation.
     */
    public function providerRequiredCredentialSettings($providerAlias)
    {
        // Attempt to find the provider using the VerificationProviderManager
        $provider = VerificationProviderManager::find($providerAlias);

        // If the provider is not found, return an error message
        if (empty($provider)) {
            return [
                'status' => true,
                'message' => __(":x is not found on the verification provider list.", ["x" => $providerAlias])
            ];
        }

        // Create an instance of the provider
        $providerInstance = app($provider);

        // Check if the provider requires credentials and is not an instance of AutomatedContract
        if (config("{$providerInstance->moduleAlias()}.credentials") && ! $providerInstance instanceof AutomatedContract) {
            return [
                'status' => true,
                'message' => __("Class :x must extend the :y class as it requires credentials for verification.", ['x' => $provider, 'y' => '\Modules\KycVerification\Contract\AutomatedContract'])
            ];
        }

        // Return a success status if no issues are found
        return [
            'status' => false,
        ];
    }
}
