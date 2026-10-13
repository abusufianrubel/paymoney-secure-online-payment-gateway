@extends('admin.layouts.master')
@section('title', __('Withdrawal Details'))

@section('page_content')
	<div class="box box-default" id="withdrawalEditBox">
		<div class="box-body">
			<div class="d-flex justify-content-between">
				<div>
					<div class="top-bar-title padding-bottom pull-left">{{ __('Virtualcard Withdrawal Details') }}</div>
				</div>

				<div>
                    @if ($virtualcardWithdrawal->fund_approval_status)
						<p class="text-left mb-0 f-18">{{ __('Status') }} : {!! getStatusText($virtualcardWithdrawal->fund_approval_status) !!}</p>
					@endif
				</div>
			</div>
		</div>
	</div>

    <section class="min-vh-100">
        <div class="my-30">
            <div class="row">
                <!-- Page title start -->
                <div class="col-md-8">
                    <div class="box">
                        <div class="box-body">
                            <div class="panel">
                                <div>
                                    <div class="p-4">

                                        <div class="panel panel-default">
                                            <div class="panel-body">

                                                <!-- Card owner -->
                                                <x-virtualcard::card-info lable="{{ __('User') }}" value='<a href="{{ url(config("adminPrefix") . "/users/edit/" . $virtualcardWithdrawal->user?->id) }}">{{ getColumnValue($virtualcardWithdrawal->user) }}</a>' />


                                                <!-- Card Brand -->
                                                <x-virtualcard::card-info lable="{{ __('Brand') }}" value="{{ $virtualcardWithdrawal->virtualcard?->card_brand }}" />

                                                <!-- Card Number -->
                                                <div class="form-group row">
                                                    <label class="control-label f-14 fw-bold text-sm-end col-sm-4" for="user">{{ __('Card Number') }}</label>
                                                    <div class="col-sm-8">
                                                        <p class="form-control-static f-14">
                                                            {!! $virtualcardWithdrawal->virtualcard?->card_number ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcardWithdrawal->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($virtualcardWithdrawal->virtualcard?->card_number) : '-' !!}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Transaction ID -->
                                                <x-virtualcard::card-info lable="{{ __('Transaction ID') }}" value="{{ $virtualcardWithdrawal->transaction?->uuid }}" />

                                                <!-- Currency Code -->
                                                <x-virtualcard::card-info lable="{{ __('Currency') }}" value="{{ $virtualcardWithdrawal->virtualcard?->currency_code }}" />

                                                <!-- Amount -->
                                                <x-virtualcard::card-info lable="{{ __('Requested Time') }}" value="{{ dateFormat($virtualcardWithdrawal->fund_request_time) }}" />

                                                <!-- Spending Limit -->
                                                <x-virtualcard::card-info lable="{{ __('Released Time') }}" value="{{ $virtualcardWithdrawal->fund_release_time ? dateFormat($virtualcardWithdrawal->fund_release_time) : '-' }}" />

                                                @if ($virtualcardWithdrawal->fund_approval_status != 'Approved')
                                                    <form action="{{ route('admin.virtualcard_withdrawal.update', $virtualcardWithdrawal) }}" method="POST" id="withdrawalStatusUpdateForm">
                                                        @csrf
                                                        @method('PATCH')

                                                        <!-- Status -->
                                                        <div class="form-group row align-items-center">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-4" for="status">{{ __('Status') }}</label>
                                                            <div class="col-sm-6">
                                                                <select class="form-control select2 w-60" name="fund_approval_status" id="status">
                                                                    <option value="Approved" {{ $virtualcardWithdrawal->fund_approval_status == 'Approved' ? 'selected' : '' }}>Approve</option>
                                                                    <option value="Pending" {{ $virtualcardWithdrawal->fund_approval_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                                    <option value="Cancelled" {{ $virtualcardWithdrawal->fund_approval_status == 'Cancelled' ? 'selected' : '' }}>Cancel</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Cancellation Reason -->
                                                        <div class="form-group row align-items-center" id="cancellation_reason_div">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-4" for="cancellation_reason">{{ __('Cancellation Reason') }}</label>
                                                            <div class="col-sm-6">
                                                                <textarea name="cancellation_reason" class="form-control f-14" id="cancellation_reason" placeholder="{{ __('Enter :x', ['x' => __('Cancellation Reason')]) }}" data-value-missing="{{ __('This field is required.') }}" >{{ $virtualcardWithdrawal->cancellation_reason }}</textarea>
                                                                @if ($errors->has('cancellation_reason'))
                                                                    <span class="error">
                                                                        <strong class="text-danger">{{ $errors->first('cancellation_reason') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>


                                                        <!-- Button -->
                                                        <div class="row">
                                                            <div class="col-md-6 offset-md-3">
                                                                <a id="cancel_anchor" class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.virtualcard_withdrawal.index') }}">{{ __('Back') }}</a>
                                                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_card_withdrawal'))
                                                                <button type="submit" class="btn btn-theme f-14" id="withdrawalStatusUpdateBtn">
                                                                    <i class="fa fa-spinner fa-spin d-none"></i>
                                                                    <span id="withdrawalStatusUpdateBtnText">{{ __('Update') }}</span>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
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
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-6" for="amount">{{ __('Amount') }}</label>

                                            <div class="col-sm-6">
                                                <p class="form-control-static f-14">
                                                    {{
                                                        moneyFormat(
                                                            $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                                                            formatNumber(
                                                                $virtualcardWithdrawal->requested_fund,
                                                                $virtualcardWithdrawal->virtualcard?->currency()?->id,
                                                            )
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>


                                        <div class="form-group row total-deposit-feesTotal-space">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-6 d-flex justify-content-end" for="feesTotal">{{ __('Fees') }}
                                                <span>
                                                    <small class="transactions-edit-fee">
                                                        @if (isset($virtualcardWithdrawal))
                                                            ( {{ (formatNumber($virtualcardWithdrawal->percentage_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id))}}% + {{ formatNumber($virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id) }})
                                                        @else
                                                            ({{0}}%+{{0}})
                                                        @endif
                                                    </small>
                                                </span>
                                            </label>

                                            @php
                                                $feesTotal = $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees;
                                            @endphp


                                            <div class="col-sm-6">
                                                <p class="form-control-static f-14">
                                                    {{
                                                        moneyFormat(
                                                            $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                                                            formatNumber(
                                                                $feesTotal,
                                                                $virtualcardWithdrawal->virtualcard?->currency()?->id
                                                            )
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        <hr class="increase-hr-height">

                                        @php
                                            $total = $feesTotal + $virtualcardWithdrawal->requested_fund;
                                        @endphp

                                        <!-- Total -->
                                        <div class="form-group row total-deposit-space">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-6" for="total">{{ __('Total') }}</label>

                                            <div class="col-sm-6">
                                                <p class="form-control-static f-14">
                                                    {{
                                                        moneyFormat(
                                                            $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                                                            formatNumber(
                                                                $total,
                                                                $virtualcardWithdrawal->virtualcard?->currency()?->id,
                                                            )
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('extra_body_scripts')
<script>
    "use strict";
    var submitButtonText = "{{ __('Updating...') }}";
</script>
<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/admin/virtualcard_withdrawal.min.js') }}"></script>

@endpush
