@php
    $amount = tokenFormat($cryptoTrx['amount'], $cryptoTrx['token_decimals']);
    $networkFee = formatNumber($cryptoTrx['network_fee'], $cryptoTrx['currency_id']);
    $network = $cryptoTrx['network'];
    $user_id = $cryptoTrx['user_id'];
    $user_full_name = $cryptoTrx['user_full_name'];
    $total = tokenFormat($cryptoTrx['amount'], $cryptoTrx['token_decimals']);
@endphp

@extends('admin.layouts.master')

@section('title', __('Crypto Receive Confirm'))

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/TatumIo/Resources/assets/admin/css/crypto_sent_receive.min.css') }}">
@endsection

@section('page_content')
<div class="row">
    <div class="col-md-2">
        <button type="button" class="btn btn-theme active mt-15 f-14">{{ __('Crypto Receive') }}</button>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-4">
        <div class="pull-right">
            <h3 class="f-24">{{ $user_full_name }}</h3>
        </div>
    </div>
</div>

<div class="box mt-20">
    <div class="box-body" id="crypto-receive-confirm">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-7">

                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h3 class="text-center f-24"><strong>{{ __('Details') }}</strong></h3>
                                <p class="text-center f-16"><strong>{{ __('Natework fee will deduct from user native wallet :x', ['x' => $network] )}}</strong></p>
                                <div class="row">
                                    <div class="col-md-6 pull-left f-14">{{ __('Receive Amount') }}</div>
                                    <div class="col-md-6  text-sm-end f-14"><strong>{{ moneyFormat($cryptoTrx['currency_symbol'], tokenFormat($amount, $cryptoTrx['token_decimals'])) }}</strong></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pull-left f-14">{{ __('Network Fee') }}</div>
                                    <div class="col-md-6 text-sm-end f-14"><strong>{{ moneyFormat($network, $networkFee) }}</strong></div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-6 pull-left f-14"><strong>{{ __('Total') }}</strong></div>
                                    <div class="col-md-6 text-sm-end f-14"><strong>{{ moneyFormat($cryptoTrx['currency_symbol'], $total) }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="ml-0">
                            <div class="float-left">
                                <a href="#" class="admin-user-crypto-receive-confirm-back-link">
                                    <button class="btn btn-danger admin-user-crypto-receive-confirm-back-btn f-14"><strong><i class="fa fa-angle-left"></i>{{ __('Back') }}</strong></button>
                                </a>
                            </div>
                            <div class="float-right">
                                <form action="{{ route('admin.tatum.token_receive.success') }}" method="POST" id="admin-user-crypto-receive-confirm" novalidate="novalidate">
                                    <input value="{{ csrf_token() }}" name="_token" id="token" type="hidden">
                                    <input type="hidden" name="network" value="{{ $network }}">
                                    <input type="hidden" name="token_id" value="{{ $cryptoTrx['token_id'] }}">

                                    <button type="submit" class="btn btn-theme f-14" id="admin-user-crypto-receive-confirm-btn">
                                        <i class="fa fa-spinner fa-spin d-none"></i>
                                        <span id="admin-user-crypto-receive-confirm-btn-text">
                                            <strong>{{ __('Confirm') }}<i class="fa fa-angle-right"></i></strong>
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('extra_body_scripts')

<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"  type="text/javascript" ></script>

<script type="text/javascript">
    'use strict';
    var cryptoReceiveBackUrl= '{{ route("admin.tatumio.token.receive", [encrypt($network), encrypt($cryptoTrx["token_id"]) ]) }}';
    var confirm = '{{ __("Confirm") }}';
    var confirming = '{{ __("Confirming...") }}';
</script>
<script src="{{ asset('Modules/TatumIo/Resources/assets/admin/js/crypto_receive.min.js') }}"  type="text/javascript"></script>
@endpush
