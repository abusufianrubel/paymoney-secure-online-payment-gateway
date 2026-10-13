@extends('user.layouts.app')

@section('content')
    <div class="bg-white pxy-62" id="WithdrawalConfirm">
        <p class="mb-0 f-26 gilroy-Semibold text-uppercase text-center">{{ __('Withdrawal To Wallet') }}</p>
        <p class="mb-0 text-center f-13 gilroy-medium text-gray mt-4 dark-A0">{{ __('Step: 2 of 3') }}</p>
        <p class="mb-0 text-center f-18 gilroy-medium text-dark dark-5B mt-2">{{ __('Confirm Withdrawal') }}</p>
		<div class="text-center">
			<!-- Replace with actual SVG icon -->
			{!! svgIcons('stepper_confirm') !!}
		</div>
        <p class="mb-0 text-center f-14 gilroy-medium text-gray dark-p mt-20">{{ __('Please review all of the information below before confirming the withdrawal.') }}</p>
		<!-- Include alert component -->
		@include('user.common.alert')
		<form action="{{ route('user.virtualcard_withdrawal.success') }}" method="POST" accept-charset="UTF-8" id="withdrawalConfirmForm">
			@csrf

			<div class="plan-details">
				<p class="mb-18 text-dark f-18 leading-22 gilroy-Semibold text-start mt-32">{{ __('Card Details') }}</p>
				<div class="transaction-box">
					<div class="pb-11 border-b-EF d-flex justify-content-between">
						<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ __('Card Holder') }}</p>
						<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ cardTitle($virtualcardWithdrawalData->virtualcard?->virtualcardHolder) }}</p>
					</div>
					<div class="pb-11 border-b-EF d-flex justify-content-between mt-14">
						<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ __('Number') }}</p>
						<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{!! virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcardWithdrawalData->virtualcard?->card_brand)) . '_icon') !!} {{ maskCardNumberForLogo($virtualcardWithdrawalData->virtualcard?->card_number) }}</p>
					</div>

				</div>
				<p class="mb-18 text-dark f-18 leading-22 gilroy-Semibold text-start inv-mt mt-32">{{ __('Amount Details') }}</p>
				<div class="pb-13 d-flex justify-content-between transaction-box">
					<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ __('Withdrawal Amount') }}</p>
					<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ moneyFormat($virtualcardWithdrawalData->virtualcard?->currency()?->code, formatNumber($virtualcardWithdrawalData->requestedFund, $virtualcardWithdrawalData->virtualcard?->currency()?->id)) }}</p>
				</div>
				<div class="pb-13 border-b-EF d-flex justify-content-between transaction-box">
					<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ __('Fees') }}</p>
					<p class="mb-0 f-16 leading-19 gilroy-regular text-gray-100">{{ moneyFormat($virtualcardWithdrawalData->virtualcard?->currency()?->code, formatNumber($virtualcardWithdrawalFee->totalFee, $virtualcardWithdrawalData->virtualcard?->currency()?->id)) }}</p>
				</div>
				<div class="d-flex justify-content-between inv-mt mt-16">
					<p class="mb-0 f-16 leading-20 text-dark gilroy-medium">{{ __('Total Withdrawal Amount') }}:</p>
					<p class="mb-0 f-16 leading-20 text-dark gilroy-medium">{{ moneyFormat($virtualcardWithdrawalData->virtualcard?->currency()?->code, formatNumber($virtualcardWithdrawalData->requestedFund + $virtualcardWithdrawalFee->totalFee, $virtualcardWithdrawalData->virtualcard?->currency()?->id)) }}</p>
				</div>
				<div class="d-grid">
					<button type="submit" id="withdrawalConfirmBtn" class="btn btn-lg btn-primary mt-4">
						<div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                            <span class="visually-hidden"></span>
                        </div>
						<span id="withdrawalConfirmBtnText">{{ __('Confirm Withdrawal') }}</span>
						<!-- Replace with actual SVG icon -->
						<span id="rightAngleSvgIcon">{!! svgIcons('right_angle') !!}</span>
					</button>
				</div>
				<div class="d-flex justify-content-center align-items-center inv-back-mt mt-4 back-direction">
					<a href="{{ route('user.virtualcard_withdrawal.create') }}" class="text-gray gilroy-medium d-inline-flex align-items-center position-relative back-btn deposit-confirm-back-btn" id="withdrawalConfirmBackBtn">
						<!-- Replace with actual SVG icon -->
						{!! svgIcons('left_angle') !!}
					<span class="ms-1 back-btn withdrawalConfirmBackBtnText">{{ __('Back') }}</span>
					</a>
				</div>
			</div>
		</form>
	</div>

@endsection

@push('js')
	<script type="text/javascript">
		'use strict';
		var submitBtnText = "{{ __('Processing...') }}";
	</script>
	<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/user/virtualcard-withdrawal.min.js') }}" type="text/javascript"></script>
@endpush
