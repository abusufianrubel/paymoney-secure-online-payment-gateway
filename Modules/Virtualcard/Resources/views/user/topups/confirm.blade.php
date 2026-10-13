@extends('user.layouts.app')

@section('content')
<div class="bg-white pxy-62 shadow" id="topupConfirm">
    <p class="mb-0 f-26 gilroy-Semibold text-uppercase text-center">{{ __('Topup Money') }}</p>
    <p class="mb-0 text-center f-13 gilroy-medium text-gray mt-4 dark-A0">{{ __('Step: 2 of 3') }}</p>
    <p class="mb-0 text-center f-18 gilroy-medium text-dark dark-5B mt-2">{{ __('Confirm Your Topup') }}</p>
    <div class="text-center">{!! svgIcons('stepper_confirm') !!}</div>
    <p class="mb-0 text-center f-14 gilroy-medium text-gray dark-p mt-20">{{ __('Please review all of the information below before confirming the topup.') }}</p>

    <form method="post" action="{{ route('user.topup.success') }}" id="topupConfirmForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="topup_wallet" id="topup_wallet" value="{{ $currencyCode }}">
        <input type="hidden" name="amount" id="amount" value="{{ $totalAmount }}">
        <input type="hidden" name="virtualCard" id="virtualCard" value="{{ $virtualCardId }}">

        <!-- Confirm Details -->
        <div class="mt-32 param-ref text-center">
            <p class="mb-18 text-dark f-18 leading-22 gilroy-Semibold text-start mt-32">{{ __('Card Details') }}</p>
				<div class="transaction-box">
					<div class="pb-11 border-b-EF d-flex justify-content-between">
						<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ __('Card Holder') }}</p>
						<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ $cardTitle }}</p>
					</div>
					<div class="pb-11 border-b-EF d-flex justify-content-between mt-14">
						<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ __('Number') }}</p>
						<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{!! virtualcardSvgIcons(strtolower(str_replace(' ', '_', $cardBrand)) . '_icon') !!} {{ maskCardNumberForLogo($cardNumber) }}</p>
					</div>

				</div>
				<p class="mb-18 text-dark f-18 leading-22 gilroy-Semibold text-start inv-mt mt-32">{{ __('Amount Details') }}</p>
				<div class="pb-13 d-flex justify-content-between transaction-box">
					<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ __('Topup Amount') }}</p>
					<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{  moneyFormat($currencyCode, formatNumber($amount, $currencyId)) }}</p>
				</div>
				<div class="pb-13 border-b-EF d-flex justify-content-between transaction-box">
					<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ __('Fees') }}</p>
					<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ moneyFormat($currencyCode, formatNumber($totalFees, $currencyId)) }}</p>
				</div>
				<div class="d-flex justify-content-between inv-mt mt-16">
					<p class="mb-0 f-16 leading-20 text-dark gilroy-medium">{{ __('Total Topup Amount') }}:</p>
					<p class="mb-0 f-16 leading-20 text-dark gilroy-medium">{{ moneyFormat($currencyCode, formatNumber($totalAmount, $currencyId)) }}</p>
				</div>
        </div>

        <!-- Confirm Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-lg btn-primary mt-4" id="topupConfirmBtn">
                <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none">
                    <span class="visually-hidden"></span>
                </div>
                <span class="px-1" id="topupConfirmBtnText">{{ __('Confirm Topup') }}</span>
                <span id="rightAngleSvgIcon">{!! svgIcons('right_angle') !!}</span>
            </button>
        </div>

        <!-- Back Button -->
        <div class="d-flex justify-content-center align-items-center mt-4 back-direction">
            <a href="{{ route('user.topup.create') }}" class="text-gray gilroy-medium d-inline-flex align-items-center position-relative back-btn" id="topupConfirmBackBtn">
                {!! svgIcons('left_angle') !!}
                <span class="ms-1 back-btn ns topupConfirmBackBtnText">{{ __('Back') }}</span>
            </a>
        </div>
    </form>
</div>
@endsection

@push('js')
    <script type="text/javascript">
        'use strict';
        let confirmBtnText = "{{ __('Confirming...') }}";
        let pretext = "{{ __('Confirm Topup') }}";
    </script>
    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/topup.min.js') }}"></script>
@endpush
