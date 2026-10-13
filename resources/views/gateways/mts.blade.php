@extends('gateways.layouts.master')

@section('content')
<form action="{{ route('gateway.confirm_payment') }}" method="post" id="walletPaymentForm">
    @csrf

    <input value="{{ csrf_token() }}" name="_token" id="token" type="hidden">
    <input value="{{ $payment_method }}" name="payment_method_id" id="payment_method_id" type="hidden">
    <input type="hidden" name="payment_type" id="payment_type" value="{{ $payment_type }}">
    <input type="hidden" name="transaction_type" id="transaction_type" value="{{ $transaction_type }}">
    <input type="hidden" name="currency_id" id="currency_id" value="{{ $currency_id }}">
    <input type="hidden" name="uuid" id="currency_id" value="{{  $uuid  }}">
    <input type="hidden" name="gateway" id="gateway" value="mts">
    <input type="hidden" name="amount" id="amount" value="{{ $total }}">
    <input type="hidden" name="total_amount" id="total_amount" value="{{ $total }}">
    <input type="hidden" name="redirect_url" id="total_amount" value="{{ $redirectUrl }}">
    @isset($user_id)
        <input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
    @endisset
    <input type="hidden" name="params" value="{{ $params }}">

    <div class="d-grid">
        <button class="btn btn-lg btn-primary" type="submit" id="walletSubmitBtn">
            <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none">
                <span class="visually-hidden"></span>
            </div>
            <span id="walletSubmitBtnText" class="px-1">{{ __('Pay with :x', ['x' => ucfirst(settings('name'))]) }}</span>
        </button>
    </div>
</form>


@endsection

@section('js')

<script src="{{ asset('public/dist/libraries/jquery/dist/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/jquery-validation/dist/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/jquery-validation/dist/additional-methods.min.js') }}" type="text/javascript"></script>

<script>
    "use strict";
    var submitText = "{{ __('Submitting...') }}";
    var preText = "{{ __('Payment Via Wallet') }}";
</script>

<script src="{{ asset('public/frontend/customs/js/gateways/mts.min.js') }}"></script>


@endsection











