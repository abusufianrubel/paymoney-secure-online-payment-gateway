@extends('admin.layouts.master')

@section('title', __('Edit Card'))

@section('head_style')
<link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/virtualcard_issue.min.css')}}">
@endsection

@section('page_content')
    <div class="box box-info" id="edit-section">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Update Card') }}</h3>
        </div>
        <form action="{{ route('admin.virtualcard.update', $virtualcard) }}" class="form-horizontal" id="virtualCardUpdateBtnForm" method="POST">
            @csrf

            <input type="hidden" value="{{ $virtualcard->currency_code }}" id="oldVirtualcardCurrencyCode">
            <input type="hidden" name="virtualcardHolderId" value="{{ $virtualcard->virtualcard_holder_id }}">

            <div class="box-body">

                <!-- Name -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label text-sm-end f-14 fw-bold">{{ __('Name on the Card') }}</label>
                    <div class="col-sm-6">
                        <input class="form-control f-14" type="text"  value="{{ cardTitle($virtualcard->virtualcardHolder) }}">
                    </div>
                </div>

                <!-- Providers -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="virtualcardProviderId">{{ __('Provider') }}</label>
                    <div class="col-sm-6">
                        <select class="select2 f-14" name="virtualcardProviderId" id="virtualcardProviderId" required>
                            @foreach ($virtualcardProviders as $provider)
                                <option value='{{ $provider->id }}' {{ $virtualcard->virtualcard_provider_id == $provider->id ? 'selected' : '' }}> {{ $provider->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Currency -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="currencyCode">{{ __('Currency') }}</label>
                    <div class="col-sm-6">
                        <select class="select2 f-14" name="currencyCode" id="currencyCode" required>

                        </select>
                    </div>
                </div>

                <!-- CardBrand -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="cardBrand">{{ __('Card Brand') }}</label>
                    <div class="col-sm-6">
                        <select class="select2 f-14" name="cardBrand" id="cardBrand" required>
                            @foreach (\Modules\Virtualcard\Enums\CardBrands::cases() as $brand)
                                <option value="{{ $brand->value }}" {{ $virtualcard->card_brand == $brand->value ? 'selected' : '' }}>{{ $brand->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Categories -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="virtualcardCategoryId">{{ __('Category') }}</label>
                    <div class="col-sm-6">
                        <select class="select2 f-14" name="virtualcardCategoryId" id="virtualcardCategoryId" required>
                            @foreach ($virtualcardCategories as $category)
                                <option value='{{ $category->id }}' {{ $virtualcard->virtualcard_category_id == $category->id ? 'selected' : '' }}> {{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- CardNumber -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 require control-label text-sm-end f-14 fw-bold" for="card_number">
                        {{ __('Card Number') }}
                    </label>
                    <div class="col-sm-6 card-input-wrapper">
                        <img id="cardLogo" class="custom-card-logo" alt="{{ __('Card Logo') }}" />
                        <input
                            class="form-control f-14"
                            placeholder="{{ __('Enter :x', ['x' => __('card number')]) }}"
                            name="cardNumber"
                            type="text"
                            id="cardNumber"
                            value="{{ $virtualcard->card_number }}"
                            required
                            maxlength="19"
                            data-value-missing="{{ __('This field is required.') }}"
                            data-max-length="{{ __(':x length should be maximum :y characters.', ['x' => __('Card number'), 'y' => '19']) }}"
                        >
                        @if($errors->has('cardNumber'))
                            <span class="error">
                                {{ $errors->first('cardNumber') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- CardCvc -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 require control-label text-sm-end f-14 fw-bold" for="cardCvc">{{ __('Card cvc') }}</label>
                    <div class="col-sm-6">
                        <input class="form-control f-14"
                        placeholder="{{ __('Enter :x', ['x' => __('card cvc')]) }}"
                        name="cardCvc" type="number" id="cardCvc"
                        value="{{ $virtualcard->card_cvc }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="4" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('CVC'), 'y' => 4]) }}"
                        >
                        @if($errors->has('cardCvc'))
                            <span class="error">
                                {{ $errors->first('cardCvc') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Amount -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 require control-label text-sm-end f-14 fw-bold" for="amount">{{ __('Amount') }}</label>
                    <div class="col-sm-6">
                        <input
                            class="form-control f-14"
                            placeholder="{{ __('Enter :x', ['x' => __('amount')]) }}"
                            name="amount"
                            type="text"
                            id="amount"
                            value="{{ $virtualcard->amount }}"
                            required data-value-missing="{{ __('This field is required.') }}"
                            onkeypress="return isNumberOrDecimalPointKey(this, event);"
                            oninput="restrictNumberToPrefdecimalOnInput(this)"
                        >
                        @if($errors->has('amount'))
                            <span class="error">
                                {{ $errors->first('amount') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Expiry Date -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 require control-label text-sm-end f-14 fw-bold" for="cardExpiryDate">{{ __('Expiry Date') }}</label>
                    <div class="col-sm-6">
                        <input class="form-control f-14" placeholder="{{ __('Enter :x', ['x' => __('expiry date')]) }}" name="cardExpiryDate" type="text" id="cardExpiryDate" value="{{ \Carbon\Carbon::createFromDate($virtualcard->expiry_year, $virtualcard->expiry_month, 1) ->format('d-m-Y') }}" required data-value-missing="{{ __('This field is required.') }}" >
                        @if($errors->has('cardExpiryDate'))
                            <span class="error">
                                {{ $errors->first('cardExpiryDate') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="status">{{ __('Status') }}</label>
                    <div class="col-sm-6">
                        <select class="select2 f-14" name="status" id="status" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                            <option value="Active" {{ $virtualcard->status == "Active" ? "selected" : '' }}>{{ __('Active') }}</option>
                            <option value='Inactive' {{ $virtualcard->status == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            <option value='Freezed' {{ $virtualcard->status == 'Freezed' ? 'selected' : '' }}>{{ __('Freeze') }}</option>
                            <option value='Terminated' {{ $virtualcard->status == 'Terminated' ? 'selected' : '' }}>{{ __('Termiate') }}</option>
                        </select>
                    </div>
                </div>


                <!-- Issue Button -->
                <div class="row">
                    <div class="col-sm-6 offset-md-3">
                        <a class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.virtualcard.index') }}" id="users_cancel">{{ __('Cancel') }}</a>
                        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_virtual_card'))
                        <button type="submit" class="btn btn-theme f-14" id="virtualCardUpdateBtn">
                            <i class="fa fa-spinner fa-spin d-none"></i>
                            <span id="virtualCardUpdateBtnText">{{ __('Update') }}</span>
                        </button>
                        @endif
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
    var decimalFormate = "{{ preference('decimal_format_amount', 2) }}";
    var caryptoFormate = "{{ preference('decimal_format_amount_crypto', 8) }}";

    const logoUrls = {
        visa: '{{ asset("Modules/Virtualcard/Resources/assets/attachments/virtual_cards/Visa_Logo.png") }}',
        mastercard: '{{ asset("Modules/Virtualcard/Resources/assets/attachments/virtual_cards/mastercard.png") }}',
        amex: '{{ asset("Modules/Virtualcard/Resources/assets/attachments/virtual_cards/amex.png") }}',
        discover: '{{ asset("Modules/Virtualcard/Resources/assets/attachments/virtual_cards/discover.png") }}',
        diners: '{{ asset("Modules/Virtualcard/Resources/assets/attachments/virtual_cards/diners_club.png") }}',
        jcb: '{{ asset("Modules/Virtualcard/Resources/assets/attachments/virtual_cards/jcb.png") }}',
        unionpay: '{{ asset("Modules/Virtualcard/Resources/assets/attachments/virtual_cards/unionpay.png") }}',
    };

</script>
<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/admin/virtualcard.min.js') }}" type="text/javascript"></script>
@endpush


