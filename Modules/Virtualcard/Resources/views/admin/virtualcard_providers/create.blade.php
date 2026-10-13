@extends('admin.layouts.master')

@section('title', __('New Provider'))

@section('page_content')
    <div class="row" id="virtualCardProvider">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('Create New Provider') }}</h3>
                </div>
                <form method="post" action="{{ route('admin.virtualcard_provider.store') }}" class="form-horizontal" id="virtualCardProviderForm">
                    @csrf
                    <div class="box-body">

                        <!-- Name -->
                        <div class="form-group row">
                            <label class="col-sm-3 control-label require mt-11 text-sm-end f-14 fw-bold" for="name">{{ __('Name') }}</label>
                            <div class="col-sm-6">
                                <input 
                                    type="text" 
                                    name="name" 
                                    class="form-control f-14" 
                                    id="name" 
                                    value="{{ old('name') }}" 
                                    placeholder="{{ __('Enter :x', ['x' => __('name')]) }}" 
                                    required data-value-missing="{{ __('This field is required.') }}"
                                    maxlength="50"
                                >
                                @if ($errors->has('name'))
                                    <span class="error">
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- currency -->
                        <div class="form-group row">
                            <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="currency_id">{{ __('Currency') }}</label>
                            <div class="col-sm-6 col-md-6">
                                <select 
                                    class="form-control select2 f-14 sl_common_bx" 
                                    name="currency_id[]" 
                                    id="currency_id" 
                                    required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" 
                                    multiple
                                >
                                    @foreach ($activeCurrency as $currency)
                                        <option {{ $currency['default'] == 1 ? 'selected' : '' }} value="{{ $currency['id'] }}">{{ $currency['code'] }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('currency_id'))
                                    <span class="error">{{ $errors->first('currency_id') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group row">
                            <label class="col-sm-3 mt-11 control-label require text-sm-end f-14 fw-bold" for="status">{{ __('Status') }}</label>
                            <div class="col-sm-6 col-md-6">
                                <select 
                                    class="select2 f-14" 
                                    name="status" 
                                    id="status" 
                                    required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                >
                                    <option value='Active'>{{ __('Active') }}</option>
                                    <option value='Inactive'>{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-6 offset-md-3">
                                <a class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.virtualcard_provider.index') }}" id="packageCreateCancel">{{ __('Cancel') }}</a>
                                @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_card_provider'))
                                <button type="submit" class="btn btn-theme f-14 float-right" id="virtualCardProviderSubmitBtn">
                                    <i class="fa fa-spinner fa-spin d-none"></i>
                                    <span id="virtualCardProviderSubmitBtnText">{{ __('Submit') }}</span>
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

    @include('common.restrict_number_to_pref_decimal')
    @include('common.restrict_character_decimal_point')
    <script>
        "use strict";
        var submitButtonText = "{{ __('Submitting...') }}";
    </script>

    <script type="text/javascript" src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/admin/virtualcard-provider.min.js') }}"></script>

@endpush
