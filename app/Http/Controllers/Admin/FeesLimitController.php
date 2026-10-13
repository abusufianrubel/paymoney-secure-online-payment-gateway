<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{Currency,
    TransactionType,
    PaymentMethod,
    FeesLimit
};

class FeesLimitController extends Controller
{
    protected $helper, $currency;

    public function __construct()
    {
        $this->helper   = new Common();
        $this->currency = new Currency();
    }

    public function limitList($tab, $currencyId)
    {
        $data['menu'] = 'currency';
        $data['list_menu'] = $tab;

        if ($tab == 'transfer') {
            $tab = 'Transferred';
        } elseif ($tab == 'exchange') {
            $tab = 'Exchange_From';
        } elseif ($tab == 'request_payment') {
            $tab = 'Request_Received';
        }

        $transactionType = TransactionType::where('name', ucfirst($tab))->first(['id', 'name']);
        $transactionTypeName = $transactionType->name;
        $data['transaction_type'] = $transactionType = $transactionType->id;
        $data['currency'] = $this->currency->getCurrency(['id' => $currencyId], ['id', 'default', 'name', 'type']);
        $type = $data['currency']->type;
        $data['currencyList'] = $this->currency->getAllCurrencies(['status' => 'Active', 'type' => $type], ['id', 'default', 'name', 'type']);
        $data['moduleAlias'] = '';

        $data['preference'] = ($type == 'fiat') ? preference('decimal_format_amount', 2) :  preference('decimal_format_amount_crypto', 8);

        $condition = ($type == 'fiat') ? getPaymoneySettings('payment_methods')['web']['fiat']['deposit'] : getPaymoneySettings('payment_methods')['web']['crypto']['deposit'];

        foreach (getCustomModules() as $module) {
            if (!empty(config($module->get('alias') . '.fees_limit_settings')) && in_array($transactionType, config($module->get('alias') . '.transaction_types'))) {
                foreach (config($module->get('alias') . '.' . 'fees_limit_settings') as $key => $moduleTransactionType) {

                    if(strtolower($transactionTypeName) != $moduleTransactionType['transaction_type']) continue;

                    $data['minAmountRequired'] = $moduleTransactionType['min_amount_require'];
                    $data['maxAmountRequired'] = $moduleTransactionType['max_amount_require'];
                    $data['displayName'] = $moduleTransactionType['display_name'];
                    $data['moduleAlias'] = $module->get('alias');


                    if ($moduleTransactionType['payment_method'] == 'Single') {
                        $data['feeslimit'] = FeesLimit::where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId])->first();
                        return view('admin.feeslimits.deposit_limit_single', $data);
                    }

                    $paymentMethods = config($module->get('alias') . '.' . 'payment_methods')[strtolower($transactionTypeName)]; 

                    $key = array_search('Wallet', $paymentMethods);

                    if ($key !== false) {
                        $paymentMethods[$key] = 'Mts';
                    }

                    $data['payment_methods'] = PaymentMethod::with([
                        'fees_limit' => function ($query) use ($transactionType, $currencyId)
                            {
                                $query->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId]);
                            }
                        ])
                        ->whereIn('name', $paymentMethods) 
                        ->whereIn('id', $condition) 
                        ->where('status', 'Active')
                        ->get(['id', 'name']);
                    return view('admin.feeslimits.deposit_limit', $data);
                }
            }
        }

        if ($tab == 'deposit') {
            $condition = ($type == 'fiat') ? getPaymoneySettings('payment_methods')['web']['fiat']['deposit'] : getPaymoneySettings('payment_methods')['web']['crypto']['deposit'];

            $data['payment_methods'] = PaymentMethod::with(['fees_limit' => function ($q) use ($transactionType, $currencyId)
                                        {
                                            $q->where('transaction_type_id', '=', $transactionType)->where('currency_id', '=', $currencyId);
                                        }])
                                        ->whereIn('id', $condition) 
                                        ->where(['status' => 'Active'])
                                        ->get(['id', 'name']);
            return view('admin.feeslimits.deposit_limit', $data);

        } else if ($tab == 'withdrawal') {

            $condition = ($type == 'fiat') ? getPaymoneySettings('payment_methods')['web']['fiat']['withdrawal'] : getPaymoneySettings('payment_methods')['web']['crypto']['withdrawal'];

            $data['payment_methods'] = PaymentMethod::with(['fees_limit' => function ($q) use ($transactionType, $currencyId)
                                        {
                                            $q->where('transaction_type_id', '=', $transactionType)->where('currency_id', '=', $currencyId);
                                        }])
                                        ->whereIn('id', $condition)
                                        ->where(['status' => 'Active'])
                                        ->get(['id', 'name']);
            return view('admin.feeslimits.deposit_limit', $data);
            
        } else {

            $data['feeslimit'] = FeesLimit::where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId])->first();
            return view('admin.feeslimits.deposit_limit_single', $data);
        }
    }

    public function updateDepositLimit(Request $request)
    {
        $paymentMethodId  = $request->payment_method_id;
        $minLimit         = $request->min_limit;
        $maxLimit         = $request->max_limit;
        $chargePercentage = $request->charge_percentage;
        $chargeFixed      = $request->charge_fixed;
        $hasTransaction   = $request->has_transaction;

        if (is_array($paymentMethodId)) {

            foreach ($paymentMethodId as $key => $value) {

                $feesLimit = FeesLimit::where(['transaction_type_id' => $request->transaction_type, 'currency_id' => $request->currency_id, 'payment_method_id' => $value])->first();
                
                if (empty($feesLimit)) {
                    $feesLimit                      = new FeesLimit();
                    $feesLimit->currency_id         = $request->currency_id;
                    $feesLimit->transaction_type_id = $request->transaction_type;
                    $feesLimit->payment_method_id   = $value;
                    $feesLimit->charge_percentage   = $chargePercentage[$key];
                    $feesLimit->charge_fixed        = $chargeFixed[$key];
                    $feesLimit->min_limit           = self::getMinLimit($minLimit, $key);
                    $feesLimit->max_limit           = isset($maxLimit) ? $maxLimit[$key] : null;
                    $feesLimit->has_transaction     = $request->defaultCurrency 
                                                        ? 'Yes' 
                                                        : (isset($hasTransaction[$value]) ? $hasTransaction[$value] : 'No');
                    $feesLimit->save();

                } else {
                    $feesLimit = FeesLimit::where(['transaction_type_id' => $request->transaction_type, 'currency_id' => $request->currency_id, 'payment_method_id' => $value])->first();

                    $feesLimit->currency_id         = $request->currency_id;
                    $feesLimit->transaction_type_id = $request->transaction_type;
                    $feesLimit->payment_method_id   = $value;
                    $feesLimit->charge_percentage   = $chargePercentage[$key];
                    $feesLimit->charge_fixed        = $chargeFixed[$key];
                    $feesLimit->min_limit           = self::getMinLimit($minLimit, $key);
                    $feesLimit->max_limit           = isset($maxLimit) ? $maxLimit[$key] : null;
                    $feesLimit->has_transaction     = $request->defaultCurrency 
                                                        ? 'Yes' 
                                                        : (isset($hasTransaction[$value]) ? $hasTransaction[$value] : 'No');
                    $feesLimit->save();
                }
            }

        } else {

            $feesLimit = FeesLimit::where(['transaction_type_id' => $request->transaction_type, 'currency_id' => $request->currency_id])->first();

            if (empty($feesLimit)) {
                $feesLimit                      = new FeesLimit();
                $feesLimit->currency_id         = $request->currency_id;
                $feesLimit->transaction_type_id = $request->transaction_type;
                $feesLimit->charge_percentage   = $chargePercentage;
                $feesLimit->charge_fixed        = $chargeFixed;
                $feesLimit->min_limit           = ($minLimit == null) ? 1.00000000 : $minLimit;
                $feesLimit->max_limit           = $maxLimit;
                $feesLimit->has_transaction     = $request->defaultCurrency 
                                                    ? 'Yes' 
                                                    : (isset($hasTransaction) ? $hasTransaction : 'No');
                $feesLimit->save();
            } else {
                $feesLimit                      = FeesLimit::find($request->id);
                $feesLimit->currency_id         = $request->currency_id;
                $feesLimit->transaction_type_id = $request->transaction_type;
                $feesLimit->charge_percentage   = $chargePercentage;
                $feesLimit->charge_fixed        = $chargeFixed;
                $feesLimit->min_limit           = ($minLimit == null) ? 1.00000000 : $minLimit;
                $feesLimit->max_limit           = $maxLimit;
                $feesLimit->has_transaction     = $request->defaultCurrency 
                                                    ? 'Yes' 
                                                    : (isset($hasTransaction) ? $hasTransaction : 'No');
                $feesLimit->save();
            }
        }

        $this->helper->one_time_message('success', __('The :x has been successfully saved.', ['x' => __('currency settings')]));

        return redirect(config('adminPrefix').'/settings/feeslimit/' . $request->tabText . '/' . $request->currency_id);
    }

    public function getFesslimitDetails(Request $request)
    {
        $data = [];
        $transactionType = $request->transaction_type;
        $currencyId = $request->currency_id;
        $moduleAlias = $request->module_alias;
        $type = Currency::where('id', $currencyId)->value('type');

        $feesLimit = getModuleTransactionTypeFeesLimit($transactionType, $currencyId, $type, $moduleAlias);
        if (!empty($moduleAlias)) {

            if (isset($feesLimit) && $feesLimit) {
                return [
                    'status' => 200,
                    'feeslimit' => $feesLimit
                ];
            }
        }
        
        if ($transactionType == Deposit) {

            $condition = ($type == 'fiat') ? getPaymoneySettings('payment_methods')['web']['fiat']['deposit'] : getPaymoneySettings('payment_methods')['web']['crypto']['deposit'];

            $feeslimit = PaymentMethod::with([
                'fees_limit' => function ($query) use ($transactionType, $currencyId)
                {
                    $query->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId]);
                }
            ])
            ->whereIn('id', $condition)
            ->where('status', 'Active')
            ->get(['id', 'name']);

        } else if ($transactionType == Withdrawal) {

            $condition = ($type == 'fiat') ? getPaymoneySettings('payment_methods')['web']['fiat']['withdrawal'] : getPaymoneySettings('payment_methods')['web']['crypto']['withdrawal'];

            $feeslimit = PaymentMethod::with([
                'fees_limit' => function ($query) use ($transactionType, $currencyId)
                {
                    $query->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId]);
                }
            ])
            ->whereIn('id', $condition)
            ->where('status', 'Active')
            ->get(['id', 'name']);

        } else {
            $feeslimit = FeesLimit::where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId])->first();
        }

        if (empty($feeslimit)) {
            $data['status'] = 401;
        } else {
            $data['status']    = 200;
            $data['feeslimit'] = $feeslimit;
        }
        return $data;
    }

    public function getSpecificCurrencyDetails(Request $request)
    {
        $data = [];
        $transactionType = $request->transaction_type;
        $currencyId = $request->currency_id;
        $moduleAlias = $request->module_alias;
        $type = Currency::where('id', $currencyId)->value('type');
        $currency = $this->currency->getCurrency(['id' => $currencyId], ['id', 'name', 'symbol']);

        if (!empty($moduleAlias)) {

            $feesLimit = getModuleTransactionTypeFeesLimit($transactionType, $currencyId, $type, $moduleAlias);

            if ($currency && isset($feesLimit) && $feesLimit) {
                return [
                    'status' => 200,
                    'currency' => $currency,
                    'feeslimit' => $feesLimit
                ];
            }
        }

        if ($transactionType == Deposit) {

            $condition = ($type == 'fiat') ? getPaymoneySettings('payment_methods')['web']['fiat']['deposit'] : getPaymoneySettings('payment_methods')['web']['crypto']['deposit'];
            
            $feesLimit = PaymentMethod::with([
                'fees_limit' => function ($q) use ($transactionType, $currencyId)
                {
                    $q->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId]);
                }
            ])
            ->whereIn('id', $condition)
            ->where('status', 'Active')
            ->get(['id', 'name']);

        } else if ($transactionType == Withdrawal) {

            $condition = ($type == 'fiat') ? getPaymoneySettings('payment_methods')['web']['fiat']['withdrawal'] : getPaymoneySettings('payment_methods')['web']['crypto']['withdrawal'];

            $feesLimit = PaymentMethod::with([
                'fees_limit' => function ($q) use ($transactionType, $currencyId)
                {
                    $q->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId]);
                }
            ])
            ->whereIn('id', $condition)
            ->where('status', 'Active')
            ->get(['id', 'name']);
            
        } else {
            $feesLimit = FeesLimit::where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId])->first();
        }

        if ($currency && $feesLimit) {
            $data['status']    = 200;
            $data['currency']  = $currency;
            $data['feeslimit'] = $feesLimit;
        } else {
            $data['status']   = 401;
            $data['currency'] = $currency;
        }
        return $data;
    }

    private function getMinLimit($minLimit, $key)
    {
        if (!isset($minLimit)) {
            return 1.00000000;
        }

        return ($minLimit[$key] == null)  ? 1.00000000 : $minLimit[$key];
    }
}
