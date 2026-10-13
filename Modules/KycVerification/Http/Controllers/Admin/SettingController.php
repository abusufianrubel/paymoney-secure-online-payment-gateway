<?php

namespace Modules\KycVerification\Http\Controllers\Admin;

use Cache;
use App\Http\Helpers\Common;
use App\Models\{Role, Setting};
use App\Http\Controllers\Controller;
use Modules\KycVerification\Http\Requests\Admin\SettingRequest;

class SettingController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * @return Illuminate\View\View
     */
    public function create()
    {
        $data = [
            'menu' => 'kyc_verification',
            'sub_menu' => 'kyc_settings',
            'result' => settings('kyc_verification'),
            'roles' => Role::select('id', 'display_name')->where('user_type', "User")->get()
        ];

        return view('kycverification::admin.setting', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param SettingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SettingRequest $request)
    {
        if ($request->kyc_mandatory == 'Yes' && checkDemoEnvironment()) {
            (new Common())->one_time_message('warning', __('KYC Verification cannot be mandated on the demo version.'));
            return redirect()->route('admin.kyc.settings.create');
        }
        
        (new Setting())->updateSettingsValue('kyc_mandatory', 'kyc_verification', $request->kyc_mandatory);
        (new Setting())->updateSettingsValue('kyc_required_for', 'kyc_verification', $request->kyc_required_for);

        Cache::forget(config('cache.prefix') . '-settings');

        (new Common())->one_time_message('success', __("The :x has been successfully saved.", ['x' => __('verification settings')]));
        return redirect()->route('admin.kyc.settings.create');
    }
}
