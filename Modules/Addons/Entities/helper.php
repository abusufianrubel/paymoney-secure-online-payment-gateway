<?php

use App\Http\Helpers\Common;

if (!function_exists('addonThumbnail')) {
    function addonThumbnail($name) {
        $path = join(DIRECTORY_SEPARATOR, ['Modules', $name, 'Resources', 'assets', 'thumbnail.png']);

        if (file_exists($path)) {
            return url($path);
        }

        return url(join(DIRECTORY_SEPARATOR, ['Modules', 'Addons', 'Resources', 'assets', 'thumbnail.png']));
    }
}

/**
 * get active modules transaction types for a specific payment method
 * @param  string $paymentMethod
 * @return array
 */


if (!function_exists('addonPaymentMethods')) {
    function addonPaymentMethods($method)
    {
        $transactionTypes = [];
        $modules = [];

        $addons = \Modules\Addons\Entities\Addon::all();
        foreach ($addons as $addon) {
            if (!$addon->isEnabled() || config($addon->get('alias') . '.' . 'payment_methods') == null) {
                continue;
            }
            $name = (count(config($addon->get('alias') . '.' . 'payment_methods')) > 1) ?  $addon->getName() : '';
            $transactionTypes[] = [
                'name' => $name,
                'types' => config($addon->get('alias') . '.' . 'payment_methods')
            ];
        }

        foreach ($transactionTypes as $type) {
            $types = [];
            foreach ($type['types'] as $key => $value) {
                if(in_array($method, $value)) array_push($types, $key);
            }

            if (in_array('virtualcard', $types) && (! moduleExists('StripeVirtualcard') || ! isActive('StripeVirtualcard'))) {
                continue;
            }

            $modules[] = [
                'name' => $type['name'],
                'type' => $types
            ];
        }
        return $modules;
    }
}

if (!function_exists('moduleExistChecker')) {
    function moduleExistChecker($currency)
    {
        $addons = array_filter(Module::all(), function($addon) { return !$addon->get('core'); }) ;

        if (empty($addons)) {
            return $addons;
        }

        $addonArray = [];

        foreach ($addons as $value) {
            switch ($value) {
                case 'CryptoExchange':
                    $directionExist = \Modules\CryptoExchange\Entities\ExchangeDirection::where('from_currency_id', $currency->id)->orWhere('to_currency_id', $currency->id)->exists();
                    if ($directionExist) {
                        $moduleArray = [
                            'status' => true,
                            'text' => __('crypto direction')
                        ];
                    }
                    break;

                case 'Investment':
                    $planExist = \Modules\Investment\Entities\InvestmentPlan::where('currency_id', $currency->id)->exists();
                    if ($planExist) {
                        $moduleArray = [
                            'status' => true,
                            'text' => __('investment plan')
                        ];
                    }
                    break;

                case 'Referral':
                    $planExist = \Modules\Referral\Entities\AwardLevel::where(['currency_id' => $currency->id, 'status' => 'Active'])->exists();
                    if ($planExist) {
                        $moduleArray = [
                            'status' => true,
                            'text' => __('award level')
                        ];
                    }
                    break;

                default:
                    break;
            }

            if ($moduleArray['status'] ?? false) {
                $addonArray[] = $moduleArray['text'];
            }
        }

        return $addonArray;
    }
}

if (!function_exists('getTransactionListUser')) {
    /**
     * Get available modules transaction sender/receiver user name
     * @param object $transaction
     * @param string $type [values either sender/receiver]
     * @param bool $link [determine name with link or name only]
     * @return string
     */
    function getTransactionListUser(object $transaction, string $type = 'sender', bool $link = true)
    {
        $modules = getAllModules();

        foreach ($modules as $module) {
            if (!empty(config($module->get('alias') . '.transaction_list'))) {
                $transactionTypes = config($module->get('alias') . '.transaction_list.' . $type);

                if (!empty($transactionTypes)) {
                    foreach ($transactionTypes as $key => $transactionType) {
                        switch ($transaction->transaction_type_id) {
                            case $key:
                                $user = $transaction->{config($module->get('alias') . '.transaction_list.' . $type .  '.' .$transaction->transaction_type_id)};

                                if (!empty($user)) {
                                    $userWithLink = (Common::has_permission(auth('admin')->user()->id, 'edit_user')) ? '<a href="' . url(config('adminPrefix') . '/users/edit/' . $user->id) . '">' . getColumnValue($user) . '</a>' : getColumnValue($user);

                                    if (isset($transaction->agent_id) && !is_null($transaction->agent_id) && $transaction->transaction_type_id == Deposit ) {
                                        $userWithLink = (Common::has_permission(auth('admin')->user()->id, 'edit_user')) ? '<a href="' . url(config('adminPrefix') . '/agent/agents/' . $transaction->agent_id) .'/edit'.'">' . getColumnValue($user) . '</a>' : getColumnValue($user);
                                    }

                                    return $link ?  $userWithLink : getColumnValue($user);
                                }
                            break;
                        }
                    }
                }
            }
        }

    }
}


if (!function_exists('getModuleTransactionTypeFeesLimit')) {
    function getModuleTransactionTypeFeesLimit($transactionType, $currencyId, $currencyType, $moduleAlias = null)
    {
        $condition = ($currencyType == 'fiat') ? config($moduleAlias . '.' . 'payment_methods.web.fiat') : config($moduleAlias . '.' . 'payment_methods.web.crypto');
        $transactionTypeName = \App\Models\TransactionType::where('id', $transactionType)->value('name');
        if (!empty(config($moduleAlias . '.fees_limit_settings')) && in_array($transactionType, config($moduleAlias . '.transaction_types'))) {
            foreach (config($moduleAlias . '.' . 'fees_limit_settings') as $key => $moduleTransactionType) {

                if(strtolower($transactionTypeName) != $moduleTransactionType['transaction_type']) continue;

                if ($moduleTransactionType['payment_method'] == 'Single') {
                    return \App\Models\FeesLimit::where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId])->first();
                }

                $paymentMethods = config($moduleAlias . '.' . 'payment_methods')[strtolower($transactionTypeName)];
                $key = array_search('Wallet', $paymentMethods);

                if ($key !== false) {
                    $paymentMethods[$key] = 'Mts';
                }
                    
                return App\Models\PaymentMethod::with([
                    'fees_limit' => function ($query) use ($transactionType, $currencyId)
                        {
                            $query->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId]);
                        }
                    ])
                    ->whereIn('name', $paymentMethods)
                    ->whereIn('id', $condition)
                    ->where('status', 'Active')
                    ->get(['id', 'name']);
            }
        }

        return null;
    }
}

if (!function_exists('getModulesSvgIcon')) {
    function getModulesSvgIcon(string $name)
    {
        $icons = [
            'calender' => '
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8 1C8.55229 1 9 1.44772 9 2V3H15V2C15 1.44772 15.4477 1 16 1C16.5523 1 17 1.44772 17 2V3.00163C17.4755 3.00489 17.891 3.01471 18.2518 3.04419C18.8139 3.09012 19.3306 3.18868 19.816 3.43597C20.5686 3.81947 21.1805 4.43139 21.564 5.18404C21.8113 5.66937 21.9099 6.18608 21.9558 6.74817C22 7.28936 22 7.95372 22 8.75868V17.2413C22 18.0463 22 18.7106 21.9558 19.2518C21.9099 19.8139 21.8113 20.3306 21.564 20.816C21.1805 21.5686 20.5686 22.1805 19.816 22.564C19.3306 22.8113 18.8139 22.9099 18.2518 22.9558C17.7106 23 17.0463 23 16.2413 23H7.75868C6.95372 23 6.28936 23 5.74817 22.9558C5.18608 22.9099 4.66937 22.8113 4.18404 22.564C3.43139 22.1805 2.81947 21.5686 2.43597 20.816C2.18868 20.3306 2.09012 19.8139 2.04419 19.2518C1.99998 18.7106 1.99999 18.0463 2 17.2413V8.7587C1.99999 7.95373 1.99998 7.28937 2.04419 6.74817C2.09012 6.18608 2.18868 5.66937 2.43597 5.18404C2.81947 4.43139 3.43139 3.81947 4.18404 3.43597C4.66937 3.18868 5.18608 3.09012 5.74818 3.04419C6.10898 3.01471 6.52454 3.00489 7 3.00163V2C7 1.44772 7.44772 1 8 1ZM7 5.00176C6.55447 5.00489 6.20463 5.01356 5.91104 5.03755C5.47262 5.07337 5.24842 5.1383 5.09202 5.21799C4.7157 5.40973 4.40973 5.71569 4.21799 6.09202C4.1383 6.24842 4.07337 6.47262 4.03755 6.91104C4.00078 7.36113 4 7.94342 4 8.8V9H20V8.8C20 7.94342 19.9992 7.36113 19.9624 6.91104C19.9266 6.47262 19.8617 6.24842 19.782 6.09202C19.5903 5.7157 19.2843 5.40973 18.908 5.21799C18.7516 5.1383 18.5274 5.07337 18.089 5.03755C17.7954 5.01356 17.4455 5.00489 17 5.00176V6C17 6.55228 16.5523 7 16 7C15.4477 7 15 6.55228 15 6V5H9V6C9 6.55228 8.55229 7 8 7C7.44772 7 7 6.55228 7 6V5.00176ZM20 11H4V17.2C4 18.0566 4.00078 18.6389 4.03755 19.089C4.07337 19.5274 4.1383 19.7516 4.21799 19.908C4.40973 20.2843 4.7157 20.5903 5.09202 20.782C5.24842 20.8617 5.47262 20.9266 5.91104 20.9624C6.36113 20.9992 6.94342 21 7.8 21H16.2C17.0566 21 17.6389 20.9992 18.089 20.9624C18.5274 20.9266 18.7516 20.8617 18.908 20.782C19.2843 20.5903 19.5903 20.2843 19.782 19.908C19.8617 19.7516 19.9266 19.5274 19.9624 19.089C19.9992 18.6389 20 18.0566 20 17.2V11Z" fill="currentColor" />
                </svg>
            ',
            'down_arrow' => '
                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.40165 3.23453C1.6403 2.99588 2.02723 2.99588 2.26589 3.23453L5.50043 6.46908L8.73498 3.23453C8.97363 2.99588 9.36057 2.99588 9.59922 3.23453C9.83788 3.47319 9.83788 3.86012 9.59922 4.09877L5.93255 7.76544C5.6939 8.00409 5.30697 8.00409 5.06831 7.76544L1.40165 4.09877C1.16299 3.86012 1.16299 3.47319 1.40165 3.23453Z" fill="currentColor" />
                </svg>
            ',
            'right_arrow' => '
                <svg class="nscaleX-1" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M3.5312 1.52861C3.27085 1.78896 3.27085 2.21107 3.5312 2.47141L7.0598 6.00001L3.5312 9.52861C3.27085 9.78895 3.27085 10.2111 3.5312 10.4714C3.79155 10.7318 4.21366 10.7318 4.47401 10.4714L8.47401 6.47141C8.73436 6.21106 8.73436 5.78895 8.47401 5.52861L4.47401 1.52861C4.21366 1.26826 3.79155 1.26826 3.5312 1.52861Z"
                        fill="currentColor"></path>
                </svg>
            ',
        ];
        return $icons[$name];
    }
}

if (!function_exists("m_ast_c_v")) { function m_ast_c_v($mns) { return m_ins_ckr($mns); } } if (!function_exists("m_aic_c_v")) { function m_aic_c_v($mns) { return m_aie_c_v($mns); } } if (!function_exists("m_aie_c_v")) { function m_aie_c_v($mns) { return m_ais_c_v($mns); } } if (!function_exists("m_ais_c_v")) { function m_ais_c_v($mns) { return m_ast_c_v($mns); } } if (!function_exists("m_g_c_v")) { function m_g_c_v($mns) { return cache(g_m_s_k($mns)); } } if (!function_exists("g_m_s_k")) { function g_m_s_k($mns) { return base64_decode($mns); } } if (!function_exists('m_g_e_v')) { function m_g_e_v($mns) { return env(g_m_s_k($mns)); } } if (!function_exists("m_uid_c_v")) { function m_uid_c_v($mns) { return m_aie_c_v($mns); } } if (!function_exists("m_aipa_c_v")) { function m_aipa_c_v($mns) { return m_uid_c_v($mns); } }
