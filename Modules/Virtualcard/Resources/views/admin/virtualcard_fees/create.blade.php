@extends('admin.layouts.master')

@section('title', __('Add Fees Limit'))

@section('page_content')
    <div class="row" id="virtualCardFees">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('Add Fees Limit') }}</h3>
                </div>
                <form method="post" action="{{ route('admin.card_fees.store') }}" class="form-horizontal" id="virtualCardFeesForm">
                    @csrf
                    <div class="box-body">

                        <!-- Provider -->
                        <div class="form-group row">
                            <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="virtualcard_provider_id">{{ __('Provider') }}</label>
                            <div class="col-sm-6 col-md-6">
                                <select
                                    class="form-control select2 f-14"
                                    name="virtualcard_provider_id"
                                    id="virtualcard_provider_id"
                                    required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                >
                                    @foreach ($cardProvider as $provider)
                                        <option value="{{ $provider['id'] }}" {{ old('virtualcard_provider_id') == $provider['id'] ? 'selected' : '' }}>{{ $provider['name'] }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('virtualcard_provider_id'))
                                    <span class="error">{{ $errors->first('virtualcard_provider_id') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- currency -->
                        <div class="form-group row">
                            <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="currencyCode">{{ __('Currency') }}</label>
                            <div class="col-sm-6 col-md-6">
                                <select
                                    class="form-control select2 f-14 sl_common_bx"
                                    name="virtualcard_currency_code"
                                    id="currencyCode"
                                    required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                >
                                </select>
                                @if($errors->has('virtualcard_currency_code'))
                                    <span class="error">{{ $errors->first('virtualcard_currency_code') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Creation Fee -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="card_creation_fee">{{ __('Card Creation Fee') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="card_creation_fee"
                                        name="card_creation_fee"
                                        value="{{ old('card_creation_fee') }}"
                                        placeholder="{{ __('Card Creation Fee') }}"
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('card_creation_fee'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('card_creation_fee') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Topup Fixed Fee -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="topup_fixed_fee">{{ __('Topup Fixed Fee') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="topup_fixed_fee"
                                        name="topup_fixed_fee"
                                        value="{{ old('topup_fixed_fee') }}"
                                        placeholder="{{ __('Topup Fixed Fee') }}"
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('topup_fixed_fee'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('topup_fixed_fee') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Topup Percentage Fee -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="topup_percentage_fee">{{ __('Topup Percentage Fee') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="topup_percentage_fee"
                                        name="topup_percentage_fee"
                                        placeholder="{{ __('Topup Percentage Fee') }}"
                                        value="{{ old('topup_percentage_fee') }}"
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('topup_percentage_fee'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('topup_percentage_fee') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Topup Min Limit -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="topup_min_limit">{{ __('Topup Min Limit') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="topup_min_limit"
                                        name="topup_min_limit"
                                        placeholder="{{ __('Topup Percentage Fee') }}"
                                        value="{{ old('topup_min_limit') }}"
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('topup_min_limit'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('topup_min_limit') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Topup Max Limit -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="topup_max_limit">{{ __('Topup Max Limit') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="topup_max_limit"
                                        name="topup_max_limit"
                                        placeholder="{{ __('Topup Max Limit') }}"
                                        value="{{ old('topup_max_limit') }}"
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('topup_max_limit'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('topup_max_limit') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Withdrawal Fixed Fee -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="withdrawal_fixed_fee">{{ __('Withdrawal Fixed Fee') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="withdrawal_fixed_fee"
                                        name="withdrawal_fixed_fee"
                                        placeholder="{{ __('Withdrawal Fixed Fee') }}"
                                        required value="{{ old('withdrawal_fixed_fee') }}"
                                        data-value-missing="{{ __('This field is required.') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('withdrawal_fixed_fee'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('withdrawal_fixed_fee') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Withdrawal Percentage Fee -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="withdrawal_percentage_fee">{{ __('Withdrawal Percentage Fee') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="withdrawal_percentage_fee"
                                        name="withdrawal_percentage_fee"
                                        placeholder="{{ __('Withdrawal Percentage Fee') }}"
                                        required value="{{ old('withdrawal_percentage_fee') }}"
                                        data-value-missing="{{ __('This field is required.') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('withdrawal_percentage_fee'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('withdrawal_percentage_fee') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Withdrawal Min Limit -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="withdrawal_min_limit">{{ __('Withdrawal Min Limit') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="withdrawal_min_limit"
                                        name="withdrawal_min_limit"
                                        placeholder="{{ __('Withdrawal Min Limit') }}"
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        value="{{ old('withdrawal_min_limit') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('withdrawal_min_limit'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('withdrawal_min_limit') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Withdrawal Max Limit -->
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require" for="withdrawal_max_limit">{{ __('Withdrawal Max Limit') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input
                                        type="text"
                                        class="form-control f-14 required"
                                        id="withdrawal_max_limit"
                                        name="withdrawal_max_limit"
                                        placeholder="{{ __('Withdrawal Max Limit') }}"
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        value="{{ old('withdrawal_max_limit') }}"
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)"
                                        oninput="restrictNumberToPrefdecimalOnInput(this)"
                                    >
                                    @if ($errors->has('withdrawal_max_limit'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('withdrawal_max_limit') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- Status -->
                            <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="status">{{ __('Status') }}</label>
                            <div class="col-sm-6 col-md-6">
                                <select
                                    class="select2 f-14"
                                    name="status"
                                    id="status"
                                    required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                    <option value='Active' {{ old('status') == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value='Inactive' {{ old('status') == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 offset-md-3">
                                <a class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.card_fees.index') }}" id="packageCreateCancel">{{ __('Cancel') }}</a>
                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_card_fees_limit'))
                                <button type="submit" class="btn btn-theme f-14 float-right" id="virtualCardFeesSubmitBtn">
                                    <i class="fa fa-spinner fa-spin d-none"></i>
                                    <span id="virtualCardFeesSubmitBtnText">{{ __('Submit') }}</span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')

    @include('common.restrict_number_to_pref_decimal')
    @include('common.restrict_character_decimal_point')
    <script>
        "use strict";
        var submitButtonText = "{{ __('Submitting...') }}";
        var selectedCurrency = "{{ old('virtualcard_currency_code') ? old('virtualcard_currency_code') : '' }}";
        var providerId = '';
    </script>
    <script type="text/javascript" src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/admin/fees-limits.min.js') }}"></script>

@endpush
