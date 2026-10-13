@extends('admin.layouts.master')
@section('title', __('Fees & Limits'))

@section('head_style')
<link rel="stylesheet" type="text/css" href="{{ asset('public/admin/customs/css/custom-checkbox.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/admin/customs/css/feeslimit.min.css') }}">
@endsection

@section('page_content')
	<div class="box box-default">
		<div class="box-body ps-2">
			<div class="row">
				<div class="col-md-12">
					<div class="top-bar-title padding-bottom">{{ __('Fees Limits') }}</div>
				</div>
			</div>
		</div>
	</div>

	<div class="box">
		<div class="box-body ps-3">
			<div class="row align-items-center">
				<div class="col-md-2">
					<div class="dropdown pull-left">
						<button class="btn btn-default dropdown-toggle f-14" type="button" data-bs-toggle="dropdown">{{ __('Currency') }} :
							<span class="currencyName">{{ $currency->name }}</span><span class="caret"></span>
						</button>
						<ul class="dropdown-menu xss f-14 p-0">
							@foreach($currencyList as $currencyItem)
							<li class="listItem px-2 py-1" data-rel="{{$currencyItem->id}}" data-default="{{ $currencyItem->default }}">
								<a class="px-2 py-1 d-block" href="#">{{$currencyItem->name}}</a>
							</li>
							@endforeach
						</ul>
					</div>
				</div>
				<div class="col-md-2 offset-md-8 defaultCurrencyDiv dis-none">
                    <h4 class="form-control-static f-14 text-sm-end mb-0"><span class="label label-success f-14">{{ __('Default Currency') }}</span>
                    </h4>
                </div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			@include('admin.common.currency_menu')
		</div>

		<div class="col-md-9">
			<div class="box box-info">
				<div class="box-header with-border text-center">
					<h3 class="box-title">
						@if(isset($displayName))
							{{ __(':x Settings', ['x' => $displayName]) }}
						@elseif($list_menu == 'withdrawal')
                            {{ __('Payout Settings') }}
                        @else
                            {{ ucwords(str_replace('_', ' ', $list_menu)) }} {{ __('Settings') }}
                        @endif
					</h3>
				</div>
				<form action='{{url(config('adminPrefix').'/settings/feeslimit/update-deposit-limit')}}' class="form-horizontal" method="POST" id="deposit_limit_form">
					{!! csrf_field() !!}
					<input type="hidden" value="{{ isset($feeslimit->id) ? $feeslimit->id : '' }}" name="id" id="id">
					<input type="hidden" value="{{ $currency->id }}" name="currency_id" id="currency_id">
					<input type="hidden" value="{{ $currency->type }}" name="type" id="type">
					<input type="hidden" value="{{ $transaction_type }}" name="transaction_type" id="transaction_type">
					<input type="hidden" value="{{ $list_menu }}" name="tabText" id="tabText">

					<input type="hidden" value="{{ $currency->default }}" name="defaultCurrency" id="defaultCurrency">

					<div class="box-body">

						<!-- has_transaction -->
						<!-- has_transaction -->
						@if ($currency->default == 1)
							<div class="form-group row">
								{{-- <label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11 default_currency_label" for="has_transaction">Default Currency</label> --}}
								<label class="col-sm-3 control-label f-14 fw-bold text-sm-end default_currency_label" for="has_transaction">{{ __('Is Activated') }}</label>
								<div class="col-sm-5">
									<label class="checkbox-container">
									<input type="checkbox" class="has_transaction f-14" name="has_transaction" value="Yes" {{ isset($feeslimit->has_transaction) && $feeslimit->has_transaction == 'Yes' ? 'checked' : '' }} id="has_transaction" disabled="disabled">
									<span class="checkmark"></span>

									@if ($errors->has('has_transaction'))
										<span class="help-block">
											<strong>{{ $errors->first('has_transaction') }}</strong>
										</span>
									@endif
								</div>
								<div class="col-sm-4">
									<p><span class="default_currency_side_text f-14">{{ __('Default currency is always active') }}</span></p>
								</div>
							</div>
							<div class="clearfix"></div>
						@elseif ($currency->default == 1 && !isset($feeslimit))
							<div class="form-group row">
								<label class="col-sm-3 control-label f-14 fw-bold text-sm-end default_currency_label" for="has_transaction">{{ __('Is Activated') }}</label>
								{{-- <label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11 default_currency_label" for="has_transaction">Is Activated</label> --}}
								<div class="col-sm-5">
									<label class="checkbox-container">
									<input type="checkbox" class="has_transaction f-14" name="has_transaction" value="Yes" {{ $currency->default == 1 ? 'checked' : '' }} id="has_transaction">
									<span class="checkmark"></span>

									@if ($errors->has('has_transaction'))
										<span class="help-block">
											<strong>{{ $errors->first('has_transaction') }}</strong>
										</span>
									@endif
								</div>
								<div class="col-sm-4">
									{{-- <p><span class="default_currency_side_text">Default currency is always active</span></p> --}}
									<p><span class="default_currency_side_text"></span></p>
								</div>
							</div>
							<div class="clearfix"></div>
						@else
							<div class="form-group row">
								<label class="col-sm-3 control-label f-14 fw-bold text-sm-end default_currency_label" for="has_transaction">{{ __('Is Activated') }}</label>
								<div class="col-sm-5">
									<label class="checkbox-container">
									<input type="checkbox" class="has_transaction f-14" name="has_transaction"
									value="Yes" {{ isset($feeslimit->has_transaction) && $feeslimit->has_transaction == 'Yes' ? 'checked' : '' }} id="has_transaction">
									<span class="checkmark"></span>
									</label>
									@if ($errors->has('has_transaction'))
										<span class="help-block">
											<strong>{{ $errors->first('has_transaction') }}</strong>
										</span>
									@endif
								</div>
								<div class="col-sm-4 default_currency_side_text">
									<p><span class="default_currency_side_text"></span></p>
								</div>
							</div>
							<div class="clearfix"></div>
						@endif
						@if((isset($minAmountRequired) && $minAmountRequired) || !isset($minAmountRequired))
							<!-- Minimum Limit -->
							<div class="form-group row">
									<label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="min_limit">{{ __('Minimum Limit') }}</label>
									<div class="col-sm-5">
									<input class="form-control f-14 min_limit" name="min_limit" type="text" value="{{ isset($feeslimit->min_limit) ? number_format((float)$feeslimit->min_limit, $preference, '.', '') : number_format((float)1.00000000, $preference, '.', '') }}" id="min_limit" onkeypress="return isNumberOrDecimalPointKey(this, event);"
									oninput="restrictNumberToPrefdecimalOnInput(this)">
									<small class="form-text text-muted f-12"><strong>{{ allowedDecimalPlaceMessage($preference) }}</strong></small>

									@if ($errors->has('min_limit'))
											<span class="help-block">
												<strong>{{ $errors->first('min_limit') }}</strong>
											</span>
									@endif
									</div>
									<div class="col-sm-4">
									<p class="mb-0 f-14 mt-11">{{ __('If not set, minimum limit is :x', ['x' => number_format((float)1.00000000, $preference, '.', '')]) }}</p>
									</div>
							</div>
							<div class="clearfix"></div>
						@endif
						
						@if((isset($maxAmountRequired) && $maxAmountRequired) || !isset($minAmountRequired))
							<!-- Maximum Limit -->
							<div class="form-group row">
									<label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="max_limit">{{ __('Maximum Limit') }}</label>
									<div class="col-sm-5">
										<input class="form-control f-14 max_limit" name="max_limit" type="text"
										value="{{ isset($feeslimit->max_limit) ? number_format((float)$feeslimit->max_limit, $preference, '.', '') : '' }}"
										onkeypress="return isNumberOrDecimalPointKey(this, event);"
										id="max_limit" oninput="restrictNumberToPrefdecimalOnInput(this)">
										<small class="form-text text-muted f-12"><strong>{{ allowedDecimalPlaceMessage($preference) }}</strong></small>
										@if ($errors->has('max_limit'))
											<span class="help-block">
												<strong>{{ $errors->first('max_limit') }}</strong>
											</span>
										@endif
									</div>
									<div class="col-sm-4">
									<p class="mb-0 f-14 mt-11">{{ __('If not set, maximum limit is infinity') }}</p>
									</div>
							</div>
							<div class="clearfix"></div>
						@endif

						<!-- Charge Percentage -->
						<div class="form-group row">
							<label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="charge_percentage">{{ __('Charge Percentage') }}</label>
							<div class="col-sm-5">
								<input class="form-control f-14 charge_percentage" name="charge_percentage" type="text"
								value="{{ isset($feeslimit->charge_percentage) ?  number_format((float)$feeslimit->charge_percentage, $preference, '.', '') :
								number_format((float)0.00000000, $preference, '.', '') }}" id="charge_percentage"
								onkeypress="return isNumberOrDecimalPointKey(this, event);"
								oninput="restrictNumberToPrefdecimalOnInput(this)">
								<small class="form-text text-muted f-12"><strong>{{ allowedDecimalPlaceMessage($preference) }}</strong></small>
								@if ($errors->has('charge_percentage'))
									<span class="help-block">
										<strong>{{ $errors->first('charge_percentage') }}</strong>
									</span>
								@endif
							</div>
							<div class="col-sm-4">
								<p class="mb-0 f-14 mt-11">{{ __('If not set, charge percentage is :x', ['x' => number_format((float)0.00000000, $preference, '.', '')] ) }} </p>
							</div>
						</div>
						<div class="clearfix"></div>

						<!-- Charge Fixed -->
						<div class="form-group row">
							<label class="col-sm-3 control-label f-14 fw-bold text-sm-end mt-11" for="charge_fixed">{{ __('Charge Fixed') }}</label>
							<div class="col-sm-5">
								<input class="form-control f-14 charge_fixed" name="charge_fixed" type="text"
								value="{{ isset($feeslimit->charge_fixed) ? number_format((float)$feeslimit->charge_fixed, $preference, '.', '') :
								number_format((float)0.00000000, $preference, '.', '') }}" id="charge_fixed" onkeypress="return isNumberOrDecimalPointKey(this, event);"
								oninput="restrictNumberToPrefdecimalOnInput(this)">
								<small class="form-text text-muted f-12"><strong>{{ allowedDecimalPlaceMessage($preference) }}</strong></small>
								@if ($errors->has('charge_fixed'))
									<span class="help-block">
										<strong>{{ $errors->first('charge_fixed') }}</strong>
									</span>
								@endif
							</div>
							<div class="col-sm-4">
								<p class="mb-0 f-14 mt-11">{{ __('If not set, charge fixed is :x', ['x' => number_format((float)0.00000000, $preference, '.', '') ] ) }}</p>
							</div>
						</div>
						<div class="clearfix"></div>

                        <div class="mt-3 row">
                            <div class="col-md-6 offset-md-3">
                                <a href="{{  url(config('adminPrefix')."/settings/currency") }}" class="btn btn-theme-danger f-14 me-1">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn btn-theme f-14" id="deposit_limit_update">
                                    <i class="fa fa-spinner fa-spin d-none"></i> <span id="deposit_limit_update_text">{{ __('Update') }}</span>
                                </button>
                            </div>
                        </div>
					</div>


				</form>
			</div>
		</div>
	</div>
@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/plugins/jquery-validation/dist/jquery.validate.min.js') }}" type="text/javascript"></script>

@include('common.restrict_number_to_pref_decimal')
@include('common.restrict_character_decimal_point')

<script>
	'use strict';
	let decimalFormat = ($('#type').val() == 'fiat') ? "<?php echo preference('decimal_format_amount', 2); ?>" : "<?php echo preference('decimal_format_amount_crypto', 8); ?>";
	let depositLimitUpdateText = "{{ __('Updating...') }}";
	let failedText = "{{ __('Failed') }}";
	let isActivated = "{{ __('Is Activated') }}";
	let defaultCurrencyActive = "{{ __('Default currency is always active') }}";
	let csrfToken = "{{ csrf_token() }}";


</script>

<script src="{{ asset('public/admin/customs/js/feeslimit/single-fees-limit.min.js') }}" type="text/javascript"></script>

@endpush
