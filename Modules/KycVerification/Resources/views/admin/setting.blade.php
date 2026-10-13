@extends('admin.layouts.master')
@section('title', __('Verification Settings'))

@section('page_content')

<!-- Main content -->
<div class="row">
    <div class="col-md-12">

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{ __('Verification Settings') }}</h3>
            </div>
            <form action="{{ route('admin.kyc.settings.store') }}" method="POST" class="form-horizontal" id="setting-form">
                @csrf
                <div class="box-body">

                    <!-- Is KYC Mandatory -->
                    <div class="form-group row">
                        <label class="col-sm-4 control-label f-14 fw-bold text-sm-end mt-11 require" for="kyc-mandatory">{{ __('Is KYC Mandatory') }}</label>
                        <div class="col-sm-6">
                            <select class="form-control f-14 select2 sl_common_bx" name="kyc_mandatory" id="kyc-mandatory" required data-value-missing="{{ __('This field is required.') }}">
                            {!! generateOptions(['No' => 'No', 'Yes' => 'Yes'], $result['kyc_mandatory']) !!}
                            </select>
                            <small class="form-text text-muted f-12">
                                <strong>*{{ __('If set to Yes, user will need to submit KYC.') }}</strong>
                            </small>
                            @error('kyc_mandatory')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- KYC Required For -->
                    <div class="form-group row {{ $result['kyc_mandatory'] == 'No' ? 'd-none' : '' }}" id="kyc-required-box">
                        <label class="col-sm-4 control-label f-14 fw-bold text-sm-end require" for="kyc-required-for">{{ __('KYC Required For') }}</label>
                        <div class="col-sm-6">
                            <select class="form-control f-14 select2 sl_common_bx" name="kyc_required_for" id="kyc-required-for" data-value-missing="{{ __('This field is required.') }}">
                                <option value="All" {{ $result['kyc_required_for'] == 'All' ? 'selected' : '' }}> {{ __('All') }} </option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $result['kyc_required_for'] == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted f-12">
                                <strong>*{{ __('KYC required for which user role') }}</strong>
                            </small>
                            @error('kyc_required_for')
                                <div class="help-block error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="update-button col-md-6 offset-md-4">
                            <a id="cancel_anchor" href="" class="btn btn-theme-danger f-14 me-1">{{ __('Cancel') }}</a>
                            @if (Common::has_permission(auth('admin')->user()->id, 'edit_kyc_setting'))
                                <button type="submit" class="btn btn-theme f-14" id="settings-submit-btn">
                                <i class="fa fa-spinner fa-spin d-none"></i> <span id="settings-submit-btn-text">{{ __('Update') }}</span>
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
    <script>
        'use strict';
        let submitBtnText = "{{ __('Updating...') }}";
    </script>

    <script src="{{ asset('Modules/KycVerification/Resources/assets/js/admin/settings.min.js') }}"></script>
@endpush

