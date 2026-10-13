<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use Cache, Common;
use App\Http\Controllers\Controller;
use Modules\Virtualcard\Http\Requests\Admin\UpdatePreferenceRequest;

class VirtualcardPreferenceController extends Controller
{
    public function create()
    {
        return view('virtualcard::admin.preferences.create', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'preference'
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(UpdatePreferenceRequest $request)
    {
        if (checkDemoEnvironment()) {
            Common::one_time_message('warning', __('Preference update is not allowed on the demo site.'));
            return redirect()->back();
        }
        
        collect($request->except('_token'))->each(function ($value, $key) {
            \App\Models\Preference::where([
                'category' => 'virtualcard',
                'field' => $key,
            ])->update(['value' => $value]);
        });

        Cache::forget(config('cache.prefix') . '-preferences');

        (new Common)->one_time_message('success', __('The :x has been successfully saved.', ['x' => __('preference')]));
        return back();
    }
}
