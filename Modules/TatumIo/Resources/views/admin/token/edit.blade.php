@extends('admin.layouts.master')
@section('title', __('Update Crypto Token') )

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/bootstrap-toggle/css/bootstrap-toggle.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/TatumIo/Resources/assets/admin/css/tatumio_asset_setting.min.css') }}">
@endsection

@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info" id="tatumio-asset-edit">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('Update Crypto Token') }}</h3>
                </div>

                <form action="{{ route('admin.tatumio.token.update', encrypt($cryptoToken->id)) }}" method="POST" class="form-horizontal" enctype="multipart/form-data" id="edit-tatumio-network-form">
                    @csrf

                    <input type="hidden" name="id" value="{{ encrypt($cryptoToken->id) }}">

                    <div class="box-body">
                        <!-- Name -->
                        <div class="form-group row align-items-center" id="name-div">
                            <label for="name" class="col-sm-3 control-label f-14 fw-bold text-sm-end">{{ __('Token Name') }}</label>
                            <div class="col-sm-6">
                                <input type="text" name="name" class="form-control f-14" value="{{ $cryptoToken->name }}" placeholder="{{ __('eg - Bitcoin or Litecoin') }}" id="name" aria-required="true" aria-invalid="false" readonly>
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>

                        <!-- Network  -->
                        <div class="form-group row align-items-center" id="crypto-networks-div">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end" for="network">{{ __('Network') }}</label>
                            <div class="col-sm-6">
                                <input type="text" value="{{ $cryptoToken->network }}" name="network" class="form-control f-14" placeholder="{{ __('Enter network code (eg - BTC)') }}" id="network" readonly>
                                <span class="text-danger">{{ $errors->first('network') }}</span>
                                <span class="network-exist-error"></span>
                            </div>
                        </div>

                        <!-- Symbol -->
                        <div class="form-group row align-items-center" id="symbol-div">
                            <label for="symbol" class="col-sm-3 control-label f-14 fw-bold text-sm-end">{{ __('Symbol') }}</label>
                            <div class="col-sm-6">
                                <input type="text" name="symbol" class="form-control f-14" value="{{ $cryptoToken->symbol }}" placeholder="{{ __('Symbol (ex - ₿)') }}" id="symbol" aria-required="true" aria-invalid="false" readonly>
                                <span class="text-danger">{{ $errors->first('symbol') }}</span>
                            </div>
                        </div>

                        <!-- Decimals -->
                        <div class="form-group row align-items-center" id="symbol-div">
                            <label for="symbol" class="col-sm-3 control-label f-14 fw-bold text-sm-end">{{ __('Decimals') }}</label>
                            <div class="col-sm-6">
                                <input type="text" name="decimals" class="form-control f-14" value="{{ $cryptoToken->decimals }}" placeholder="{{ __('Symbol (ex - ₿)') }}" id="symbol" aria-required="true" aria-invalid="false" readonly>
                                <span class="text-danger">{{ $errors->first('symbol') }}</span>
                            </div>
                        </div>

                         <!-- Token Supply -->
                        <div class="form-group row align-items-center" id="symbol-div">
                            <label for="symbol" class="col-sm-3 control-label f-14 fw-bold text-sm-end">{{ __('Token Supply') }}</label>
                            <div class="col-sm-6">
                                <input type="text" name="totalSupply" class="form-control f-14" value="{{ $cryptoToken->value }}" placeholder="{{ __('Symbol (ex - ₿)') }}" id="symbol" aria-required="true" aria-invalid="false" readonly>
                                <span class="text-danger">{{ $errors->first('symbol') }}</span>
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
                                    @if (fileExistCheck($cryptoToken->currency?->logo, 'currency'))
                                        <img src="{{ image($cryptoToken->currency?->logo, 'currency') }}" alt="{{ __('Currency Logo') }}" class="img-w64" id="currency-demo-logo-preview">
                                    @else
                                        <img src='{{ image(null, 'currency') }}' class="img-w64" id="currency-demo-logo-preview">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Address generate -->
                        <div class="form-group row" id="create-network-address-div">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="inputEmail3">{{ __('Create Wallet') }}</label>
                            <div class="col-sm-6">
                                <input type="checkbox" data-toggle="toggle" name="create_wallet" id="create_address">
                                <div class="clearfix"></div>
                                <small class="form-text text-muted f-12"><strong>{{ __('*If On, ') }}<span class="network-name"></span> {{ __('wallet will be created for all registered users.') }}</strong></small>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group row">
                            <label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="status">{{ __('Status') }}</label>
                            <div class="col-sm-6">
                                <select class="form-control f-14 status select2" name="status" id="status">
                                    <option value='Active' {{ $cryptoToken->status == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value='Inactive' {{ $cryptoToken->status == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                                <div class="clearfix"></div>
                                <small class="form-text text-muted f-12"><strong>{{ __('*Updating status will update corresponding crypto currency.') }}</strong></small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <a class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.tatumio.token') }}" >{{ __('Cancel') }}</a>
                                @if (Common::has_permission(auth('admin')->user()->id, 'edit_crypto_token'))
                                    <button type="submit" class="btn btn-theme f-14" id="tatumio-settings-edit-btn">
                                        <i class="fa fa-spinner fa-spin display-spinner"></i> <span id="tatumio-settings-edit-btn-text">{{ __('Update') }}</span>
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
<script src="{{ asset('public/dist/plugins/bootstrap-toggle/js/bootstrap-toggle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/debounce/jquery.ba-throttle-debounce.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/dist/libraries/sweetalert/sweetalert-unpkg.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"  type="text/javascript" ></script>

<script>
    'use script';
    var defaultImageSource = "{{ image(null, 'currency') }}";
    var pleaseWait = '{{ __("Please Wait") }}';
    var loading = '{{ __("Loading...") }}';
    var merchantAddress = '{{ __("Merchant address does not belong to this network.") }}';
    var update = '{{ __("Update") }}';
    var updating = '{{ __("Updating...") }}';
</script>

<script src="{{ asset('Modules/TatumIo/Resources/assets/admin/js/tatumio_asset_setting.min.js') }}"  type="text/javascript" ></script>
@include('common.read-file-on-change')

@endpush
