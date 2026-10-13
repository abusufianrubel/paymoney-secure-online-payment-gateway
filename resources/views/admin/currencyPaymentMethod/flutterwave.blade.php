<!-- Flutterwave - Public Key -->
<div class="form-group row">
    <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="flutterwave_public_key">{{ __('Public Key') }}</label>
    <div class="col-sm-6">

        <input class="form-control f-14" name="flutterwave[public_key]" type="text" placeholder="{{ __('Flutterwave Public Key') }}"
        value="{{ isset($currencyPaymentMethod->method_data) ? json_decode($currencyPaymentMethod->method_data)->public_key : '' }}" id="flutterwave_public_key">

        @if ($errors->has('flutterwave[public_key]'))
            <span class="help-block">
                <strong>{{ $errors->first('flutterwave[public_key]') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="clearfix"></div>

<!-- Flutterwave - Secret Key -->
<div class="form-group row">
    <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="flutterwave_secret_key">{{ __('Secret Key') }}</label>
    <div class="col-sm-6">

        <input class="form-control f-14" name="flutterwave[secret_key]" type="text" placeholder="{{ __('Flutterwave Secret Key') }}"
        value="{{ isset($currencyPaymentMethod->method_data) ? json_decode($currencyPaymentMethod->method_data)->secret_key : '' }}" id="flutterwave_secret_key">

        @if ($errors->has('flutterwave[secret_key]'))
            <span class="help-block">
                <strong>{{ $errors->first('flutterwave[secret_key]') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="clearfix"></div>

<!-- Flutterwave - Secret Hash -->
<div class="form-group row">
    <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="flutterwave_secret_hash">{{ __('Webhook Secret Hash') }}</label>
    <div class="col-sm-6">

        <input class="form-control f-14" name="flutterwave[secret_hash]" type="text" placeholder="{{ __('Flutterwave Webhook Secret Hash') }}"
        value="{{ isset($currencyPaymentMethod->method_data) ? json_decode($currencyPaymentMethod->method_data)->secret_hash : '' }}" id="flutterwave_secret_hash">

        @if ($errors->has('flutterwave[secret_hash]'))
            <span class="help-block">
                <strong>{{ $errors->first('flutterwave[secret_hash]') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="clearfix"></div>

<div class="form-group row">
    <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end mt-11" for="webhook_url">{{ __('Webhook URL') }}</label>
    <div class="col-sm-6">
        <div class="d-flex justify-content-between">
            <input name="webhook_url" class="form-control f-14 flutterwave_ipn_url" type="text" readonly value="{{ url('gateway/process-payment/flutterwave') }}" id="webhook_url">
            <button type="button" class="btn btn-md btn-primary coin-copy f-14" id="flutterwave_copy_button">
                {{ __('Copy') }}
            </button>
        </div>
        <small class="text-color f-12"><strong>{!! __('Copy the above url and set it in :x field.', ['x' => '<a href="'. image(null, 'flutterwave_webhook') .'">'. __('Flutterwave Webhook URL') .'</a>']) !!}</strong></small>
    </div>
</div>
<div class="clearfix"></div>