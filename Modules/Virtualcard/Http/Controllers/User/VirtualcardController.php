<?php

namespace Modules\Virtualcard\Http\Controllers\User;

use App\Models\{
    Wallet,
    Currency
};
use Spatie\QueryBuilder\{
    QueryBuilder,
    AllowedFilter
};
use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardHolder,
    VirtualcardSpending,
    VirtualcardCategory,
    VirtualcardFeeslimit,
    WebhookTransaction
};
use App\Http\Controllers\{
    Controller,
    Users\EmailController
};
use Modules\Virtualcard\{
    Events\VirtualcardRequest,
    Http\Requests\User\StoreVirtualcardRequest
};
use Carbon\Carbon, Exception;
use Illuminate\Http\Request;

class VirtualcardController extends Controller
{
    public function index()
    {
        $cardholders = VirtualcardHolder::where(['user_id' => auth()->id(), 'status' => 'Approved'])->get();
        $currencyCodes = VirtualcardFeeslimit::whereStatus('Active')->pluck('virtualcard_currency_code')->toArray();
        $currencies = Currency::whereIn('code', $currencyCodes)->get(['id', 'code']);
        $categories = VirtualcardCategory::whereStatus('active')->get(['id', 'name']);

        $virtualcards = QueryBuilder::for(Virtualcard::with([
                                        'virtualcardProvider',
                                        'virtualcardCategory',
                                        'virtualcardHolder',
                                        'virtualcardHolder.user'
                                    ]))
                                    ->allowedFilters([
                                        AllowedFilter::exact('status', 'status'),
                                        AllowedFilter::exact('brand', 'card_brand'),
                                        AllowedFilter::exact('currency', 'currency_code'),
                                        AllowedFilter::callback('from', function ($query, $value) {
                                            $query->where('created_at', '>=', Carbon::parse(setDateForDb($value))->startOfDay() );
                                        }),
                                        AllowedFilter::callback('to', function ($query, $value) {
                                            $query->where('created_at', '<=', Carbon::parse(setDateForDb($value))->endOfDay() );
                                        }),
                                    ])
                                    ->whereHas('virtualcardHolder.user', function($query) {
                                        $query->where('user_id', auth()->id());
                                    })
                                    ->orderBy('id', 'desc')
                                    ->paginate(10);

        return view('virtualcard::user.virtualcards.index', [

            'cardholders' => $cardholders,
            'currencies' => $currencies,
            'categories' => $categories,
            'virtualcards' => $virtualcards,
            'virtualcardCurrencies' => $this->getDistinctValues('currency_code'),
            'virtualcardStatuses' => $this->getDistinctValues('status'),
            'virtualcardBrands' => $this->getDistinctValues('card_brand'),
            'filter' => request()->input('filter')

        ]);
    }

    public function create()
    {
        $cardholders = VirtualcardHolder::where(['user_id' => auth()->id(), 'status' => 'Approved'])->get();
        $currencyCodes = VirtualcardFeeslimit::whereStatus('Active')->pluck('virtualcard_currency_code')->toArray();
        $currencies = Currency::whereIn('code', $currencyCodes)->get(['id', 'code']);
        $categories = VirtualcardCategory::whereStatus('active')->get(['id', 'name']);

        return view('virtualcard::user.virtualcards.create', [

            'cardholders' => $cardholders,
            'currencies' => $currencies,
            'categories' => $categories

        ]);
    }

    public function store(StoreVirtualcardRequest $request)
    {
        $virtualcard = Virtualcard::whereHas('virtualcardHolder', function($query) {
                            $query->where('user_id', auth()->id());
                        })
                        ->where('status', 'Approved')
                        ->get();
        if ($virtualcard->count() >= preference('card_limit')) {
            return redirect()->back()->with('error', __('You have reached the maximum limit of virtual cards you can create'));
        }

        $virtualcard = new Virtualcard();
        $virtualcard->virtualcard_holder_id = $request->virtualcardHolderId;
        $virtualcard->virtualcard_category_id = $request->preferredCategory;
        $virtualcard->card_brand = $request->cardBrand;
        $virtualcard->currency_code = $request->preferredCurrency;
        $virtualcard->status = 'Pending';
        $virtualcard->save();

        $cardHolder = VirtualcardHolder::find($request->virtualcardHolderId, ['id', 'virtualcard_provider_id']);

        if (!empty($cardHolder)) {
            $creationFee = VirtualcardFeeslimit::where([
                            'virtualcard_currency_code' => $request->preferredCurrency,
                            'virtualcard_provider_id' => $cardHolder->virtualcard_provider_id,
                            'status' => 'Active',
                        ])->max('card_creation_fee');

            $wallet = Wallet::where(['user_id' => auth()->id(), 'currency_id' => currencyDetails($virtualcard->currency_code)->id])->first(['id', 'balance']);
            if ($wallet) {
                $wallet->decrement('balance', $creationFee);
            }
        }

        event(new VirtualcardRequest($virtualcard));

        return redirect()->route('user.virtualcard.index')->with('success', __('Virtual card application submitted successfully. Please wait for approval. You can view pending card requests under the \'Pending\' filter option.'));
    }

    public function show(Virtualcard $virtualcard)
    {
        if ($virtualcard->virtualcardHolder?->user_id != auth()->id() || $virtualcard->status == 'Pending') {
            return redirect()->route('user.virtualcard.index')->with('error', __('You are not authorized to view this page'));
        }

        $topupTransactions = \App\Models\Transaction::where('transaction_type_id', Virtualcard_Topup)
                        ->whereHas('virtualcardTopup', function ($q) use ($virtualcard) {
                            $q->where('virtualcard_id', $virtualcard->id)
                            ->where('user_id', auth()->id());
                        })
                        ->with('virtualcardTopup')
                        ->latest()
                        ->get();

        $withdrawalTransactions = \App\Models\Transaction::where('transaction_type_id', Virtualcard_Withdrawal)
                        ->whereHas('virtualcardWithdrawal', function ($q) use ($virtualcard) {
                            $q->where('virtualcard_id', $virtualcard->id)
                            ->where('user_id', auth()->id());
                        })
                        ->with('virtualcardWithdrawal')
                        ->latest()
                        ->get();

        // Merge and sort by created_at desc
        $transactions = $topupTransactions->merge($withdrawalTransactions)
                        ->sortByDesc('created_at')
                        ->take(10)
                        ->values();

        $automatedTransactions = WebhookTransaction::with('virtualcard')->where('virtualcard_id', $virtualcard->id)
                                ->where('user_id', auth()->id())
                                ->orderBy('id', 'desc')
                                ->take(10)
                                ->get();

        $totalSpent = VirtualcardSpending::where('virtualcard_id', $virtualcard->id)->sum('amount');


        return view('virtualcard::user.virtualcards.show', [

            'virtualcard' => $virtualcard->load('spendingControls'),
            'transactions' => $transactions,
            'automatedTransactions' => $automatedTransactions,
            'totalSpent' => $totalSpent

        ]);
    }

    protected function getDistinctValues(string $column)
    {
        return Virtualcard::select($column)->distinct()->get();
    }

    public function sendOtp(Request $request)
    {
        $otp = rand(100000, 999999);
        $virtualcardId = $request->virtualcardId;

        if (checkDemoEnvironment()) {
            $otp = "000000";
            session(['otp' => $otp, 'otpSentTime' => now(), 'virtualcardId' => $virtualcardId]);
            return response()->json(['success' => true, 'message' => 'OTP sent']);
        }

        // Store OTP temporarily (in a database, cache, or session)
        session(['otp' => $otp, 'otpSentTime' => now(), 'virtualcardId' => $virtualcardId]);

        $message = __("Your OTP is: :x. This OTP is valid for the next 1 minutes.", ['x' => $otp]);

        // Send OTP via SMS
        if (auth()->user()->formattedPhone) {
            sendSMS(auth()->user()->formattedPhone, $message);
        }

        // Send OTP via Email
        (new EmailController)->sendEmail(auth()->user()->email, __('Show Card OTP'), $message);

        return response()->json(['success' => true, 'message' => 'OTP sent']);
    }

    public function verifyOtp(Request $request)
    {
        try {
            $virtualcard = Virtualcard::with('virtualcardProvider')->where('id', session('virtualcardId'))->first();

            // Check if OTP matches and it's within the 1-minute expiry time
            if (request()->otp == session('otp') && now()->diffInMinutes(session('otpSentTime')) <= 1 && $virtualcard) {

                $virtualcardId = session('virtualcardId');

                session()->forget(['otp', 'otpSentTime', 'virtualcardId']);

                if ($virtualcard->virtualcardProvider?->type == 'Automated') {
                    if (checkDemoEnvironment()) {
                        return response()->json([
                            'success' => true, 
                            'cardNumber' => "4000009990000039", 
                            'virtualcardId' => 4, 
                            'cvc' => 123
                        ]);
                    }
                    
                    $providerHandler = \Modules\Virtualcard\Facades\VirtualcardProviderManager::get($virtualcard->virtualcardProvider?->name);
                    $cardDetailsService = $providerHandler->getCardService();
                    $cardResponse = $cardDetailsService->getVirtualcard($virtualcard->id);

                    return response()->json(['success' => true, 'cardNumber' => $cardResponse->cardNumber, 'virtualcardId' => $virtualcardId, 'cvc' => $cardResponse->cardCvc]);
                } else {
                    $providerHandler = \Modules\Virtualcard\Facades\VirtualcardProviderManager::get('manualvirtualcard');
                    $cardDetailsService = $providerHandler->getCardService();
                    $cardResponse = $cardDetailsService->getVirtualcard($virtualcard->id);

                    return response()->json(['success' => true, 'cardNumber' => $cardResponse->cardNumber, 'virtualcardId' => $virtualcardId, 'cvc' => $cardResponse->cardCvc]);
                }
            }

            return response()->json(['success' => false], 400);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function creationFee(Request $request)
    {
        $cardHolder = VirtualcardHolder::find($request->cardHolderId, ['id', 'virtualcard_provider_id']);

        if (empty($cardHolder)) {
            return response()->json(['success' => false, 'message' => __('Invalid cardholder ID.')]);
        }

        $creationFee = VirtualcardFeeslimit::where([
                        'virtualcard_currency_code' => $request->preferredCurrency,
                        'virtualcard_provider_id' => $cardHolder->virtualcard_provider_id,
                        'status' => 'Active',
                    ])->max('card_creation_fee');

        if (!$creationFee) {
            return response()->json(['success' => false]);
        }

        $currency = Currency::where('code', $request->preferredCurrency)->first();

        if (empty($currency)) {
            return response()->json(['success' => false, 'message' => __('Invalid currency.')]);
        }

        $wallet = Wallet::where(['user_id' => auth()->id(), 'currency_id' => $currency->id])->first();

        if (empty($wallet)) {
            return response()->json(['success' => false, 'message' => __('You do not have a wallet for this currency')]);
        }

        if ($wallet->balance < $creationFee) {
            return response()->json(['success' => false, 'message' => __('Plese keep at least :x in your wallet to create a virtual card', ['x' => moneyFormat($currency->symbol, formatNumber($creationFee, $currency->id))])]);
        }

        if ($creationFee && $currency) {
            return response()->json(['success' => true, 'message' => __('A processing fee of :x applies to card generation; it may vary.', ['x' => moneyFormat($currency->symbol, formatNumber($creationFee, $currency->id))])]);
        }
    }
}
