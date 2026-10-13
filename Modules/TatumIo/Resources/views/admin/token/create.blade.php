@extends('admin.layouts.master')
@section('title', __('Add New Token') )

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/bootstrap-toggle/css/bootstrap-toggle.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/TatumIo/Resources/assets/admin/css/tatumio_asset_setting.min.css') }}">
@endsection

@section('page_content')

    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-title padding-bottom pull-left">{{ __('Add New Token') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-info">
        @if (count($networks))
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="tabs">
                <li class="nav-item border-0"><a class="nav-link active" href="#tab_1" data-bs-toggle="tab" aria-expanded="false">{{ __('Custom Token') }}</a></li>
                <li class="nav-item border-0"><a class="nav-link" href="#tab_2" data-bs-toggle="tab" aria-expanded="false">{{ __('Stable Token') }}</a></li>
                </ul>

                <div class="tab-content">
                    <!-- Custom Token -->
                    <div class="tab-pane fade in show active" id="tab_1">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-info" id="tatumio-asset-create">                         
                                    <form action="{{ route('admin.tatumio.token.store') }}" method="POST" class="row form-horizontal" enctype="multipart/form-data" id="add-tatumio-network-form">
                                        @csrf
                                        <div class="box-body px-3">
                    
                                            <!-- Token Network -->
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="type">{{ __('Token Type') }}</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control f-14 type select2" name="network" id="type">
                                                    @foreach ($networks as $network)
                                                            <option value='{{ $network }}'>{{ (Str::startsWith($network, 'TRX') ? 'TRC-20' : 'ERC-20') . (Str::endsWith($network, 'TEST') ? ' (testnet)' : '') }}</option>                                  
                                                    @endforeach 
                                                    </select>
                                                    <div class="clearfix"></div>
                                                    <small class="form-text text-muted f-12"><strong>{{ __('*Estimated fee of') }}  500 <span id="token-fees">{{ $networks->first() }}</span>  {{ __('will be required for deploying the Token.') }}</strong></small>
                                                </div>
                                            </div>
                    
                    
                                            <!-- Name -->
                                            <div class="form-group align-items-center row" id="name-div">
                                                <label for="name" class="col-sm-3 control-label f-14 fw-bold text-sm-end">{{ __('Token Name') }}</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="name" class="form-control f-14" value="{{ old('name') }}" placeholder="{{ __('MyToken') }}" id="name" aria-required="true" aria-invalid="false" required data-value-missing="{{ __("This field is required.") }}">
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>
                            
                                            <!-- Symbol -->
                                            <div class="form-group align-items-center row" id="symbol-div">
                                                <label for="symbol" class="col-sm-3 control-label f-14 fw-bold text-sm-end">{{ __('Symbol') }}</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="symbol" class="form-control f-14" value="{{ old('symbol') }}" placeholder="MT" id="symbol" aria-required="true" aria-invalid="false" required data-value-missing="{{ __("This field is required.") }}">
                                                    <span class="text-danger">{{ $errors->first('symbol') }}</span>
                                                </div>
                                            </div>
                    
                                            <!-- Decimals -->
                                            <div class="form-group align-items-center row" id="decimals-div">
                                                <label for="decimals" class="col-sm-3 control-label f-14 fw-bold text-sm-end">{{ __('Decimals') }}</label>
                                                <div class="col-sm-6">
                                                    <input type="number" step="1" name="decimals" class="form-control f-14" value="{{ old('decimals') }}" placeholder="6" id="decimals" aria-required="true" aria-invalid="false" required data-value-missing="{{ __("This field is required.") }}">
                                                    <span class="text-danger">{{ $errors->first('decimals') }}</span>
                                                </div>
                                            </div>
                    
                                            <!-- total supply -->
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="api_key">{{ __('Token Supply') }}</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control f-14 total_supply" name="total_supply" type="number" step="1" placeholder="10000000000" value="{{ old('total_supply') }}" id="total_supply" required data-value-missing="{{ __("This field is required.") }}" >
                                                    <span class="text-danger">{{ $errors->first('total_supply') }}</span>                                                                                                          
                                                </div>
                                            </div>
                                                        
                                            <!-- Logo -->
                                            <div class="form-group row" id="logo-div">
                                                <label for="currency-logo" class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11">{{ __('Logo') }}</label>
                                                <div class="col-sm-4">
                                                    <input type="file" name="logo" class="form-control f-14 input-file-field" id="currency-logo">
                                                    <span class="text-danger">{{ $errors->first('logo') }}</span>
                                                    <div class="clearfix"></div>
                                                <small class="form-text text-muted f-12"><strong>{{ allowedImageDimension(64,64) }}</strong></small>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="pull-right setting-img">
                                                        <img src="{{ image(null, 'currency') }}" class="img-w64" id="currency-demo-logo-preview">
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Address generate -->
                                            <div class="form-group row" id="create-network-address-div">
                                                <label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="network-address">{{ __('Create Wallet') }}</label>
                                                <div class="col-sm-6">
                                                    <input type="checkbox" data-toggle="toggle" name="create_wallet" id="network-address">
                                                    <div class="clearfix"></div>
                                                    <small class="form-text text-muted f-12"><strong>{{ __('*If On, ') }}<span class="network-name"></span> {{ __('wallet will be created for all registered users.') }}</strong></small>
                                                </div>
                                            </div>
                    
                    
                                            <!-- Status -->
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="status">{{ __('Status') }}</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control f-14 status select2" name="status" id="status">
                                                        <option value='Active'>{{ __('Active') }}</option>
                                                        <option value='Inactive'>{{ __('Inactive') }}</option>
                                                    </select>
                                                    <div class="clearfix"></div>
                                                    <small class="form-text text-muted f-12"><strong>{{ __('*Updating status will update corresponding crypto currency.') }}</strong></small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 offset-md-3">
                                                    <a class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.tatumio.token') }}" >{{ __('Cancel') }}</a>
                                                    @if (Common::has_permission(auth('admin')->user()->id, 'add_crypto_asset'))
                                                        <button type="submit" class="btn btn-theme f-14" id="tatumio-settings-submit-btn">
                                                            <i class="fa fa-spinner fa-spin display-spinner"></i> <span id="tatumio-settings-submit-btn-text">{{ __('Submit') }}</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Stable Token -->
                    <div class="tab-pane" id="tab_2">

                        <div class="box-body px-2">
                    
                            <!-- Token Network -->
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-3">
                                    <label class="control-label f-14 fw-bold text-sm-end mt-11" for="type">{{ __('Token Type') }}</label>

                                    <select class="form-control f-14 type select2" name="network" id="stableType">
                                    @foreach ($networks as $key => $network)
                                            <option value='{{ $network }}' id="network-{{$key+1}}">{{ (Str::startsWith($network, 'TRX') ? 'TRC-20' : 'ERC-20') . (Str::endsWith($network, 'TEST') ? ' (testnet)' : '') }}</option>                                  
                                    @endforeach 
                                    </select>
                                    <div class="clearfix"></div>
                                    <small class="form-text text-muted f-12"><strong>{{ __('*Updating type will update corresponding crypto currency.') }}</strong></small>
                                </div>
                            </div>

                            <!-- Address -->
                            @foreach ($addresses as $key => $address )
                                <div class="row {{ $loop->first ? '' : 'd-none' }} address" id="token-{{$key+1}}">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-6">
                                        <label class="control-label f-14 fw-bold text-sm-end mt-11" for="type">{{ __('Receiving Crypto Address') }}</label>
                                        <br>
                                        <div class="clearfix"></div>
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $address }}"  alt="{{ __('QR Code') }}">
                                        <div class="clearfix"></div>
                                        <small class="form-text text-muted f-12"><strong>{{ __('Only receive Stable token (USDT, USDJ etc.) ') }} <span></span> {{ __('to this address, receiving any other coin will result in permanent loss.') }}</strong></small>
                                        <p class="word-break">{{ $address }}</p>
                                    </div>
                                </div>    
                            @endforeach                           
                        </div>             
                    </div>
                </div>
            </div>
        @else 
              {{ __('Token creation option is avaialble only for Tron network, please create tron crypto asset first ') }}   
        @endif
    </div>

    
@endsection

@push('extra_body_scripts')

@include('common.restrict_number_to_pref_decimal')
@include('common.restrict_character_decimal_point')
<script src="{{ asset('public/dist/plugins/bootstrap-toggle/js/bootstrap-toggle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"  type="text/javascript" ></script>

<script>
    'use strict';
    var defaultImageSource = '{{ url("public/user_dashboard/images/favicon.png") }}';
    var pleaseWait = '{{ __("Please Wait") }}';
    var loading = '{{ __("Loading...") }}';
    var submit = '{{ __("Submit") }}';
    var submitting = '{{ __("Submitting...") }}';
</script>

<script src="{{ asset('Modules/TatumIo/Resources/assets/admin/js/tatumio_token.min.js') }}"  type="text/javascript"></script>

@include('common.read-file-on-change')
@endpush
