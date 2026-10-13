@extends('admin.layouts.master')
@section('title', __('Edit Transaction'))
@section('page_content')

@if ($transaction->transaction_type_id == Virtualcard_Topup)
    <div class="box box-default">
        <div class="box-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="top-bar-title padding-bottom pull-left">{{ __('Transaction Details') }}</div>
                </div>
                <!-- Transaction Status -->
                <div>
                    @if ($transaction->status)
                        <p class="text-left mb-0 f-18">{{ __('Status') }} :
                            @php
                                $transactionTypes = config('virtualcard.transaction_types');
                                if (in_array($transaction->transaction_type_id, $transactionTypes)) {
                                    echo getStatusText($transaction->status);
                                }
                                $virtualcardTransData = virtualcardPaymentDetails($transaction->transaction_reference_id, $transaction->transaction_type_id);
                            @endphp
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <section class="min-vh-100">
        <div class="my-30">
            <div class="row f-14">
                <!-- Page title start -->
                <form action="{{ route('admin.virtualcard_topup.statusChange', $virtualcardTransData) }}" class="form-horizontal row" id="TopupStatusUpdateForm" method="POST">
                    <input type="hidden" class="form-control" name="topupStatusUpdate" value="Yes" id="topupStatusUpdate">
                    @csrf
                    <div class="col-md-8">
                        <div class="box">
                            <div class="box-body">
                                <div class="panel">
                                    <div>
                                        <div class="p-4 rounded">

                                            <!-- User -->
                                            <div class="form-group row">
                                                <label class="control-label col-sm-3 fw-bold text-sm-end">{{ __('User') }}</label>
                                                <div class="col-sm-9">
                                                    <p class="form-control-static">
                                                        {{ getColumnValue($transaction->user) }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!--Card Brand -->
                                            <div class="form-group row">
                                                <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Card Brand') }}</label>
                                                <div class="col-sm-6">
                                                    <p class="form-control-static f-14">{{ $virtualcardTransData->virtualcard?->card_brand }}</p>
                                                </div>
                                            </div>

                                            <!-- Card Number -->
                                            <div class="form-group row">
                                                <label class="control-label fw-bold f-14 text-sm-end col-sm-3" for="deposit_uuid">{{ __('Card Number') }}</label>
                                                <div class="col-sm-6">
                                                    <p class="form-control-static f-14">{{ formatCardNumber($virtualcardTransData->virtualcard?->card_number) ?? '-' }}</p>
                                                </div>
                                            </div>

                                            <!-- Transaction ID -->
                                            <div class="form-group row">
                                                <label class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Transaction ID') }}</label>
                                                <div class="col-sm-9">
                                                    <p class="form-control-static">
                                                        {{ getColumnValue($transaction, 'uuid') }}</p>
                                                </div>
                                            </div>

                                            <!-- Type -->
                                            @if ($transaction->transaction_type_id)
                                                <div class="form-group row">
                                                    <label class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Type') }}</label>
                                                    <div class="col-sm-9">
                                                        <p class="form-control-static">
                                                            {{ str_replace('_', ' ', $transaction->transaction_type?->name) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Currency Code -->
                                            <div class="form-group row">
                                                <label
                                                    class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Currency Code') }}</label>
                                                <div class="col-sm-9">
                                                    <p class="form-control-static">
                                                        {{ getColumnValue($transaction->currency, 'code') }}</p>
                                                </div>
                                            </div>

                                            <!-- Created at date -->
                                            <div class="form-group row">
                                                <label
                                                    class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Date') }}</label>
                                                <div class="col-sm-9">
                                                    <p class="form-control-static">
                                                        {{ dateFormat(getColumnValue($transaction, 'created_at')) }}</p>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            @php
                                                $approvalStatus = old('fund_approval_status') ? old('fund_approval_status') : $virtualcardTransData->fund_approval_status;
                                            @endphp

                                            @if ($virtualcardTransData->fund_approval_status != 'Approved')
                                                <div class="form-group row align-items-center">
                                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3" for="fund_approval_status">{{ __('Change Status') }}</label>
                                                    <div class="col-sm-6">
                                                        <select class="form-control select2 w-60" name="fund_approval_status" id="fund_approval_status">
                                                            <option value="Approved" {{ $approvalStatus ==  'Approved'? 'selected':"" }}>{{ __('Approve') }}</option>
                                                            <option value="Pending"  {{ $approvalStatus == 'Pending' ? 'selected':"" }}>{{ __('Pending') }}</option>
                                                            <option value="Cancelled"  {{ $approvalStatus == 'Cancelled' ? 'selected':"" }}>{{ __('Cancel') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Cancellation Reason -->
                                            <div class="form-group row" id="cancellation_reason_div">
                                                <label class="col-sm-3 control-label require mt-11 text-sm-end f-14 fw-bold" for="cancellation_reason">{{ __('Cancellation Reason') }}</label>
                                                <div class="col-sm-6">
                                                    <textarea
                                                        name="cancellation_reason"
                                                        class="form-control f-14"
                                                        id="cancellation_reason"
                                                        placeholder="{{ __('Enter :x', ['x' => __('Cancellation Reason')]) }}"
                                                        data-value-missing="{{ __('This field is required.') }}"
                                                    >{{ old('cancellation_reason') ? old('cancellation_reason') : $virtualcardTransData->cancellation_reason }}</textarea>

                                                    @if ($errors->has('cancellation_reason'))
                                                        <span class="error">
                                                            <strong class="text-danger">{{ $errors->first('cancellation_reason') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 offset-md-3">
                                                    <a id="cancel_anchor" class="btn btn-theme-danger me-1 f-14" href="{{ url(config('adminPrefix') . '/transactions') }}">{{ __('Back') }}</a>
                                                    @if ($virtualcardTransData->fund_approval_status != 'Approved' && Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_card_topup'))
                                                    <button type="submit" class="btn btn-theme f-14" id="TopupStatusUpdateBtn">
                                                        <i class="fa fa-spinner fa-spin d-none"></i> <span id="TopupStatusUpdateBtnText">{{ __('Update') }}</span>
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Amount Section -->
                    <div class="col-md-4">
                        <div class="box">
                            <div class="box-body">
                                <div class="panel">
                                    <div>
                                        <div class="pt-4 rounded">
                                            @if ($transaction->subtotal)
                                                <div class="form-group row">
                                                    <label class="control-label col-sm-6 fw-bold text-sm-end">{{ __('Amount') }}</label>
                                                    <div class="col-sm-6">
                                                        {{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->subtotal, $transaction->currency_id)) }}
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="form-group row total-deposit-feesTotal-space">

                                                <label
                                                    class="control-label col-sm-6 d-flex fw-bold justify-content-end">{{ __('Fees') }}
                                                    <span>
                                                        <small class="transactions-edit-fee">
                                                            @if (isset($transaction))
                                                                ({{ formatNumber($transaction->percentage, $transaction->currency_id) }}%
                                                                +
                                                                {{ formatNumber($transaction->charge_fixed, $transaction->currency_id) }})
                                                            @else
                                                                (0% + 0)
                                                            @endif
                                                        </small>
                                                    </span>
                                                </label>
                                                @php
                                                    $totalFees = $transaction->charge_percentage + $transaction->charge_fixed;
                                                @endphp

                                                <div class="col-sm-6">
                                                    <p class="form-control-static">
                                                        {{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($totalFees, $transaction?->currency_id)) }}
                                                    </p>

                                                </div>
                                            </div>

                                            <hr class="increase-hr-height">

                                            @if ($transaction->total)
                                                <div class="form-group row total-deposit-space">
                                                    <label
                                                        class="control-label col-sm-6 fw-bold text-sm-end">{{ __('Total') }}</label>
                                                    <div class="col-sm-6">
                                                        <p class="form-control-static">
                                                            {{ moneyFormat(optional($transaction->currency)->symbol, str_replace('-', '', formatNumber($transaction->total, $transaction->currency_id))) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endif

<!-- Virtualcard Withdrawal Transaction -->
@if ($transaction->transaction_type_id == Virtualcard_Withdrawal)
    <div class="box box-default">
        <div class="box-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="top-bar-title padding-bottom pull-left">{{ __('Transaction Details') }}</div>
                </div>
                <!-- Transaction Status -->
                <div>
                    @if ($transaction->status)
                        <p class="text-left mb-0 f-18">{{ __('Status') }} :
                            @php
                                $transactionTypes = config('virtualcard.transaction_types');
                                if (in_array($transaction->transaction_type_id, $transactionTypes)) {
                                    echo getStatusText($transaction->status);
                                }
                                $virtualcardTransData = virtualcardPaymentDetails($transaction->transaction_reference_id, $transaction->transaction_type_id);
                            @endphp
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <section class="min-vh-100">
        <div class="my-30">
            <div class="row f-14">
                <!-- Page title start -->
                <div class="col-md-8">
                    <div class="box">
                        <div class="box-body">
                            <div class="panel">
                                <div>
                                    <div class="p-4 rounded">

                                        <!-- User -->
                                        <div class="form-group row">
                                            <label
                                                class="control-label col-sm-3 fw-bold text-sm-end">{{ __('User') }}</label>
                                            <div class="col-sm-9">
                                                <p class="form-control-static">
                                                    {{ getColumnValue($transaction->user) }}
                                                </p>
                                            </div>
                                        </div>

                                        <!--Card Brand -->
                                        <div class="form-group row">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Card Brand') }}</label>
                                            <div class="col-sm-6">
                                                <p class="form-control-static f-14">{{ $virtualcardTransData->virtualcard?->card_brand }}</p>
                                            </div>
                                        </div>

                                        <!-- Card Number -->
                                        <div class="form-group row">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-3" for="deposit_uuid">{{ __('Card Number') }}</label>
                                            <div class="col-sm-6">
                                                <p class="form-control-static f-14">{{ formatCardNumber($virtualcardTransData->virtualcard?->card_number) ?? '-' }}</p>
                                            </div>
                                        </div>

                                        <!-- Transaction ID -->
                                        <div class="form-group row">
                                            <label class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Transaction ID') }}</label>
                                            <div class="col-sm-9">
                                                <p class="form-control-static">
                                                    {{ getColumnValue($transaction, 'uuid') }}</p>
                                            </div>
                                        </div>

                                        <!-- Type -->
                                        @if ($transaction->transaction_type_id)
                                            <div class="form-group row">
                                                <label class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Type') }}</label>
                                                <div class="col-sm-9">
                                                    <p class="form-control-static">
                                                        {{ str_replace('_', ' ', $transaction->transaction_type?->name) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Currency Code -->
                                        <div class="form-group row">
                                            <label
                                                class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Currency') }}</label>
                                            <div class="col-sm-9">
                                                <p class="form-control-static">{{ getColumnValue($transaction->currency, 'code') }}</p>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label
                                                class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Requested') }}</label>
                                            <div class="col-sm-9">
                                                <p class="form-control-static">{{ dateFormat($transaction->virtualcardWithdrawal?->fund_request_time) }}</p>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label
                                                class="control-label col-sm-3 fw-bold text-sm-end">{{ __('Completed') }}</label>
                                            <div class="col-sm-9">
                                                <p class="form-control-static">{{ $transaction->virtualcardWithdrawal?->fund_release_time ? dateFormat($transaction->virtualcardWithdrawal?->fund_release_time) : '-' }}</p>
                                            </div>
                                        </div>
                                        <!-- Status -->
                                        @php
                                            $approvalStatus = old('fund_approval_status') ? old('fund_approval_status') : $virtualcardTransData->fund_approval_status;
                                        @endphp

                                        @if ($virtualcardTransData->fund_approval_status != 'Approved')

                                            <form action="{{ route('admin.virtualcard_withdrawal.update', $virtualcardTransData) }}" class="form-horizontal row" id="TopupStatusUpdateForm" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <!-- Status -->
                                                <div class="form-group row align-items-center">
                                                    <label class="control-label fw-bold f-14 text-sm-end col-sm-3" for="withdrawalFundApprovalStatus">{{ __('Change Status') }}</label>
                                                    <div class="col-sm-6">
                                                        <select
                                                            class="form-control select2 w-60"
                                                            name="fund_approval_status"
                                                            id="withdrawalFundApprovalStatus"
                                                        >
                                                            <option value="Approved" {{ $approvalStatus ==  'Approved'? 'selected':"" }}>{{ __('Approve') }}</option>
                                                            <option value="Pending"  {{ $approvalStatus == 'Pending' ? 'selected':"" }}>{{ __('Pending') }}</option>
                                                            <option value="Cancelled"  {{ $approvalStatus == 'Cancelled' ? 'selected':"" }}>{{ __('Cancel') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Cancellation Reason -->
                                                <div class="form-group row" id="withdrawalCancellationReasonDiv">
                                                    <label class="col-sm-3 control-label require mt-11 text-sm-end f-14 fw-bold" for="withdrawalCancellationReason">{{ __('Cancellation Reason') }}</label>
                                                    <div class="col-sm-6">
                                                        <textarea
                                                            name="cancellation_reason"
                                                            class="form-control f-14"
                                                            id="withdrawalCancellationReason"
                                                            placeholder="{{ __('Enter :x', ['x' => __('Cancellation Reason')]) }}"
                                                            data-value-missing="{{ __('This field is required.') }}"
                                                        >{{ old('cancellation_reason') ? old('cancellation_reason') : $virtualcardTransData->cancellation_reason }}</textarea>
                                                        @if ($errors->has('cancellation_reason'))
                                                            <span class="error">
                                                                <strong class="text-danger">{{ $errors->first('cancellation_reason') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 offset-md-3">
                                                        <a id="cancel_anchor" class="btn btn-theme-danger me-1 f-14" href="{{ url(config('adminPrefix') . '/transactions') }}">{{ __('Back') }}</a>
                                                        @if ($virtualcardTransData->fund_approval_status != 'Approved' && Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_card_withdrawal'))
                                                        <button type="submit" class="btn btn-theme f-14" id="TopupStatusUpdateBtn">
                                                            <i class="fa fa-spinner fa-spin d-none"></i> <span id="TopupStatusUpdateBtnText">{{ __('Update') }}</span>
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
                <!-- Amount Section -->
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-body">
                            <div class="panel">
                                <div>
                                    <div class="pt-4 rounded">
                                        @if ($transaction->subtotal)
                                            <div class="form-group row">
                                                <label
                                                    class="control-label col-sm-6 fw-bold text-sm-end">{{ __('Amount') }}</label>
                                                <div class="col-sm-6">
                                                    {{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->subtotal, $transaction->currency_id)) }}
                                                </div>
                                            </div>
                                        @endif

                                        <div class="form-group row total-deposit-feesTotal-space">

                                            <label
                                                class="control-label col-sm-6 d-flex fw-bold justify-content-end">{{ __('Fees') }}
                                                <span>
                                                    <small class="transactions-edit-fee">
                                                        @if (isset($transaction))
                                                            ({{ formatNumber($transaction->percentage, $transaction->currency_id) }}%
                                                            +
                                                            {{ formatNumber($transaction->charge_fixed, $transaction->currency_id) }})
                                                        @else
                                                            (0% + 0)
                                                        @endif
                                                    </small>
                                                </span>
                                            </label>
                                            @php
                                                $totalFees = $transaction->charge_percentage + $transaction->charge_fixed;
                                            @endphp

                                            <div class="col-sm-6">
                                                <p class="form-control-static">
                                                    {{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($totalFees, $transaction?->currency_id)) }}
                                                </p>

                                            </div>
                                        </div>

                                        <hr class="increase-hr-height">

                                        @if ($transaction->total)
                                            <div class="form-group row total-deposit-space">
                                                <label
                                                    class="control-label col-sm-6 fw-bold text-sm-end">{{ __('Total') }}</label>
                                                <div class="col-sm-6">
                                                    <p class="form-control-static">
                                                        {{ moneyFormat(optional($transaction->currency)->symbol, str_replace('-', '', formatNumber($transaction->total, $transaction->currency_id))) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

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
