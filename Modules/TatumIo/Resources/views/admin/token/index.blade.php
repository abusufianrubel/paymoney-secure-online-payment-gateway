@extends('admin.layouts.master')

@section('title', __('Crypto Token'))

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/libraries/sweetalert/sweetalert.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/TatumIo/Resources/assets/admin/css/provider.min.css') }}">
@endsection

@section('page_content')
    
    <!-- Provider header -->
    <div class="crypto-header">
        <div class="box-body box-body-navbar">
            <div class="d-flex justify-content-between flex-wrap">
                <div class="d-flex flex-row crypto-name-status justify-content-between flex-wrap">
                    <div class="top-bar-title padding-bottom pull-left">
                       TatumIo
                        <strong class="crypto-text-success">
                            <small>
                                {{ !$provider->cryptoAssetSettings->isEmpty() ? '(' : '' }}
                                {{ json_decode($provider->subscription_details)->current_plan ?? '' }}
                                {{ !$provider->cryptoAssetSettings->isEmpty() ? ')' : '' }}
                            </small>
                        </strong>
                    </div>
                    <div class="crypto-card-2-logo-color-sample crypto-green"></div>
                </div>
                <div class="d-flex add-search">
                    <div id="container-search" class="container-search">
                        <input id="crypto-search-input" class="crypto-search-input" type="text" placeholder="Search">
                        <i id="crypto-search-icon" class="fa fa-search" aria-hidden="true"></i>
                    </div>

                    <!-- Provider status change switch -->
                    <div class="p-2">
                        <button class="btn btn-theme pull-right f-14" id="refresh"><span class="fa fa-retweet"></span> {{ __('Refresh') }} </button>
                    </div>  

                    <!-- Add Crypto Asset Button -->
                    @if (Common::has_permission(auth('admin')->user()->id, 'add_crypto_asset'))
                        <a href="{{ route('admin.tatumio.token.create') }}" class="btn btn-theme pull-right f-14 p-2"><span class="fa fa-plus"></span>&nbsp;{{ __('Add New Token') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Network list -->
    <div class="box">
        <div class="box-body box-body-customize mb-2">
            <!-- Main content -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div>
                                <div class="row">
                                    @if (Common::has_permission(auth('admin')->user()->id, 'view_crypto_asset'))
                                        @foreach ($tokens as $token)
                                                <div class="mt-20 col-xxl-3 col-md-6 col-12">
                                                   <div class="crypto-cards">
                                                    <div class="dropdown">
                                                        <span data-bs-toggle="dropdown">
                                                            <span class="crypto-dropdown">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="5" height="22"
                                                                    viewBox="0 0 5 22" fill="none">
                                                                    <circle cx="2.5" cy="2.5" r="2.5" fill="#C4C4C4" />
                                                                    <circle cx="2.5" cy="10.8333" r="2.5" fill="#C4C4C4" />
                                                                    <circle cx="2.5" cy="19.1667" r="2.5" fill="#C4C4C4" />
                                                                </svg>
                                                            </span>
                                                        </span>
                                                        <ul class="dropdown-menu pull-right crypto-dropdown-list xss f-14 p-0">
                                                                                                    
                                                            <li class="px-2 py-1">
                                                                <a href="{{ route('admin.tatumio.token.edit', encrypt($token->id))}}" class="validate-network px-2 py-1 d-block">{{ __('Edit') }}</a>
                                                            </li>

                                                        </ul>
                                                    </div>
                                                    <div class="crypto-card-header">
                                                        <span class="crypto-logo text-center">  
                                                            
                                                            @if (!empty(optional($token->currency)->logo) && fileExistCheck($token->currency->logo, 'currency'))
                                                                <img src="{{ image($token->currency->logo, 'currency') }}" alt="{{ __('Currency Logo') }}" class="img-w64">
                                                            @else
                                                                <img src='{{ image(null, 'currency') }}' class="img-w64">
                                                            @endif
                                                                                                              
                                                        </span>
                                                        <h3 class="crypto-name text-center"> {{ $token->name }} <span class="label label-{{ $token->status == 'Active' ? 'success' : 'danger' }}">{{ $token->status }}</span></h3>
                                                        <small class="text-center token-name">{{ (str_contains($token->network, 'TRX') ? 'TRC-20' : 'ERC-20') . (Str::endsWith($token->network, 'TEST') ? ' (testnet)' : '') }}</small>
                                                    </div>
                                                    <div class="crypto-information">
                                                        <div class="crypto-address">
                                                            <span class="address">{{ __('Token Address') }}</span>
                                                            <span class="address-name">{{ $token->address }}</span>
                                                        </div>                                                                                   
                                                    </div>
                                                    <div class="crypto-information">                                                         
                                                        <div class="account-bottom-border"></div>
                                                        <div class="crypto-information-aligns crypto-border">
                                                            <div class="crypto-balance">
                                                                <h4 class="text-light">{{ __('Supply') }}</h4>
                                                                <span class="text-deep">  {{ $token->value }}</span>
                                                            </div>

                                                            <div>
                                                                <h3 class="text-light text-right">{{ __('Token Symbol') }}</h3>
                                                                <p class="text-deep text-end mb-0">{{ $token->symbol }}</p>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="crypto-information">                                                         
                                                        <div class="account-bottom-border"></div>
                                                        <div class="crypto-information-aligns crypto-border">
                                                            <div class="crypto-balance">
                                                                <h4 class="text-light">{{ __('Decimals') }}</h4>
                                                                <span class="text-deep">  {{ $token->decimals }}</span>
                                                            </div>

                                                            <div >
                                                                <h4 class="text-light text-right">{{ __('Crypto Network') }}</h4>
                                                                <p class="text-deep text-end mb-0">{{ $token->network }}</p>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    @if(Common::has_permission(auth()->guard('admin')->id(), 'view_token_transactions'))
                                                        <div class="crypto-btn-container">
                                                            <a href="{{ route('admin.tatumio.token.send', ['network' => encrypt($token->network), 'tokenid' => encrypt($token->id)]) }}" class="crypto-send-btn">{{ __('Send') }}</a>
                                                            <a href="{{ route('admin.tatumio.token.receive', ['network' => encrypt($token->network), 'tokenid' => encrypt($token->id)]) }}" class="crypto-rec-btn">{{ __('Receive') }}</a>
                                                        </div>
                                                    @endif
                                                   </div>
                                                </div>
                                        @endforeach                                                        
                                    @endif
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
    <script type="text/javascript" src="{{ asset('public/dist/libraries/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        'use strict';
        var assetStatusChangeUrl = '{{ route("admin.tatumio_asset.status_change") }}';
        var validateAddressUrl = '{{ route("admin.tatumio_asset.validate_address") }}';
        var adjustRoute = '{{ route("admin.tatumio.token.adjust") }}';     
        var updateText = '{{ __("Updated") }}';
        var refreshList = '{{ __("Refresh Token List")}}';
        var refreshText = '{{ __("Refreshing will adjust all TRC-20 tokens")}}';
        var confirmText = '{{ __("Yes, refresh")}}';
        var cancelText = '{{ __("No, cancel")}}';
        var notFound = '{{ __("Not Found") }}';
        var wrongInput = '{{ __("Wrong Input") }}';
        var wentWrong = '{{ __("Something went wrong.") }}';
        var checking = '{{ __("Checking...") }}';
        var validateAddress = '{{ __("Validate Address") }}';
        var emptyAddress = '{{ __("Must specify an address to validate.") }}';
        var validateCryptoAddress= '{{ __("Validate :x crypto address") }}';
        var networkAddress = '{{ __(":x network address (eg - 3EvfKEKk13kXFJDaHXNfHbMbRXggNpojp5)") }}';
    </script>
    <script type="text/javascript" src="{{ asset('Modules/TatumIo/Resources/assets/admin/js/provider.min.js') }}"></script>
@endpush
