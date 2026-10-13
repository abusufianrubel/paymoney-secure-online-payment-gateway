@extends('gateways.layouts.master')

@section('content')

<div class="col-md-12">
    <form method="POST" action="{{ route('gateway.confirm_payment')}}"  id="payment-form">
        @csrf
        <input type="hidden" name="currency_id" id="currency_id" value="{{ $currency_id }}">
        <input type="hidden" name="amount" id="amount" value="{{ $total }}">
        <input type="hidden" name="uuid" id="uuid" value="{{ $uuid }}">
        <input type="hidden" name="payment_method" id="payment_method" value="{{ $payment_method }}">
        <input type="hidden" name="payment_method_id" id="payment_method_id" value="{{ $payment_method }}">
        <input type="hidden" name="transaction_type" id="transaction_type" value="{{ $transaction_type }}">
        <input type="hidden" name="payment_type" id="payment_type" value="{{ $payment_type }}">
        <input type="hidden" name="params" id="payment_type" value="{{ $params }}">
        <input type="hidden" name="gateway" id="gateway" value="flutterwave">

        <div class="row">
            <div class="col-12">
                @if ($transaction_type == Payment_Sent || (defined('Crypto_Buy') && $transaction_type == Crypto_Buy) || (defined('Donation_Sent') && $transaction_type == Donation_Sent) || (defined('Invoice_Payment_Received') && $transaction_type == Invoice_Payment_Received))
                
                    <div class="form-group mb-3">
                        <label class="form-label" for="name">{{ __('Name') }} <span class="star">*</span></label>
                        <input type="text" class="form-control input-form-control" placeholder="{{ __('Put your name here.') }}" name="name" id="name" required="" data-value-missing="{{ __('This field is required.') }}" value="{{ getColumnValue(auth()->user()) === '-' ? '' : getColumnValue(auth()->user()) }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="email">{{ __('Email Address') }} <span class="star">*</span></label>
                        <input type="email" class="form-control input-form-control" placeholder="{{ __('Email Address') }}" name="email" id="email" required="" data-value-missing="{{ __('This field is required.') }}" value="{{ auth()->user() ? auth()->user()->email : '' }}">
                    </div>
                @endif

                <div class="d-grid mt-3p">
                    <button type="submit" class="btn btn-lg btn-primary" type="submit" id="flutterwave-button-submit">
                        <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none">
                            <span class="visually-hidden"></span>
                        </div>
                        <span id="flutterwaveSubmitBtnText" class="px-1">{{ __('Pay with :x', ['x' => ucfirst($gateway)]) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="{{ asset('public/dist/plugins/jquery-validation/dist/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/debounce/jquery.ba-throttle-debounce.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"></script>
<script type="text/javascript">
    'use strict';
    var submitting = "{{ __('Submitting...') }}";
</script>
<script src="{{ asset('public/frontend/customs/js/gateways/flutterwave.min.js') }}"></script>
@endsection
