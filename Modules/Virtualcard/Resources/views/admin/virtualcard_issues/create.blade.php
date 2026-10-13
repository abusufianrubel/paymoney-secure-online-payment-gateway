@extends('admin.layouts.master')

@section('title', __('Issue Card'))

@section('head_style')
<link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/virtualcard_issue.min.css')}}">
@endsection

@section('page_content')
    <div class="box box-info" id="user-create">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Issuing Card') }}</h3>
        </div>
        <form action="{{ route('admin.virtualcard_issue.store', [$virtualcardHolder, $virtualcardProvider, $virtualcard]) }}" class="form-horizontal" id="virtualCardIssueForm" method="POST">
            @csrf

            <input type="hidden" name="virtualcardHolderId" value="{{ $virtualcardHolder->id }}">
            <input type="hidden" name="virtualcard" value="{{ $virtualcard->id }}">
            <input type="hidden" name="virtualcardProviderId" value="{{ $virtualcardProvider->id }}">

            <div class="box-body">

                <!-- Provider -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label text-sm-end f-14 fw-bold">{{ __('Provider') }}</label>
                    <div class="col-sm-6">
                        <input class="form-control f-14" type="text"  value="{{ $virtualcardProvider->name }}" readonly>
                    </div>
                </div>
                <!-- Name -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label text-sm-end f-14 fw-bold">{{ __('Name on the Card') }}</label>
                    <div class="col-sm-6">
                        <input class="form-control f-14" type="text"  value="{{ cardTitle($virtualcardHolder) }}">
                    </div>
                </div>

                <!-- Categories -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="virtualcardCategoryId">{{ __('Category') }}</label>
                    <div class="col-sm-6">
                        <select
                            class="select2 f-14 form-control sl_common_bx"
                            name="virtualcardCategoryId"
                            id="virtualcardCategoryId"
                            required data-value-missing="{{ __('This field is required.') }}"
                        >
                            @foreach ($virtualcardCategories as $category)
                                <option value='{{ $category->id }}'
                                    @if (old('virtualcardCategoryId') && old('virtualcardCategoryId') == $category->id) {{ 'selected="selected"' }}
                                    @elseif (!empty($virtualcard->virtualcard_category_id) && $virtualcard->virtualcard_category_id == $category->id) {{ 'selected="selected"' }}
                                    @endif

                                > {{ $category->name }}</option>
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
                            value="{{ old('cardNumber') }}"
                            maxlength="19"
                            required data-value-missing="{{ __('This field is required.') }}"
                            data-max-length="{{ __(':x length should be maximum :y characters.', ['x' => __('Card number'), 'y' => 19 ]) }}"
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
                        <input
                            class="form-control f-14"
                            placeholder="{{ __('Enter :x', ['x' => __('card cvc')]) }}"
                            name="cardCvc" type="number"
                            id="cardCvc"
                            value="{{ old('cardCvc') }}"
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

                <!-- Expiry Date -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 require control-label text-sm-end f-14 fw-bold" for="cardExpiryDate">{{ __('Expiry Date') }}</label>
                    <div class="col-sm-6">
                        <input
                            class="form-control f-14"
                            placeholder="{{ __('Enter :x', ['x' => __('expiry date')]) }}"
                            name="cardExpiryDate" type="text"
                            id="cardExpiryDate"
                            value="{{ old('cardExpiryDate') }}"
                            required data-value-missing="{{ __('This field is required.') }}"
                        >
                        @if($errors->has('cardExpiryDate'))
                            <span class="error">
                                {{ $errors->first('cardExpiryDate') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Currency -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="currencyCode">{{ __('Currency') }}</label>
                    <div class="col-sm-6">
                        <select
                            class="select2 f-14 form-control sl_common_bx"
                            name="currencyCode"
                            id="currencyCode"
                            required data-value-missing="{{ __('This field is required.') }}"
                        >
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->code }}"
                                    @if (old('currencyCode') && old('currencyCode') == $currency->code)
                                        {{ 'selected="selected"' }}
                                    @elseif ( $virtualcard->currency_code == $currency->code)
                                        {{ 'selected="selected"' }}
                                    @endif
                                >
                                    {{ $currency->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                 <!-- CardBrand -->
                 <div class="form-group row">
                    <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="cardBrand">{{ __('Card Brand') }}</label>
                    <div class="col-sm-6">
                        <select
                            class="select2 f-14 form-control sl_common_bx"
                            name="cardBrand"
                            id="cardBrand"
                            required data-value-missing="{{ __('This field is required.') }}"
                        >
                            @foreach (\Modules\Virtualcard\Enums\CardBrands::cases() as $brand)
                                <option value="{{ $brand->value }}"
                                    @if (old('cardBrand') && old('cardBrand') == $brand->value) {{ 'selected="selected"' }}
                                    @elseif (!empty($virtualcard->card_brand) && $virtualcard->card_brand == $brand->value) {{ 'selected="selected"' }}
                                    @endif
                                >{{ $brand->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Amount -->
                <div class="form-group row">
                    <label class="col-sm-3 mt-11 require control-label text-sm-end f-14 fw-bold" for="amount">{{ __('Amount') }}</label>
                    <div class="col-sm-6">
                        <input
                            class="form-control f-14"
                            placeholder="{{ __('Enter amount', ['x' => __('amount')]) }}"
                            name="amount"
                            type="text"
                            id="amount"
                            value="{{ old('amount') }}"
                            required data-value-missing="{{ __('This field is required.') }}"
                            onkeypress="return isNumberOrDecimalPointKey(this, event);" oninput="restrictNumberToPrefdecimalOnInput(this)"
                        >
                        @if($errors->has('amount'))
                            <span class="error">
                                {{ $errors->first('amount') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Issue Button -->
                <div class="row">
                    <div class="col-sm-6 offset-md-3">
                        <a class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.virtualcard_holder.index') }}" id="users_cancel">{{ __('Cancel') }}</a>
                        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_virtual_card'))
                        <button type="submit" class="btn btn-theme f-14" id="virtualCardIssueBtn">
                            <i class="fa fa-spinner fa-spin d-none"></i>
                            <span id="virtualCardIssueBtnText">{{ __('Issue Card') }}</span>
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
        var submitButtonText = "{{ __('Assigning...') }}";

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
    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/admin/virtualcard_issue.min.js') }}" type="text/javascript"></script>
    
@endpush


