@extends('admin.layouts.master')

@section('title', __('Edit KYC Provider'))

@section('page_content')
    <div class="row">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{ __('Edit KYC Provider') }}</h3>
            </div>

            <!-- form start -->
            <form method="POST" action="{{ route('admin.kyc.providers.update', $provider->id) }}" class="form-horizontal" id="provider-form">
                @csrf
                @method('PUT')

                <div class="box-body">
                    <!-- Name -->
                    <div class="form-group row">
                        <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-sm-end require" for="name">{{ __('Name') }}</label>
                        <div class="col-sm-6">
                            <div>
                                <input type="text" name="name" class="form-control f-14" value="{{ $provider->name }}" placeholder="{{ __('Edit name') }}" id="name" required data-value-missing="{{ __('This field is required') }}">
                            </div>
                            <input type="hidden" name="alias" value="{{ $provider->alias }}">
                            <small class="form-text text-muted f-12">
                                <strong>*{{ __('This name will be visible on the verification list as provider.') }}</strong>
                            </small>
                            @error('name')
                                <span class="help-block">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Default -->
                    <div class="form-group row">
                        <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-sm-end require" for="default">{{ __('Default') }}</label>
                        <div class="col-sm-6">
                            <div>
                                @if ($provider->is_default == 'Yes')
                                    <p class="f-14 mb-0 mt-10"><span class="label label-success">{{$provider->is_default}}</span></p>
                                    <input type="hidden" value="{{ $provider->is_default }}" name="is_default">
                                @else
                                    <select class="form-control f-14 select2 sl_common_bx" name="is_default" id="default" required data-value-missing="{{ __('This field is required.') }}">
                                        {!! generateOptions(['No' => 'No', 'Yes' => 'Yes'], $provider->is_default) !!}
                                    </select>
                                @endif
                            </div>
                            <small class="form-text text-muted f-12">
                                <strong>*{{ __('If set to Yes, this will be the default verification provider for all users.') }}</strong>
                            </small>
                            @error('name')
                                <span class="help-block">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <a class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.kyc.providers.index') }}">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-theme f-14" id="provider-submit-btn">
                                <i class="fa fa-spinner fa-spin d-none"></i> <span id="provider-submit-btn-text">{{ __('Update') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
    <script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        'use strict';
        var submitButtonText = "{{ __('Updating...') }}";
    </script>

    <script src="{{ asset('Modules/KycVerification/Resources/assets/js/admin/provider.min.js') }}"></script>
@endpush
