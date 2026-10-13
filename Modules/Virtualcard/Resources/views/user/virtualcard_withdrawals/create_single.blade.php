@extends('user.layouts.app')
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/user/virtual-withdrawal.min.css') }}">
@endpush
@section('content')
    @include('user.common.alert')

    <div class="bg-white pxy-62" id="WithdrawalCreateSingle">
        <p class="mb-0 f-26 gilroy-Semibold text-uppercase text-center">{{ __('Withdrawal To Wallet') }}</p>

        <p class="mb-0 text-center f-13 gilroy-medium text-gray mt-4 dark-A0">{{ __('Step: 1 of 3') }}</p>
        <p class="mb-0 text-center f-18 gilroy-medium text-dark dark-5B mt-2">{{ __('Fill Information') }}</p>
        <div class="text-center">
            {!! svgIcons('stepper_create') !!}
        </div>
        <p class="mb-0 text-center f-14 gilroy-medium text-gray dark-p mt-20"> {{ __('You can withdraw funds from any card associated with your wallet.') }}</p>

        <form method="POST" action="{{ route('user.virtualcard_withdrawal.confirm') }}" id="withdrawalCreateSingleForm">
            @csrf
            <input type="hidden" name="percentage_fee" id="percentage_fee" value="">
            <input type="hidden" name="fixed_fee" id="fixed_fee" value="">
            <input type="hidden" name="total_fee" id="total_fee" value="">
            <input type="hidden" value="{{ auth()->id() }}" name="userId" id="userId">

            <div class="row">
                <div class="col-md-12">
                    <div class="label-top mt-20">
                        <label class="gilroy-medium text-gray-100 mb-2 f-15" for="virtualcardId">{{ __('Card') }}</label>
                        <div class="d-flex justify-content-start align-items-center gap-2 form-control input-form-control apply-bg not-focus-bg w-100 virtual-input-container">
                            <span>{!! $virtualcard->card_brand == 'Visa Card' ? virtualcardSvgIcons('visa_card_icon') : virtualcardSvgIcons('master_card_icon') !!}</span>
                            <div class="w-100 virtualcard-input d-flex justify-content-start align-items-center">
                                 <span>{{ maskCardNumberForLogo($virtualcard->card_number) }}</span> 
                                 <p id="" class="virtualCardDisplay mb-0 ms-1">({{ cardTitle($virtualcard->virtualcardHolder) }})</p>
                               <input type="hidden" value="{{ $virtualcard->id }}" name="virtualcardId" id="virtualcardId" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Withdrawal Wallet -->
            <div class="row">
                <div class="col-12">
                    <div class="mt-20 param-ref">
                        <label class="gilroy-medium text-gray-100 mb-2 f-15" for="withdrawalWallet">{{ __('Withdrawal Wallet') }}</label>
                        <div class="avoid-blink">
                            <select class="select2 sl_common_bx" data-minimum-results-for-search="Infinity" name="withdrawalWallet" id="withdrawalWallet">

                            </select>
                        </div>
                    </div>
                </div>
                <p class="mb-0 text-gray-100 dark-B87 gilroy-regular f-12 mt-2">{{ __('Fee') }} (<span class="pFees">0%</span>+<span class="fFees"> 0</span>) {{ __('Total Fee') }}: <span class="total_fees">0.00</span></p>
            </div>

            <!-- Amount -->
            <div class="row">
                <div class="col-md-12">
                    <div class="label-top mt-20">
                        <label class="gilroy-medium text-gray-100 mb-2 f-15" for="requestedFund">{{ __('Amount') }}</label>
                        <input
                            type="text"
                            class="form-control input-form-control apply-bg l-s2 requestedFund"
                            value="{{ session('paymentData')['amount'] ?? old('requestedFund') }}"
                            name="requestedFund"
                            placeholder="{{ __('Give an amount') }}"
                            id="requestedFund"
                            required data-value-missing="{{ __('This field is required.') }}"
                            onkeypress="return isNumberOrDecimalPointKey(this, event);"
                            oninput="restrictNumberToPrefdecimalOnInput(this)"
                        >
                    </div>
                    <span class="amountLimit custom-error"></span>
                </div>
            </div>


            <div class="row d-none" id="empty-payment">
                <div class="col-12">
                    <div class="mt-20 param-ref">
                        <span class="text-danger">{{ __('Wallet or payment method is not available.') }}</span>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-lg btn-primary mt-4" id="withdrawalButton">
                    <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                    <span id="withdrawalButtonText">{{ __('Next') }}</span>
                    <span id="rightAngleSvgIcon">{!! svgIcons('right_angle') !!}</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('js')

    @include('common.restrict_number_to_pref_decimal')
    @include('common.restrict_character_decimal_point')

    <script src="{{ asset('public/dist/plugins/debounce/jquery.ba-throttle-debounce.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/libraries/sweetalert2/sweetalert2.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/libraries/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        'use strict';
        let token = '{{ csrf_token() }}';
        let minAmount = "{{ __('Minimum amount') }}";
        let maxAmount = "{{ __('Maximum amount') }}";
        let preText = "{{ __('Next') }}";
        let failedText = "{{ __('Failed') }}";
        let withdrawalButtonText = "{{ __('Processing') }}";
        var submitBtnText = "{{ __('Processing...') }}";
        let userId = '{{ auth()->id() }}';
        var withdrawalWalletId = "{{ session('paymentData')['walletId'] ?? '' }}";
        let getWithdrawalWalletUrl = '{{ route("user.virtualcard_withdrawal.wallet") }}';
        let checkAmountLimitUrl = '{{ route("user.virtualcard_withdrawal.validate_amount_limit") }}';
        let retrieveFeesLimitUrl = '{{ route("user.virtualcard_withdrawal.retrieve_fees_limit") }}';
    </script>
    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/user/virtualcard-withdrawal.min.js') }}" type="text/javascript"></script>
@endpush
