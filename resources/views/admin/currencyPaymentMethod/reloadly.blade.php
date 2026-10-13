@php
    $methodData = isset($currencyPaymentMethod->method_data)
        ? json_decode($currencyPaymentMethod->method_data)
        : null;
@endphp

<!-- reloadly - Client ID -->
<div class="form-group row">
	<label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="reloadly_client_id">{{ __('Client ID') }}</label>
	<div class="col-sm-6">
		<input class="form-control f-14" name="reloadly[client_id]" type="text" placeholder="{{ __('Reloadly Client ID') }}"
		value="{{ $methodData->client_id ?? '' }}" id="reloadly_client_id">

		@if ($errors->has('reloadly[client_id]'))
			<span class="help-block">
				<strong>{{ $errors->first('reloadly[client_id]') }}</strong>
			</span>
		@endif
	</div>
</div>
<div class="clearfix"></div>

<!-- reloadly - Client Secret -->
<div class="form-group row">
	<label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="reloadly_client_secret">{{ __('Client Secret') }}</label>
	<div class="col-sm-6">
		<input class="form-control f-14" name="reloadly[client_secret]" type="text" placeholder="{{ __('Reloadly Client Secret') }}"
		value="{{ $methodData->client_secret ?? '' }}" id="reloadly_client_secret">
		@if ($errors->has('reloadly[client_secret]'))
			<span class="help-block">
				<strong>{{ $errors->first('reloadly[client_secret]') }}</strong>
			</span>
		@endif
	</div>
</div>

<!-- reloadly - Mode -->
<div class="form-group row">
	<label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="reloadly_mode">{{ __('Mode') }}</label>
	<div class="col-sm-6">
		<select class="form-control f-14" name="reloadly[mode]" id="reloadly_mode">
			<option value="">{{ __('Select Mode') }}</option>
			<option value='sandbox' {{ $methodData && $methodData->mode == 'sandbox' ? 'selected':"" }} >{{ __('sandbox') }}</option>
			<option value='live' {{ $methodData && $methodData->mode == 'live' ? 'selected':"" }} >{{ __('live') }}</option>
		</select>
	</div>
</div>
<div class="clearfix"></div>
