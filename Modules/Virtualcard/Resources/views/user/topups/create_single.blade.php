@extends('user.layouts.app')
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/user/virtual-topup.min.css') }}">
@endpush
@section('content')
<div class="bg-white pxy-62 shadow" id="topupCreateSingle">
    <p class="mb-0 f-26 gilroy-Semibold text-uppercase text-center">{{ __('Topup') }}</p>
    <p class="mb-0 text-center f-13 gilroy-medium text-gray mt-4 dark-A0">{{ __('Step: 1 of 3') }}</p>
    <p class="mb-0 text-center f-18 gilroy-medium text-dark dark-5B mt-2">{{ __('Create Topup') }}</p>
    <div class="text-center">{!! svgIcons('stepper_create') !!}</div>

    <p class="mb-0 text-center f-14 gilroy-medium text-gray dark-p mt-20">{{ __('You can topup to your card using your wallets. Fill the details correctly & the amount you want to topup.') }}</p>

    @include('user.common.alert')

    <form method="post" action="{{ route('user.topup.confirm') }}" id="topupCreateSingleForm">
        @csrf
        <input type="hidden" name="percentage_fee" id="percentage_fee" value="">
        <input type="hidden" name="fixed_fee" id="fixed_fee" value="">
        <input type="hidden" name="total_fee" id="total_fee" value="">

        <!-- card -->
        <div class="mt-28 param-ref">
            <label class="gilroy-medium text-gray-100 mb-2 f-15" for="card">{{ __('Card') }}</label>
            <div class="avoid-blink">
                <div class="d-flex justify-content-start align-items-center gap-2 form-control input-form-control apply-bg not-focus-bg w-100 virtual-input-container">
                    <span>{!! $singleVirtualCard['cardBrand'] == 'Visa Card' ? virtualcardSvgIcons('visa_card_icon') : virtualcardSvgIcons('master_card_icon') !!}</span>

                    <div class="w-100 virtualcard-input">
                        <input type="text" class="w-100 border-0 virtualCardDisplay" name="virtualCardDisplay" id="virtualCardDisplay" placeholder="{{ __('Card') }}"  value="{{ $singleVirtualCard['cardName'] }}" required data-value-missing="{{ __('This field is required.') }}" readonly>
                        
                        <input type="hidden" name="virtualCardId" id="virtualCard" value="{{ $singleVirtualCard['cardId'] }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Empty -->
        <div class="row">
            <div class="col-12">
                <div class="mt-20 param-ref d-none" id="paymentMethodEmpty">
                    <label class="gilroy-medium text-warning mb-2 f-15">{{ __('Fees Limit or Payment Method are currently inactive.') }}</label>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="row">
            <div class="col-12">
                <div class="mt-20 param-ref" id="paymentMethodSection">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15" for="topup_wallet">{{ __('Wallet') }}</label>
                    <div class="avoid-blink">
                        <select class="select2" data-minimum-results-for-search="Infinity" name="display_topup_wallet" id="display_topup_wallet">
                            @if(isset($singleVirtualCard))
                            <option value="{{ $singleVirtualCard['wallet'] }}">{{ $singleVirtualCard['wallet'] . ' ' . __('Wallet') }}</option>
                            @endif
                        </select>
                        <input type="hidden" name="topupWallet" id="topup_wallet" value="{{ isset($singleVirtualCard) ? $singleVirtualCard['wallet'] : '' }}">
                    </div>
                </div>
                <p class="mb-0 text-gray-100 dark-B87 gilroy-regular f-12 mt-2">{{ __('Fee') }} (<span class="pFees">0%</span>+<span class="fFees"> 0</span>) {{ __('Total Fee') }}: <span class="total_fees">0.00</span></p>
            </div>
        </div>

        <!-- Amount -->
        <div class="mt-20 label-top">
            <label class="gilroy-medium text-gray-100 mb-2 f-15" for="amount">{{ __('Amount') }}</label>
            <input
                type="text"
                class="form-control input-form-control apply-bg l-s2"
                name="amount"
                id="amount"
                placeholder="{{ __('Enter amount') }}"
                value="{{ session('paymentData')['amount'] ?? old('amount') }}"
                onkeypress="return isNumberOrDecimalPointKey(this, event);"
                oninput="restrictNumberToPrefdecimalOnInput(this)"
                required data-value-missing="{{ __('This field is required.') }}"
            >
            <span class="amountLimit custom-error"></span>
        </div>

        <!-- Submit -->
        <div class="d-grid">
            <button type="submit" class="btn btn-lg btn-primary mt-4" id="topupCreateSubmitBtn">
                <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none">
                    <span class="visually-hidden"></span>
                </div>
                <span class="px-1" id="topupCreateSubmitBtnText">{{ __('Next') }}</span>

            </button>
        </div>
    </form>
</div>
@endsection

@push('js')
    @include('common.restrict_number_to_pref_decimal')
    @include('common.restrict_character_decimal_point')

    <script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/debounce/jquery.ba-throttle-debounce.min.js') }}"></script>


    <script type="text/javascript">
        'use strict';
        var token = $('[name="_token"]').val();
        var cardWalletUrl = "{{ route('user.topup.wallets') }}";
        var feesLimitUrl = "{{ route('user.topup.fees_limit') }}";
        var wallet = "{{ __('Wallet') }}";
        var selectedWallet = "{{ session('paymentData')['wallet'] ?? '' }}";
        var submitBtnText = "{{ __('Processing...') }}";
    </script>

<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/topup.min.js') }}"></script>
@endpush
