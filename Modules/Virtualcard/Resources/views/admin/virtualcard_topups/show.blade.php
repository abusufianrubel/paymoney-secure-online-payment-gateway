@extends('admin.layouts.master')
@section('title', __('Topup Details'))

@section('page_content')
<div class="box box-default">
	<div class="box-body">
		<div class="d-flex justify-content-between">
			<div>
				<div class="top-bar-title padding-bottom pull-left">{{ __('Virtualcard Topup Details') }}</div>
			</div>
            <div>
				<p class="text-left mb-0 f-18">{{ __('Status') }} : {!! getStatusText($cardTopup->fund_approval_status) !!}</p>
			</div>
		</div>
	</div>
</div>
@php
    $fees  = $cardTopup->percentage_fees + $cardTopup->fixed_fees;
    $total = $fees + $cardTopup->requested_fund;
@endphp
<div class="my-30">
    <form action="{{ route('admin.virtualcard_topup.statusChange', $cardTopup) }}" class="form-horizontal row" id="TopupStatusUpdateForm" method="POST">
        @csrf
        <input type="hidden" class="form-control" name="topupStatusUpdate" value="Yes" id="topupStatusUpdate">
        <!-- Page title start -->
        <div class="col-md-8">
            <div class="box">
                <div class="box-body">
                    <div class="panel">
                        <div>
                            <div class="p-4">

                                <!-- User -->
                                <div class="form-group row">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('User') }}</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static f-14">{{ getColumnValue($cardTopup->user) }}</p>
                                    </div>
                                </div>

                                <!-- TrxID -->
                                <div class="form-group row">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Transaction ID') }}</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static f-14">{{ optional($cardTopup->transaction)->uuid }}</p>
                                    </div>
                                </div>

                                <!--Card Brand -->
                                <div class="form-group row">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Card Brand') }}</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static f-14">{{ optional($cardTopup->virtualcard)->card_brand }}</p>
                                    </div>
                                </div>

                                <!-- Card Number -->
                                <div class="form-group row">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Card Number') }}</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static f-14">

                                            {!! $cardTopup->virtualcard?->card_number ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $cardTopup->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($cardTopup->virtualcard?->card_number) : '-' !!}
                                        
                                        </p>
                                    </div>
                                </div>

                                <!-- Currency Code -->
                                <div class="form-group row">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Currency') }}</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static f-14">{{ optional($cardTopup->virtualcard)->currency_code }}</p>
                                    </div>
                                </div>

                                <!-- Created At -->
                                <div class="form-group row">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Date') }}</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static f-14">{{ dateFormat($cardTopup->created_at) }}</p>
                                    </div>
                                </div>

                                <!-- Status -->
                                @if ($cardTopup->fund_approval_status != 'Approved')
                                @php
                                    $approvalStatus = old('fund_approval_status') ? old('fund_approval_status') : $cardTopup->fund_approval_status;
                                @endphp
                                <div class="form-group row align-items-center">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3" for="fund_approval_status">{{ __('Status') }}</label>
                                    <div class="col-sm-6">
                                        <select class="form-control select2 w-60" name="fund_approval_status" id="fund_approval_status">
                                            <option value="Approved" {{ $approvalStatus ==  'Approved'? 'selected':"" }}>{{ __('Approve') }}</option>
                                            <option value="Pending"  {{ $approvalStatus == 'Pending' ? 'selected':"" }}>{{ __('Pending') }}</option>
                                            <option value="Cancelled"  {{ $approvalStatus == 'Cancelled' ? 'selected':"" }}>{{ __('Cancel') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Cancellation Reason -->
                                <div class="form-group row" id="cancellation_reason_div">
                                    <label class="col-sm-3 control-label require mt-11 text-sm-end f-14 fw-bold" for="name">{{ __('Cancellation Reason') }}</label>
                                    <div class="col-sm-6">
                                        <textarea name="cancellation_reason" class="form-control f-14" id="cancellation_reason" placeholder="{{ __('Enter :x', ['x' => __('Cancellation Reason')]) }}" data-value-missing="{{ __('This field is required.') }}" >{{ old('cancellation_reason') ? old('cancellation_reason') : $cardTopup->cancellation_reason }}</textarea>
                                        @if ($errors->has('cancellation_reason'))
                                            <span class="error">
                                                <strong class="text-danger">{{ $errors->first('cancellation_reason') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <a id="cancel_anchor" class="btn btn-theme-danger me-1 f-14" href="{{ route('admin.virtualcard_topup.index') }}">{{ __('Cancel') }}</a>
                                        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_card_topup'))
                                        <button type="submit" class="btn btn-theme f-14" id="TopupStatusUpdateBtn">
                                            <i class="fa fa-spinner fa-spin d-none"></i> <span id="TopupStatusUpdateBtnText">{{ __('Update') }}</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <div class="panel">
                        <div>
                            <div class="pt-4">
                                <!-- Amount -->
                                <div class="form-group row">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-6">{{ __('Amount') }}</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static f-14">{{ moneyFormat(currencyDetails(optional($cardTopup->virtualcard)->currency_code)->symbol, formatNumber($cardTopup->requested_fund, currencyDetails(optional($cardTopup->virtualcard)->currency_code)->id)) }}</p>
                                    </div>
                                </div>
                                <div class="form-group row total-deposit-feesTotal-space">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-6 d-flex justify-content-end" for="feesTotal">{{ __('Fees') }}
                                        <span>
                                            <small class="transactions-edit-fee">
                                                @if (isset($cardTopup->transaction))
                                                ({{(formatNumber(optional($cardTopup->transaction)->percentage, currencyDetails(optional($cardTopup->virtualcard)->currency_code)->id))}}% + {{ formatNumber(optional($cardTopup->transaction)->charge_fixed, currencyDetails(optional($cardTopup->virtualcard)->currency_code)->id) }})
                                                @else
                                                    ({{0}}%+{{0}})
                                                @endif
                                            </small>
                                        </span>
                                    </label>
                                    <div class="col-sm-6">
                                    <p class="form-control-static f-14">{{ moneyFormat(currencyDetails(optional($cardTopup->virtualcard)->currency_code)->symbol, formatNumber($fees, currencyDetails(optional($cardTopup->virtualcard)->currency_code)->id)) }}</p>
                                    </div>
                                </div>
                                <hr class="increase-hr-height">
                                <!-- Total -->
                                <div class="form-group row total-deposit-space">
                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-6" for="total">{{ __('Total') }}</label>
                                    <div class="col-sm-6">
                                    <p class="form-control-static f-14">{{ moneyFormat(currencyDetails(optional($cardTopup->virtualcard)->currency_code)->symbol, formatNumber($total, currencyDetails(optional($cardTopup->virtualcard)->currency_code)->id)) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('extra_body_scripts')

    @include('common.restrict_number_to_pref_decimal')
    @include('common.restrict_character_decimal_point')
    <script>
        "use strict";
        var submitButtonText = "{{ __('Updating...') }}";
    </script>

    <script type="text/javascript" src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/topup-status-update.min.js') }}"></script>
@endpush
