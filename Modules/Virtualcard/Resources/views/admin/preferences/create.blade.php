@extends('admin.layouts.master')

@section('title', __('Virtualcard Preferences'))

@section('head_style')
  <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/select2/css/select2.min.css')}}">
@endsection

@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info" id="virtualCardPreference">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('Preferences') }}</h3>
                </div>

                <form action="{{ route('admin.virtualcard_preference.update') }}" class="form-horizontal" id="virtualCardPreferenceUpdateBtnForm" method="POST">
                    @csrf
                    <div class="box-body">

                        <!-- KYC -->
                        <div class="form-group row">
                            <label class="col-sm-3 mt-11 control-label f-14 fw-bold text-sm-end require" for="kyc">{{ __('KYC Verification') }}</label>
                            <div class="col-sm-6">
                                <select name="card_kyc" class="select2 sl_common_bx form-control" id="kyc" required data-value-missing="{{ __('This field is required.') }}">
                                    <option value="Yes" {{ preference('card_kyc') == 'Yes' ? 'selected' : "" }}>{{ __('Yes') }}</option>
                                    <option value="No" {{ preference('card_kyc') == 'No' ? 'selected' : "" }}>{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Holder Limit -->
                        <div class="form-group row">
                            <label class="col-sm-3 mt-11 control-label f-14 fw-bold text-sm-end require" for="holder_limit">{{ __('Holder Limit') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input 
                                        type="text" 
                                        class="form-control f-14 required" 
                                        id="holder_limit" 
                                        name="holder_limit"  
                                        placeholder="{{ __('Card Holder Limit') }}" 
                                        value="{{ preference('holder_limit') ? preference('holder_limit') : "" }}" 
                                        required data-value-missing="{{ __('This field is required.') }}"  
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)" 
                                        oninput="restrictNumberToPrefdecimalOnInput(this)" 
                                        maxlength="2" data-max-length="{{ __(':x length should be maximum :y.', ['x' => __('Cardholer limit'), 'y' => 2]) }}"
                                    >
                                    @if ($errors->has('holder_limit'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('holder_limit') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Limit -->
                        <div class="form-group row">
                            <label class="col-sm-3 mt-11 control-label f-14 fw-bold text-sm-end require" for="card_limit">{{ __('Card Limit') }}</label>
                            <div class="col-sm-6">
                                <div class="justify-content-between">
                                    <input 
                                        type="text" 
                                        class="form-control f-14 required" 
                                        id="card_limit" 
                                        name="card_limit"  
                                        placeholder="{{ __('Card Creation Limit') }}" 
                                        value="{{ preference('card_limit') ? preference('card_limit') : "" }}" 
                                        required data-value-missing="{{ __('This field is required.') }}"  
                                        onkeypress="return isNumberOrDecimalPointKey(this,event)" 
                                        oninput="restrictNumberToPrefdecimalOnInput(this)" 
                                        maxlength="2" 
                                    >
                                    @if ($errors->has('card_limit'))
                                        <span class="error">
                                            <strong class="text-danger">{{ $errors->first('card_limit') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Creator -->
                        <div class="form-group row" id="exchange_rate_div">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end require">{{ __('Card Creator') }}</label>
                            <div class="col-sm-6" >
                                <div class="form-check fw-bold f-14">
                                    <input class="form-check-input" type="radio" id="Users" name="card_creator" {{ preference('card_creator') == 'Users' ? 'checked':"" }} value="Users">
                                    <label for="Users">{{ __('Users') }}</label>
                                </div>
                                <div class="form-check fw-bold f-14">
                                    <input class="form-check-input" type="radio" id="Merchants" name="card_creator" {{ preference('card_creator') == 'Merchants' ? 'checked':"" }} value="Merchants" >
                                    <label for="Merchants">{{ __('Merchants') }}</label>
                                </div>
                                <div class="form-check fw-bold f-14">
                                    <input class="form-check-input" type="radio" id="Both" name="card_creator" {{ preference('card_creator') == 'Both' ? 'checked':"" }} value="Both" >
                                    <label for="Both">{{ __('Both') }}</label>
                                </div>
                                <span class="text-danger">{{ $errors->first('card_creator') }}</span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_card_preference'))
                                <button type="submit" class="btn btn-theme f-14" id="virtualCardPreferenceUpdateBtn"><i class="fa fa-spinner fa-spin d-none"></i> <span id="virtualCardPreferenceUpdateBtnText">{{ __('Update') }}</span></button>
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
        var submitButtonText = "{{ __('Updating...') }}";
    </script>
    <script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"  type="text/javascript" ></script>
    <script src="{{ asset('Modules/ManualVirtualcard/Resources/assets/js/Admin/virtualcard-preference.min.js') }}"></script>
@endpush