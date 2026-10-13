<?php

namespace Modules\TatumIo\Http\Controllers\Admin;

use App\Http\Helpers\Common;
use App\Models\CryptoProvider;
use App\Models\Currency;
use Modules\TatumIo\Traits\TokenTrait;
use Exception, Cache;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\TatumIo\Entities\CryptoToken;
use Modules\TatumIo\Http\Requests\TatumTokenStoreRequest;

class TatumIoTokenController extends Controller
{
    use TokenTrait;

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function index()
    {
        $data['menu'] = 'crypto_token';
        $data['tokens'] = CryptoToken::get();

        $data['provider'] = CryptoProvider::with([
            'cryptoAssetSettings:id,currency_id,crypto_provider_id,network,network_credentials,status'
        ])->where('alias', 'tatumio')
        ->first();

        $cacheKey = 'crypto-token';

        if (!Cache::has($cacheKey)) {
            $this->getAllToken();
            Cache::put($cacheKey, true, now()->addMinutes(20));
        } 

        return view('tatumio::admin.token.index', $data);
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $data['menu'] = 'crypto_token';
        $data['networks'] = $network = $this->tokenSupportedNetwork();
        $data['addresses'] = $this->supportedNetworkAddress();

        if (count($network) == 0) {
            (new Common())->one_time_message('error', __('Token creation is only available for the Tron network. Please create a Tron asset first.'));
            return redirect()->route('admin.crypto_providers.list', 'TatumIo');
        }

        return view('tatumio::admin.token.create', $data);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(TatumTokenStoreRequest $request)
    {
        try {
            DB::beginTransaction();   
            $token = $this->createTatumIoERC20Token($request);
            $currency = $this->storeTokenCurrency($request);
            $this->createToken($request, $token, $currency);
            Cache::forget('crypto_token');
            DB::commit();
            (new Common())->one_time_message('success', __('Token Created successfully.'));
            return redirect()->route('admin.tatumio.token');
        } catch (Exception $e) {
            DB::rollBack();
            (new Common())->one_time_message('error', $e->getMessage());
            return redirect()->route('admin.tatumio.token.create')->withInput();
        }

    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function edit($id)
    {
        $data['menu'] = 'crypto_token';
        $data['cryptoToken'] = CryptoToken::find(decrypt($id));
        return view('tatumio::admin.token.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        try {
            $cryptoToken = CryptoToken::find(decrypt($id));
            $cryptoToken->status = $request->status;
            $cryptoToken->save(); 

            $currency = Currency::find($cryptoToken->currency_id);
            $currency->status = $request->status;
            if ($request->hasFile('logo')) {
                $networkLogo = $request->file('logo');
                if (isset($networkLogo)) {
                    $response = uploadImage($networkLogo, 'public/uploads/currency_logos/', '64*64');
                    if ($response['status'] === true) {
                        $currency->logo = $response['file_name'];
                    }
                }
            }
            $currency->save();

            if (isset($request->create_wallet) && $request->create_wallet == 'on') {
                $this->createUsersTokenWallet($currency->id);
            }

            (new Common())->one_time_message('success', __('Token Updated successfully.'));
            return redirect()->route('admin.tatumio.token');
        } catch (Exception $e) {
            (new Common())->one_time_message('error', $e->getMessage());
            return redirect()->route('admin.tatumio.token');
        }
        


    }

    public function storeTokenCurrency($request)
    {
        try {
            $currency =  new \App\Models\Currency();
            $currency->type = 'crypto_token';
            $currency->name = $request->name;
            $currency->symbol = strtoupper($request->symbol);
            $currency->code = strtoupper($request->symbol);

            if ($request->hasFile('logo')) {
                $networkLogo = $request->file('logo');
                if (isset($networkLogo)) {
                    $response = uploadImage($networkLogo, 'public/uploads/currency_logos/', '64*64');
                    if ($response['status'] === true) {
                        $currency->logo = $response['file_name'];
                    }
                }
            }

            $currency->status  = ($request->status == 'Active') ? 'Active' : 'Inactive';
            $currency->save();

            return $currency;
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        } 
    }


    public function createToken($request, $token, $currency)
    {

        try {
            $cryptoToken =  new CryptoToken();
            $cryptoToken->name = $request->name;
            $cryptoToken->currency_id = $currency->id;
            $cryptoToken->txid = $token;
            $cryptoToken->network = $request->network;
            $cryptoToken->symbol = strtoupper($currency->symbol);
            $cryptoToken->decimals = $request->decimals;
            $cryptoToken->address = null;
            $cryptoToken->value = $request->total_supply;
            $cryptoToken->status = 'Active';
            $cryptoToken->save();

            if (isset($request->create_wallet) && $request->create_wallet == 'on') {
                $this->createUsersTokenWallet($currency->id);
            }

        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }

    }

    public function adjust()
    {
        $this->getAllToken();
        (new Common())->one_time_message('success', __('Adjusted Successfully'));
        return redirect()->route('admin.tatumio.token');
    }

    protected function createUsersTokenWallet($currencyId)
    {
        try {
            $users = \App\Models\User::with(['wallets' => function ($q) use ($currencyId)
            {
                $q->where(['currency_id' => $currencyId]);
            }])
            ->where(['status' => 'Active'])
            ->get(['id', 'email']);

            if (!empty($users)) {
                foreach ($users as $user) {
                    $getWalletObject = (new Common)->getUserWallet([], ['user_id' => $user->id, 'currency_id' => $currencyId], ['id']);
                    if (empty($getWalletObject) && count($user->wallets) == 0) {
                        $wallet              = new \App\Models\Wallet();
                        $wallet->user_id     = $user->id;
                        $wallet->currency_id = $currencyId;
                        $wallet->is_default  = 'No';
                        $wallet->save();
                    }
                }
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }
}
