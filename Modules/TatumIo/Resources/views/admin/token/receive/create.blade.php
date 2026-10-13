@extends('admin.layouts.master')

@section('title', __('Token Receive'))

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/TatumIo/Resources/assets/admin/css/crypto_sent_receive.min.css') }}">
@endsection

@section('page_content')
    <div class="row">
        <div class="col-md-2">
            <button type="button" class="btn btn-theme active mt-15 f-14">{{ __('Token Receive') }}</button>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-4">
            <div class="pull-right">
                <h3 class="user-full-name f-24">{{ __('Username') }}</h3>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="box box-info" id="crypto-receive-create">

                <form action="{{ route('admin.tatum.token_receive.confirm') }}" class="form-horizontal" id="admin-crypto-receive-form" method="POST">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" id="token"/>
                        <div class="box-body">

                            <!-- Token -->
                            <div class="form-group row align-items-center" id="token-div">
                                <label class="col-sm-3 control-label f-14 fw-bold text-sm-end" for="user">{{ __('Token') }}</label>
                                <div class="col-sm-6">
                                    <input class="form-control f-14 token" data-type="{{ $tokenDetails->symbol }}" name="token" type="text" value="{{ $tokenDetails->symbol }}" id="token" readonly>
                                </div>
                            </div>

                            <!-- Network -->
                            <div class="form-group row align-items-center" id="network-div">
                                <label class="col-sm-3 control-label f-14 fw-bold text-sm-end" for="user">{{ __('Network') }}</label>
                                <div class="col-sm-6">
                                    <input class="form-control f-14" name="network" type="text" value="{{ $network }}" id="network" class="network" readonly>
                                </div>
                            </div>

                            <!-- User -->
                            <div class="form-group row align-items-center" id="user-div">
                                <label class="col-sm-3 control-label f-14 fw-bold text-sm-end" for="user">{{ __('User') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control f-14 select2" name="user_id" id="user_id">
                                        <option value="">{{ __('Please select a user') }}</option>
                                        @foreach ($users as $key => $user)
                                            <option value='{{ $user->id }}'>{{ getColumnValue($user) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')

<script src="{{ asset('public/dist/plugins/debounce/jquery.ba-throttle-debounce.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/dist/libraries/sweetalert/sweetalert-unpkg.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"  type="text/javascript" ></script>

@include('common.restrict_number_to_pref_decimal')
@include('common.restrict_character_decimal_point')

<script type="text/javascript">
    'use strict';
    var network = '{{ $network }}';
    var cryptoToken = '{{ $tokenDetails->symbol }}';
    var tokenAddress = '{{ $tokenDetails->address }}';
    var tokenDecimals = '{{ $tokenDetails->decimals }}';
    var userBalanceWithMerchantAddressUrl = '{{ route("admin.tatum.token.address_balance_user") }}';
    var confirmationCryptoReceivedUrl = '{{ route("admin.tatum.token_receive.confirm") }}';
    var backButtonUrl = '{{ route("admin.tatumio.token") }}';
    var validateBalanceUrl = '{{ route("admin.tatum.token.validate_balance_user") }}';
    var pleaseWait = '{{ __("Please Wait") }}';
    var loading = '{{ __("Loading...") }}';
    var merchantCryptoAddress = '{{ __("Merchant Address") }}';
    var tokenCryptoAddress = '{{ __("Token Address") }}';
    var tokenLabel = '{{ __("Token Address") }}';
    var usertokenBalance = '{{ __("User Token Balance") }}';
    var userCryptoBalance = '{{ __("User Balance") }}';
    var userCryptoAddress = '{{ __("User Address") }}';
    var cryptoReceivedAmount = '{{ __("Amount") }}';
    var minAmount = '{{ __("The minimum amount must be :x") }}';
    var requiredField = '{{ __("This field is required.") }}';
    var cryptoTransactionText = '{{ __("Crypto transactions might take few moments to complete.") }}';
    var minWithdrawan = '{{ __("The amount withdrawn/sent must at least be :x") }}';
    var minNetworkFee = '{{ __("Please keep at least :x for network fees.") }}';
    var networkFeeText = '{{ __("Natework fee will deduct from user native wallet :x") }}';
    var backButton = '{{ __("Back") }}';
    var nextButton = '{{ __("Next") }}';
    var errorText = '{{ __("Error!") }}'
    var receiving = '{{ __("Receiving...") }}';
    var receive = '{{ __("Receive") }}';
    var low = '{{ __("Low") }}';
    var medium = '{{ __("Medium") }}';
    var high = '{{ __("High") }}';
    var tatumIoMinLimit = "{{ $minTatumIoLimit }}";
</script>
<script src="{{ asset('Modules/TatumIo/Resources/assets/admin/js/token_receive.min.js') }}"  type="text/javascript"></script>

@endpush
